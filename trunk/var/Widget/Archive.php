<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Posts.php 200 2008-05-21 06:33:20Z magike.net $
 */

/**
 * 内容的文章基类
 * 定义的css类
 * p.more:阅读全文链接所属段落
 *
 * TODO 增加feed支持
 * @package Widget
 */
class Widget_Archive extends Widget_Abstract_Contents
{
    /**
     * 调用的风格文件
     * 
     * @access private
     * @var string
     */
    private $_themeFile;
    
    /**
     * 分页计算对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 所有文章个数
     * 
     * @access private
     * @var integer
     */
    private $_total = false;
    
    /**
     * 标记是否为从外部调用
     * 
     * @access private
     * @var boolean
     */
    private $_invokeFromOutside = false;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 生成分页的内容
     * 
     * @access private
     * @var array
     */
    private $_pageRow;
    
    /**
     * 聚合器对象
     * 
     * @access private
     * @var Typecho_Feed_Writer
     */
    private $_feed;
    
    /**
     * RSS 2.0聚合地址
     * 
     * @access private
     * @var string
     */
    private $_feedUrl;
    
    /**
     * RSS 1.0聚合地址
     * 
     * @access private
     * @var string
     */
    private $_feedRssUrl;
    
    /**
     * ATOM 聚合地址
     * 
     * @access private
     * @var string
     */
    private $_feedAtomUrl;
    
    /**
     * 本页关键字
     * 
     * @access private
     * @var string
     */
    private $_keywords;
    
    /**
     * 本页描述
     * 
     * @access private
     * @var string
     */
    private $_description;
    
    /**
     * 聚合类型
     * 
     * @access private
     * @var string
     */
    private $_feedType;
    
    /**
     * 当前feed地址
     * 
     * @access private
     * @var string
     */
    private $_currentFeedUrl;
    
    /**
     * 归档标题
     * 
     * @access private
     * @var array
     */
    private $_archiveTitle = array();
    
    /**
     * 归档类型
     * 
     * @access private
     * @var string
     */
    private $_archiveType = 'index';
    
    /**
     * 是否为单一归档
     * 
     * @access private
     * @var string
     */
    private $_archiveSingle = false;
    
    /**
     * 归档缩略名
     * 
     * @access private
     * @var string
     */
    private $_archiveSlug;
    
    /**
     * 自定义归档
     * 
     * @access private
     * @var boolean
     */
    private $_archiveCustom = false;
    
    /**
     * 设置分页对象
     * 
     * @access private
     * @var Typecho_Widget_Helper_PageNavigator
     */
    private $_pageNav;
    
    
    /**
     * 构造函数,初始化组件
     * 
     * @access public
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     * @return void
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        
        $this->parameter->setDefault(array('pageSize' => $this->options->pageSize,
        'type' => NULL));
        
        /** 用于判断是路由调用还是外部调用 */
        if (NULL == $this->parameter->type) {
            $this->parameter->type = Typecho_Router::$current;
        } else {
            $this->_invokeFromOutside = true;
        }

        /** 处理feed模式 **/
        if ('feed' == $this->parameter->type) {
        
            $this->_currentFeedUrl = '';
            
            /** 判断聚合类型 */
            switch (true) {
                case 0 === strpos($this->request->feed, '/rss/') || '/rss' == $this->request->feed:
                    /** 如果是RSS1标准 */
                    $this->request->feed = substr($this->request->feed, 4);
                    $this->_feedType = Typecho_Feed::RSS1;
                    $this->_currentFeedUrl = $this->options->feedRssUrl;
                    $this->response->setContentType('application/rdf+xml');
                    break;
                case 0 === strpos($this->request->feed, '/atom/') || '/atom' == $this->request->feed:
                    /** 如果是ATOM标准 */
                    $this->request->feed = substr($this->request->feed, 5);
                    $this->_feedType = Typecho_Feed::ATOM1;
                    $this->_currentFeedUrl = $this->options->feedAtomUrl;
                    $this->response->setContentType('application/atom+xml');
                    break;
                default:
                    $this->_feedType = Typecho_Feed::RSS2;
                    $this->_currentFeedUrl = $this->options->feedUrl;
                    $this->response->setContentType('application/rss+xml');
                    break;
            }
            
            $feedQuery = $this->request->feed;
            $matched = Typecho_Router::match($this->request->feed, $params);
            $this->parameter->type = Typecho_Router::$current;
            $this->request->setParams($params);
        
            if ('/comments/' == $feedQuery || '/comments' == $feedQuery) {
                /** 专为feed使用的hack */
                $this->parameter->type = 'comments';
            } else if (!$matched || 'feed' == $this->parameter->type) {
                throw new Typecho_Widget_Exception(_t('聚合页不存在'), 404);
            }
            
            /** 初始化聚合器 */
            $this->setFeed(new Typecho_Feed(Typecho_Common::VERSION, $this->_feedType, $this->options->charset, _t('zh-CN')));
            
            /** 默认输出10则文章 **/
            $this->parameter->pageSize = 10;
        }
    }
    
    /**
     * 设置分页对象
     * @param $pageRow
     * @return void
     */
    public function setPageRow(array $pageRow)
    {
        $this->_pageRow = $pageRow;
    }
    
	/**
	 * @param $_archiveCustom the $_archiveCustom to set
	 */
	public function setArchiveCustom($archiveCustom)
	{
		$this->_archiveCustom = $archiveCustom;
	}

	/**
	 * @param $_archiveSlug the $_archiveSlug to set
	 */
	public function setArchiveSlug($archiveSlug)
	{
		$this->_archiveSlug = $archiveSlug;
	}

	/**
	 * @param $_archiveSingle the $_archiveSingle to set
	 */
	public function setArchiveSingle($archiveSingle)
	{
		$this->_archiveSingle = $archiveSingle;
	}

	/**
	 * @param $_archiveType the $_archiveType to set
	 */
	public function setArchiveType($archiveType)
	{
		$this->_archiveType = $archiveType;
	}

	/**
	 * @param $_archiveTitle the $_archiveTitle to set
	 */
	public function setArchiveTitle(array $archiveTitle)
	{
		$this->_archiveTitle = $archiveTitle;
	}
	
	/**
	 * 增加标题
	 * @param string $archiveTitle 标题
	 * @return void
	 */
	public function addArchiveTitle($archiveTitle)
	{
	    $current = $this->getArchiveTitle();
	    $current[] = $archiveTitle;
	    $this->setArchiveTitle($current);
	}

	/**
	 * @param $_feedType the $_feedType to set
	 */
	public function setFeedType($feedType)
	{
		$this->_feedType = $feedType;
	}

	/**
	 * @param $_description the $_description to set
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
	}

	/**
	 * @param $_keywords the $_keywords to set
	 */
	public function setKeywords($keywords)
	{
		$this->_keywords = $keywords;
	}

	/**
	 * @param $_feedAtomUrl the $_feedAtomUrl to set
	 */
	public function setFeedAtomUrl($feedAtomUrl)
	{
		$this->_feedAtomUrl = $feedAtomUrl;
	}

	/**
	 * @param $_feedRssUrl the $_feedRssUrl to set
	 */
	public function setFeedRssUrl($feedRssUrl)
	{
		$this->_feedRssUrl = $feedRssUrl;
	}

	/**
	 * @param $_feedUrl the $_feedUrl to set
	 */
	public function setFeedUrl($feedUrl)
	{
		$this->_feedUrl = $feedUrl;
	}

	/**
	 * @param $_feed the $_feed to set
	 */
	public function setFeed($feed)
	{
		$this->_feed = $feed;
	}

	/**
	 * @param $_countSql the $_countSql to set
	 */
	public function setCountSql(Typecho_Db_Query $countSql)
	{
		$this->_countSql = $countSql;
	}

	/**
	 * @param $_themeFile the $_themeFile to set
	 */
	public function setThemeFile($themeFile)
	{
		$this->_themeFile = $themeFile;
	}
	
	/**
	 * 获取分页对象
	 * @return array
	 */
	public function getPageRow()
	{
	    return $this->_pageRow;
	}

	/**
	 * @return the $_archiveCustom
	 */
	public function getArchiveCustom()
	{
		return $this->_archiveCustom;
	}

	/**
	 * @return the $_archiveSlug
	 */
	public function getArchiveSlug()
	{
		return $this->_archiveSlug;
	}

	/**
	 * @return the $_archiveSingle
	 */
	public function getArchiveSingle()
	{
		return $this->_archiveSingle;
	}

	/**
	 * @return the $_archiveType
	 */
	public function getArchiveType()
	{
		return $this->_archiveType;
	}

	/**
	 * @return the $_archiveTitle
	 */
	public function getArchiveTitle()
	{
		return $this->_archiveTitle;
	}

	/**
	 * @return the $_feedType
	 */
	public function getFeedType()
	{
		return $this->_feedType;
	}

	/**
	 * @return the $_description
	 */
	public function getDescription()
	{
		return $this->_description;
	}

	/**
	 * @return the $_keywords
	 */
	public function getKeywords()
	{
		return $this->_keywords;
	}

	/**
	 * @return the $_feedAtomUrl
	 */
	public function getFeedAtomUrl()
	{
		return $this->_feedAtomUrl;
	}

	/**
	 * @return the $_feedRssUrl
	 */
	public function getFeedRssUrl()
	{
		return $this->_feedRssUrl;
	}

	/**
	 * @return the $_feedUrl
	 */
	public function getFeedUrl()
	{
		return $this->_feedUrl;
	}

	/**
	 * @return the $_feed
	 */
	public function getFeed()
	{
		return $this->_feed;
	}

	/**
	 * @return the $_countSql
	 */
	public function getCountSql()
	{
		return $this->_countSql;
	}

	/**
	 * @return the $_themeFile
	 */
	public function getThemeFile()
	{
		return $this->_themeFile;
	}

    
    /**
     * 处理index
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function indexHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        $select->where('table.contents.type = ?', 'post');
    }
    
    /**
     * 404页面处理
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function error404Handle(Typecho_Db_Query $select, &$hasPushed)
    {
        /** 设置header */
        $this->response->setStatus(404);
    
        /** 设置标题 */
        $this->_archiveTitle[] = _t('页面不存在');
        
        /** 设置归档类型 */
        $this->_archiveType = 404;
        
        /** 设置归档缩略名 */
        $this->_archiveSlug = 404;
        
        /** 设置归档缩略名 */
        $this->_themeFile = '404.php';
        
        /** 设置单一归档类型 */
        $this->_archiveSingle = true;
        
        $hasPushed = true;
        
        /** 插件接口 */
        $this->pluginHandle()->error404Handle($select, $this);
    }
    
    /**
     * 独立页处理
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function singleHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        if ('comment_page' == $this->parameter->type) {
            $params = array();
            $matched = Typecho_Router::match($this->request->permalink, $params);
            $this->parameter->type = Typecho_Router::$current;
            $this->request->setParams($params);
        }
    
        /** 匹配类型 */
        $select->where('table.contents.type = ?', $this->parameter->type);
        
        /** 如果是单篇文章或独立页面 */
        if (isset($this->request->cid)) {
            $select->where('table.contents.cid = ?', $this->request->filter('int')->cid);
        }
        
        /** 匹配缩略名 */
        if (isset($this->request->slug)) {
            $select->where('table.contents.slug = ?', $this->request->slug);
        }
        
        /** 匹配时间 */
        if (isset($this->request->year)) {
            $year = $this->request->filter('int')->year;
            
            $fromMonth = 1;
            $toMonth = 12;

            if (isset($this->request->month)) {
                $fromMonth = $this->request->filter('int')->month;
                $toMonth = $fromMonth;
                
                $fromDay = 1;
                $toDay = date('t', mktime(0, 0, 0, $toMonth, 1, $year));
                
                if (isset($this->request->day)) {
                    $fromDay = $this->request->filter('int')->day;
                    $toDay = $fromDay;
                }
            }
            
            /** 获取起始GMT时间的unix时间戳 */
            $from = mktime(0, 0, 0, $fromMonth, $fromDay, $year) - $this->options->timezone + $this->options->serverTimezone;
            $to = mktime(23, 59, 59, $toMonth, $toDay, $year) - $this->options->timezone + $this->options->serverTimezone;
            $select->where('table.contents.created > ? AND table.contents.created < ?', $from, $to);
        }

        /** 保存密码至cookie */
        if ($this->request->isPost() && isset($this->request->protectPassword)) {
            Typecho_Cookie::set('protectPassword', $this->request->protectPassword, 0, $this->options->siteUrl);
        }
        
        /** 匹配类型 */
        $select->limit(1);
        $this->db->fetchRow($select, array($this, 'push'));
        
        if (!$this->have() || (isset($this->request->category) && $this->category != $this->request->category)) {
            /** 对没有索引情况下的判断 */
            throw new Typecho_Widget_Exception(_t('请求的地址不存在'), 404);
        }

        /** 设置关键词 */
        $this->_keywords = implode(',', Typecho_Common::arrayFlatten($this->tags, 'name'));
        
        /** 设置描述 */
        $this->_description = $this->description;
        
        /** 设置模板 */
        if ($this->template) {
            /** 应用自定义模板 */
            $this->_themeFile = $this->template;
        }
        
        /** 设置头部feed */
        /** RSS 2.0 */
        
        //对自定义首页使用全局变量
        if (!$this->_archiveCustom) {
            $this->_feedUrl = $this->feedUrl;
            
            /** RSS 1.0 */
            $this->_feedRssUrl = $this->feedRssUrl;
            
            /** ATOM 1.0 */
            $this->_feedAtomUrl = $this->feedAtomUrl;
            
            /** 设置标题 */
            $this->_archiveTitle[] = $this->title;
        }
        
        /** 设置归档类型 */
        $this->_archiveType = $this->type;
        
        /** 设置归档缩略名 */
        $this->_archiveSlug = ('post' == $this->type || 'attachment' == $this->type) ? $this->cid : $this->slug;
        
        /** 设置单一归档类型 */
        $this->_archiveSingle = true;
        
        /** 设置403头 */
        if ($this->hidden) {
            $this->response->setStatus(403);
        }

        $hasPushed = true;
        
        /** 插件接口 */
        $this->pluginHandle()->singleHandle($select, $this);
    }
    
    /**
     * 处理分类
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function categoryHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        /** 如果是分类 */
        $categorySelect = $this->db->select()
        ->from('table.metas')
        ->where('type = ?', 'category')
        ->limit(1);
        
        if (isset($this->request->mid)) {
            $categorySelect->where('mid = ?', $this->request->filter('int')->mid);
        }
        
        if (isset($this->request->slug)) {
            $categorySelect->where('slug = ?', $this->request->slug);
        }
        
        $category = $this->db->fetchRow($categorySelect,
        array($this->widget('Widget_Abstract_Metas'), 'filter'));
        
        if (!$category) {
            throw new Typecho_Widget_Exception(_t('分类不存在'), 404);
        }
    
        /** fix sql92 by 70 */
        $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
        ->where('table.relationships.mid = ?', $category['mid'])
        ->where('table.contents.type = ?', 'post');
        
        /** 设置分页 */
        $this->_pageRow = $category;
        
        /** 设置关键词 */
        $this->_keywords = $category['name'];
        
        /** 设置描述 */
        $this->_description = $category['description'];
        
        /** 设置头部feed */
        /** RSS 2.0 */
        $this->_feedUrl = $category['feedUrl'];
        
        /** RSS 1.0 */
        $this->_feedRssUrl = $category['feedRssUrl'];
        
        /** ATOM 1.0 */
        $this->_feedAtomUrl = $category['feedAtomUrl'];
        
        /** 设置标题 */
        $this->_archiveTitle[] = $category['name'];
        
        /** 设置归档类型 */
        $this->_archiveType = 'category';
        
        /** 设置归档缩略名 */
        $this->_archiveSlug = $category['slug'];
        
        /** 插件接口 */
        $this->pluginHandle()->categoryHandle($select, $this);
    }
    
    /**
     * 处理标签
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function tagHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        $tagSelect = $this->db->select()->from('table.metas')
        ->where('type = ?', 'tag')->limit(1);
        
        if (isset($this->request->mid)) {
            $tagSelect->where('mid = ?', $this->request->filter('int')->mid);
        }
        
        if (isset($this->request->slug)) {
            $tagSelect->where('slug = ?', $this->request->slug);
        }

        /** 如果是标签 */
        $tag = $this->db->fetchRow($tagSelect,
        array($this->widget('Widget_Abstract_Metas'), 'filter'));
        
        if (!$tag) {
            throw new Typecho_Widget_Exception(_t('标签不存在'), 404);
        }
    
        /** fix sql92 by 70 */
        $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
        ->where('table.relationships.mid = ?', $tag['mid'])
        ->where('table.contents.type = ?', 'post');
        
        /** 设置分页 */
        $this->_pageRow = $tag;
        
        /** 设置关键词 */
        $this->_keywords = $tag['name'];
        
        /** 设置描述 */
        $this->_description = $tag['description'];
        
        /** 设置头部feed */
        /** RSS 2.0 */
        $this->_feedUrl = $tag['feedUrl'];
        
        /** RSS 1.0 */
        $this->_feedRssUrl = $tag['feedRssUrl'];
        
        /** ATOM 1.0 */
        $this->_feedAtomUrl = $tag['feedAtomUrl'];
        
        /** 设置标题 */
        $this->_archiveTitle[] = $tag['name'];
        
        /** 设置归档类型 */
        $this->_archiveType = 'tag';
        
        /** 设置归档缩略名 */
        $this->_archiveSlug = $tag['slug'];
        
        /** 插件接口 */
        $this->pluginHandle()->tagHandle($select, $this);
    }
    
    /**
     * 处理作者
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function authorHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        $uid = $this->request->filter('int')->uid;
        
        $author = $this->db->fetchRow($this->db->select()->from('table.users')
        ->where('uid = ?', $uid),
        array($this->widget('Widget_Abstract_Users'), 'filter'));
        
        if (!$author) {
            throw new Typecho_Widget_Exception(_t('作者不存在'), 404);
        }
        
        $select->where('table.contents.authorId = ?', $uid);
        
        /** 设置分页 */
        $this->_pageRow = $author;
        
        /** 设置关键词 */
        $this->_keywords = $author['screenName'];
        
        /** 设置描述 */
        $this->_description = $author['screenName'];
        
        /** 设置头部feed */
        /** RSS 2.0 */
        $this->_feedUrl = $author['feedUrl'];
        
        /** RSS 1.0 */
        $this->_feedRssUrl = $author['feedRssUrl'];
        
        /** ATOM 1.0 */
        $this->_feedAtomUrl = $author['feedAtomUrl'];
        
        /** 设置标题 */
        $this->_archiveTitle[] = $author['screenName'];
        
        /** 设置归档类型 */
        $this->_archiveType = 'author';
        
        /** 设置归档缩略名 */
        $this->_archiveSlug = $author['uid'];
        
        /** 插件接口 */
        $this->pluginHandle()->authorHandle($select, $this);
    }
    
    /**
     * 处理日期
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function dateHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        /** 如果是按日期归档 */
        $year = $this->request->filter('int')->year;
        $month = $this->request->filter('int')->month;
        $day = $this->request->filter('int')->day;
        
        if (!empty($year) && !empty($month) && !empty($day)) {
        
            /** 如果按日归档 */
            $from = mktime(0, 0, 0, $month, $day, $year);
            $to = mktime(23, 59, 59, $month, $day, $year);
            
            /** 归档缩略名 */
            $this->_archiveSlug = 'day';
            
            /** 设置标题 */
            $this->_archiveTitle[] = $year;
            $this->_archiveTitle[] = $month;
            $this->_archiveTitle[] = $day;
        } else if (!empty($year) && !empty($month)) {
        
            /** 如果按月归档 */
            $from = mktime(0, 0, 0, $month, 1, $year);
            $to = mktime(23, 59, 59, $month, date('t', $from), $year);
            
            /** 归档缩略名 */
            $this->_archiveSlug = 'month';
            
            /** 设置标题 */
            $this->_archiveTitle[] = $year;
            $this->_archiveTitle[] = $month;
        } else if (!empty($year)) {
        
            /** 如果按年归档 */
            $from = mktime(0, 0, 0, 1, 1, $year);
            $to = mktime(23, 59, 59, 12, 31, $year);
            
            /** 归档缩略名 */
            $this->_archiveSlug = 'year';
            
            /** 设置标题 */
            $this->_archiveTitle[] = $year;
        }
        
        $select->where('table.contents.created >= ?', $from - $this->options->timezone + $this->options->serverTimezone)
        ->where('table.contents.created <= ?', $to - $this->options->timezone + $this->options->serverTimezone)
        ->where('table.contents.type = ?', 'post');
        
        /** 设置归档类型 */
        $this->_archiveType = 'date';
        
        /** 设置头部feed */
        $value = array('year' => $year, 'month' => str_pad($month, 2, '0', STR_PAD_LEFT), 'day' => str_pad($day, 2, '0', STR_PAD_LEFT));
        
        /** 设置分页 */
        $this->_pageRow = $value;
        
        /** 获取当前路由,过滤掉翻页情况 */
        $currentRoute = str_replace('_page', '', $this->parameter->type);
        
        /** RSS 2.0 */
        $this->_feedUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedUrl);
        
        /** RSS 1.0 */
        $this->_feedRssUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedRssUrl);
        
        /** ATOM 1.0 */
        $this->_feedAtomUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedAtomUrl);
        
        /** 插件接口 */
        $this->pluginHandle()->dateHandle($select, $this);
    }
    
    /**
     * 处理搜索
     * 
     * @access private
     * @param Typecho_Db_Query $select 查询对象
     * @param boolean $hasPushed 是否已经压入队列
     * @return void
     */
    private function searchHandle(Typecho_Db_Query $select, &$hasPushed)
    {
        /** 增加自定义搜索引擎接口 */
        //~ fix issue 40
        $keywords = $this->request->filter('url', 'search')->keywords;
        $this->pluginHandle()->trigger($hasPushed)->search($keywords, $this);

        if (!$hasPushed) {
            $searchQuery = '%' . $keywords . '%';
            
            /** 搜索无法进入隐私项保护归档 */
            $select->where('table.contents.password IS NULL')
            ->where('table.contents.title LIKE ? OR table.contents.text LIKE ?', $searchQuery, $searchQuery)
            ->where('table.contents.type = ?', 'post');
        }
        
        /** 设置关键词 */
        $this->_keywords = $keywords;
        
        /** 设置分页 */
        $this->_pageRow = array('keywords' => $keywords);
        
        /** 设置头部feed */
        /** RSS 2.0 */
        $this->_feedUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedUrl);
        
        /** RSS 1.0 */
        $this->_feedRssUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedAtomUrl);
        
        /** ATOM 1.0 */
        $this->_feedAtomUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedAtomUrl);
        
        /** 设置标题 */
        $this->_archiveTitle[] = $keywords;
        
        /** 设置归档类型 */
        $this->_archiveType = 'search';
        
        /** 插件接口 */
        $this->pluginHandle()->searchHandle($select, $this);
    }

    /**
     * 重载select 
     * 
     * @access public
     * @return void
     */
    public function select()
    {
        if ($this->_feed) {
            // 对feed输出加入限制条件
            return parent::select()->where('table.contents.allowFeed = ?', 1)
            ->where('table.contents.password IS NULL');
        } else {
            return parent::select();
        }
    }

    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 处理搜索结果跳转 */
        if (isset($this->request->s)) {
            $filterKeywords = $this->request->filter('search')->s;
            
            /** 跳转到搜索页 */
            if (NULL != $filterKeywords) {
                $this->response->redirect(Typecho_Router::url('search', 
                array('keywords' => urlencode($filterKeywords)), $this->options->index));
            }
        }
    
        /** 初始化分页变量 */
        $this->_currentPage = isset($this->request->page) ? $this->request->page : 1;
        $hasPushed = false;

        /** 定时发布功能 */
        $select = $this->select()->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $this->options->gmtTime);
        
        /** 初始化其它变量 */
        $this->_feedUrl = $this->options->feedUrl;
        $this->_feedRssUrl = $this->options->feedRssUrl;
        $this->_feedAtomUrl = $this->options->feedAtomUrl;
        $this->_keywords = $this->options->keywords;
        $this->_description = $this->options->description;
        
        $handles = array(
            'index'                     =>  'indexHandle',
            'index_page'                =>  'indexHandle',
            404                         =>  'error404Handle',
            'page'                      =>  'singleHandle',
            'post'                      =>  'singleHandle',
            'attachment'                =>  'singleHandle',
            'comment_page'              =>  'singleHandle',
            'category'                  =>  'categoryHandle',
            'category_page'             =>  'categoryHandle',
            'tag'                       =>  'tagHandle',
            'tag_page'                  =>  'tagHandle',
            'author'                    =>  'authorHandle',
            'author_page'               =>  'authorHandle',
            'archive_year'              =>  'dateHandle',
            'archive_year_page'         =>  'dateHandle',
            'archive_month'             =>  'dateHandle',
            'archive_month_page'        =>  'dateHandle',
            'archive_day'               =>  'dateHandle',
            'archive_day_page'          =>  'dateHandle',
            'search'                    =>  'searchHandle',
            'search_page'               =>  'searchHandle'
        );
        
        if (isset($handles[$this->parameter->type])) {
            $handle = $handles[$this->parameter->type];
            $this->{$handle}($select, $hasPushed);
        } else {
            $hasPushed = $this->pluginHandle()->handle($this->parameter->type, $this, $select);
        }
        
        /** 如果已经提前压入则直接返回 */
        if ($hasPushed) {
            return;
        }
        
        /** 仅输出文章 */
        $this->_countSql = clone $select;

        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->parameter->pageSize);
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出文章内容
     *
     * @access public
     * @param string $more 文章截取后缀
     * @return void
     */
    public function content($more = NULL)
    {
        parent::content($this->is('single') ? false : $more);
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @param string $prev 上一页文字
     * @param string $next 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return void
     */
    public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...')
    {
        $hasNav = false;
        $this->pluginHandle()->trigger($hasNav)->pageNav($prev, $next, $splitPage, $splitWord);
        
        if (!$hasNav) {
            $query = Typecho_Router::url($this->parameter->type . 
            (false === strpos($this->parameter->type, '_page') ? '_page' : NULL),
            $this->_pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
            $this->_currentPage, $this->parameter->pageSize, $query);
            $nav->render($prev, $next, $splitPage, $splitWord);
        }
    }
    
    /**
     * 前一页
     * 
     * @access public
     * @param string $word 链接标题
     * @param string $page 页面链接
     * @return void
     */
    public function pageLink($word = '&laquo; Previous Entries', $page = 'prev')
    {
        if (empty($this->_pageNav)) {
            $query = Typecho_Router::url($this->parameter->type . 
            (false === strpos($this->parameter->type, '_page') ? '_page' : NULL),
            $this->_pageRow, $this->options->index);

            /** 使用盒状分页 */
            $this->_pageNav = new Typecho_Widget_Helper_PageNavigator_Classic(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
            $this->_currentPage, $this->parameter->pageSize, $query);
        }
        
        $this->_pageNav->{$page}($word);
    }
    
    /**
     * 获取评论归档对象
     * 
     * @access public
     * @param string $type 评论类型
     * @param boolean $desc 是否倒序输出
     * @param boolean $pageSize 评论分页数目,如果为0则代表不分页
     * @param boolean $focusLast 是否自动聚焦到最后一页
     * @return Widget_Abstract_Comments
     */
    public function comments($type = NULL, $desc = 0, $pageSize = 0, $focusLast = 0)
    {
        $type = strtolower($type);
        $parameter = array('parentId' => $this->hidden ? 0 : $this->cid, 'type' => $type, 'desc' => $desc,
        'pageSize' => $pageSize, 'focusLast' => $focusLast, 'parentContent' => $this->row,
        'commentPage' => $this->request->filter('int')->commentPage);

        return $this->widget('Widget_Comments_Archive' . (empty($type) ? '' : '@' . $type), $parameter);
    }
    
    /**
     * 获取附件对象
     * 
     * @access public
     * @param integer $limit 最大个数
     * @param integer $offset 重新
     * @return Widget_Contents_Attachment_Related
     */
    public function attachments($limit = 0, $offset = 0)
    {
        return $this->widget('Widget_Contents_Attachment_Related', array('parentId' => $this->cid, 'limit' => $limit));
    }
    
    /**
     * 显示下一个内容的标题链接
     * 
     * @access public
     * @param string $format 格式
     * @param string $default 如果没有下一篇,显示的默认文字
     * @return void
     */
    public function theNext($format = '%s', $default = NULL)
    {
        $content = $this->db->fetchRow($this->select()->where('table.contents.created > ? AND table.contents.created < ?',
        $this->created, $this->options->gmtTime)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $this->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_ASC)
        ->limit(1));
        
        if ($content) {
            $content = $this->filter($content);
            $link = '<a href="' . $content['permalink'] . '" title="' . $content['title'] . '">' . $content['title'] . '</a>';
            printf($format, $link);
        } else {
            echo $default;
        }
    }
    
    /**
     * 显示上一个内容的标题链接
     * 
     * @access public
     * @param string $format 格式
     * @param string $default 如果没有上一篇,显示的默认文字
     * @return void
     */
    public function thePrev($format = '%s', $default = NULL)
    {
        $content = $this->db->fetchRow($this->select()->where('table.contents.created < ?', $this->created)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $this->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1));
        
        if ($content) {
            $content = $this->filter($content);
            $link = '<a href="' . $content['permalink'] . '" title="' . $content['title'] . '">' . $content['title'] . '</a>';
            printf($format, $link);
        } else {
            echo $default;
        }
    }
    
    /**
     * 获取关联内容组件
     * 
     * @access public
     * @param integer $limit 输出数量
     * @param string $type 关联类型
     * @return Typecho_Widget
     */
    public function related($limit = 5, $type = NULL)
    {
        $type = strtolower($type);
        
        switch ($type) {
            case 'author':
                /** 如果访问权限被设置为禁止,则tag会被置为空 */
                return $this->widget('Widget_Contents_Related_Author', 
                array('cid' => $this->cid, 'type' => $this->type, 'author' => $this->author->uid, 'limit' => $limit));
            default:
                /** 如果访问权限被设置为禁止,则tag会被置为空 */
                return $this->widget('Widget_Contents_Related', 
                array('cid' => $this->cid, 'type' => $this->type, 'tags' => $this->tags, 'limit' => $limit));
        }
    }
    
    /**
     * 输出头部元数据
     * 
     * @access public
     * @param string $rule 规则
     * @return void
     */
    public function header($rule = NULL)
    {
        $rules = array();
        $allows = array(
            'description'   =>  htmlspecialchars($this->_description),
            'keywords'      =>  htmlspecialchars($this->_keywords),
            'generator'     =>  $this->options->generator,
            'template'      =>  $this->options->theme,
            'pingback'      =>  $this->options->xmlRpcUrl,
            'xmlrpc'        =>  $this->options->xmlRpcUrl . '?rsd',
            'wlw'           =>  $this->options->xmlRpcUrl . '?wlw',
            'rss2'          =>  $this->_feedUrl,
            'rss1'          =>  $this->_feedRssUrl,
            'atom'          =>  $this->_feedAtomUrl
        );
        
        /** 头部是否输出聚合 */
        $allowFeed = !$this->is('single') || $this->allow('feed') || $this->_archiveCustom;
        
        if (!empty($rule)) {
            parse_str($rule, $rules);
            $allows = array_merge($allows, $rules);
        }
    
        $header = '';
        if (!empty($allows['description'])) {
            $header .= '<meta name="description" content="' . $allows['description'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['keywords'])) {
            $header .= '<meta name="keywords" content="' . $allows['keywords'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['generator'])) {
            $header .= '<meta name="generator" content="' . $allows['generator'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['template'])) {
            $header .= '<meta name="template" content="' . $allows['template'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['pingback'])) {
            $header .= '<link rel="pingback" href="' . $allows['pingback'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['xmlrpc'])) {
            $header .= '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="' . $allows['xmlrpc'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['wlw'])) {
            $header .= '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="' . $allows['wlw'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['rss2']) && $allowFeed) {
            $header .= '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="' . $allows['rss2'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['rss1']) && $allowFeed) {
            $header .= '<link rel="alternate" type="application/rdf+xml" title="RSS 1.0" href="' . $allows['rss1'] . '" />' . "\r\n";
        }
        
        if (!empty($allows['atom']) && $allowFeed) {
            $header .= '<link rel="alternate" type="application/atom+xml" title="ATOM 1.0" href="' . $allows['atom'] . '" />' . "\r\n";
        }
        
        /** 插件支持 */
        $this->pluginHandle()->header($header, $this);
        
        /** 输出header */
        echo $header;
    }
    
    /**
     * 支持页脚自定义
     * 
     * @access public
     * @return void
     */
    public function footer()
    {
        $this->pluginHandle()->footer($this);
    }
    
    /**
     * 输出cookie记忆别名
     * 
     * @access public
     * @param string $cookieName 已经记忆的cookie名称
     * @param string $clear 及时清理cookie
     * @return string
     */
    public function remember($cookieName, $clear = false)
    {
        $value = Typecho_Cookie::get('__typecho_remember_' . $cookieName);
        if ($clear) {
            Typecho_Cookie::delete('__typecho_remember_' . $cookieName);
        }
        
        echo htmlspecialchars($value);
    }
    
    /**
     * 输出归档标题
     * 
     * @access public
     * @param string $split
     * @return void
     */
    public function archiveTitle($split = ' &raquo; ', $before = ' &raquo; ', $end = '')
    {
        if ($this->_archiveTitle) {
            echo $before . implode($split, $this->_archiveTitle) . $end;
        }
    }
    
    /**
     * 输出关键字
     * 
     * @access public
     * @return unknown
     */
    public function keywords($split = ',', $default = '')
    {
        echo empty($this->_keywords) ? $default : str_replace(',', $split, htmlspecialchars($this->_keywords));
    }
    
    /**
     * 判断归档类型和名称
     * 
     * @access public
     * @param string $archiveType 归档类型
     * @param string $archiveSlug 归档名称
     * @return boolean
     */
    public function is($archiveType, $archiveSlug = NULL)
    {
        return ($archiveType == $this->_archiveType || 
        (($this->_archiveSingle ? 'single' : 'archive') == $archiveType && 'index' != $this->_archiveType))
        && (empty($archiveSlug) ? true : $archiveSlug == $this->_archiveSlug);
    }
    
    /**
     * 获取主题文件
     * 
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function need($fileName)
    {
        require __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/' . $fileName;
    }
    
    /**
     * 输出视图
     * 
     * @access public
     * @return void
     */
    public function render()
    {    
        /** 添加Pingback */
        $this->response->setHeader('X-Pingback', $this->options->xmlRpcUrl);
        $themeDir = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/';
        $validated = false;
        
        //~ 自定义模板
        if (!empty($this->_themeFile)) {
            if ($this->_archiveCustom || file_exists($themeDir . $this->_themeFile)) {
                $validated = true;
            }
        } else if (!empty($this->_archiveType)) {
        
            //~ 首先找具体路径, 比如 category/default.php
            if (!$validated && !empty($this->_archiveSlug)) {
                $themeFile = $this->_archiveType . '/' . $this->_archiveSlug . '.php';
                if (file_exists($themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }

            //~ 然后找归档类型路径, 比如 category.php
            if (!$validated) {
                $themeFile = $this->_archiveType . '.php';
                if (file_exists($themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }
            
            //针对attachment的hook
            if (!$validated && 'attachment' == $this->_archiveType) {
                if (file_exists($themeDir . 'page.php')) {
                    $this->_themeFile = 'page.php';
                    $validated = true;
                } else if (file_exists($themeDir . 'post.php')) {
                    $this->_themeFile = 'post.php';
                    $validated = true;
                }
            }
            
            //~ 最后找归档路径, 比如 archive.php 或者 single.php
            if (!$validated && 'index' != $this->_archiveType) {
                $themeFile = $this->_archiveSingle ? 'single.php' : 'archive.php';
                if (file_exists($themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }
            
            if (!$validated && '404.php' != $this->_themeFile) {
                $this->_themeFile = 'index.php';
            }
        }
        
        /** 文件不存在 */
        if (!$validated && !file_exists($themeDir . $this->_themeFile)) {
        
            /** 单独处理404情况 */
            if (404 == $this->_archiveType) {
                Typecho_Common::error(404);
            } else {
                throw new Typecho_Widget_Exception(_t('请求的地址不存在'), 404);
            }
        }
    
        /** 挂接插件 */
        $this->pluginHandle()->beforeRender($this);
        
        /** 输出模板 */
        require_once $themeDir . $this->_themeFile;
        
        /** 挂接插件 */
        $this->pluginHandle()->afterRender($this);
    }

    /**
     * 输出feed
     * 
     * @access public
     * @return void
     */
    public function feed()
    {
        $this->_feed->setSubTitle($this->_description);
        $this->_feed->setFeedUrl($this->_currentFeedUrl);
        
        $this->_feed->setBaseUrl(('/' == $this->request->feed || 0 == strlen($this->request->feed)
        || '/comments' == $this->request->feed || '/comments/' == $this->request->feed) ?
        $this->options->siteUrl : Typecho_Common::url($this->request->feed, $this->options->index));
        $this->_feed->setFeedUrl($this->request->makeUriByRequest());
        
        if ($this->is('single') || 'comments' == $this->parameter->type) {
            $this->_feed->setTitle(_t('%s 的评论', 
            $this->options->title . ($this->_archiveTitle ? ' - ' . implode(' - ', $this->_archiveTitle) : NULL)));
        
            if ('comments' == $this->parameter->type) {
                $comments = $this->widget('Widget_Comments_Recent', 'pageSize=10');
            } else {
                $comments = $this->comments(NULL, true);
            }
            
            while ($comments->next()) {
                $suffix = $this->pluginHandle()->trigger($plugged)->commentFeedItem($this->_feedType, $comments);
                if (!$plugged) {
                    $suffix = NULL;
                }
                
                $this->_feed->addItem(array(
                    'title'     =>  $comments->author,
                    'content'   =>  $comments->content,
                    'date'      =>  $comments->created,
                    'link'      =>  $comments->permalink,
                    'author'    =>  (object) array(
                        'screenName'  =>  $comments->author,
                        'url'         =>  $comments->url,
                        'mail'        =>  $comments->mail
                    ),
                    'excerpt'   =>  strip_tags($comments->content),
                    'suffix'    =>  $suffix
                ));
            }
        } else {
            $this->_feed->setTitle($this->options->title . ($this->_archiveTitle ? ' - ' . implode(' - ', $this->_archiveTitle) : NULL));
            
            $feedUrl = '';
            if (Typecho_Feed::RSS2 == $this->_feedType) {
                $feedUrl = $this->feedUrl;
            } else if (Typecho_Feed::RSS1 == $this->_feedType) {
                $feedUrl = $this->feedRssUrl;
            } else if (Typecho_Feed::ATOM1 == $this->_feedType) {
                $feedUrl = $this->feedAtomUrl;
            }
            
            while ($this->next()) {
                $suffix = $this->pluginHandle()->trigger($plugged)->feedItem($this->_feedType, $this);
                if (!$plugged) {
                    $suffix = NULL;
                }
                
                $this->_feed->addItem(array(
                    'title'     =>  $this->title,
                    'content'   =>  $this->options->feedFullText ? $this->content : (false !== strpos($this->text, '<!--more-->') ?
                    $this->excerpt . "<p class=\"more\"><a href=\"{$this->permalink}\" title=\"{$this->title}\">[...]</a></p>" : $this->content),
                    'date'      =>  $this->created,
                    'link'      =>  $this->permalink,
                    'author'    =>  $this->author,
                    'excerpt'   =>  $this->description,
                    'comments'  =>  $this->commentsNum,
                    'commentsFeedUrl' => $feedUrl,
                    'suffix'    =>  $suffix
                ));
            }
        }
        
        echo $this->_feed->__toString();
    }
}
