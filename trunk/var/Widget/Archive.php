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
    private $_themeFile = 'index.php';
    
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
     * 构造函数
     * 
     * @access public
     * @param mixed $params 传递的参数
     * @return void
     */
    public function __construct($params = NULL)
    {
        parent::__construct($params);

        /** 处理feed模式 **/
        if ('feed' == Typecho_Router::$current) {
        
            /** 判断聚合类型 */
            switch (true) {
                case 0 === strpos($this->request->feed, '/rss/') || '/rss' == $this->request->feed:
                    /** 如果是RSS1标准 */
                    $this->request->feed = substr($this->request->feed, 4);
                    $this->_feedType = Typecho_Feed::RSS1;
                    break;
                case 0 === strpos($this->request->feed, '/atom/') || '/atom' == $this->request->feed:
                    /** 如果是ATOM标准 */
                    $this->request->feed = substr($this->request->feed, 5);
                    $this->_feedType = Typecho_Feed::ATOM1;
                    break;
                default:
                    $this->_feedType = Typecho_Feed::RSS2;
                    break;
            }
        
            if (!Typecho_Router::match($this->request->feed) || 'feed' == Typecho_Router::$current) {
                if (0 === strpos($this->request->feed, '/comments/') || '/comments' == $this->request->feed) {
                    /** 专为feed使用的hack */
                    Typecho_Router::$current = 'comments';
                } else {
                    throw new Typecho_Widget_Exception(_t('聚合页不存在'), 404);
                }
            }
            
            /** 初始化聚合器 */
            $this->_feed = Typecho_Feed::generator($this->_feedType);
            
            /** 默认输出10则文章 **/
            $this->parameter->pageSize = 10;
        }
    }

    /**
     * 重载select 
     * 
     * @access public
     * @return void
     */
    public function select()
    {
        if ('feed' == Typecho_Router::$current) {
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
        if ($this->request->isSetParameter('s')) {
            $filterKeywords = Typecho_Common::filterSearchQuery($this->request->s);
            
            /** 跳转到搜索页 */
            if (NULL != $filterKeywords) {
                $this->response->redirect(Typecho_Router::url('search', 
                array('keywords' => urlencode($filterKeywords)), $this->options->index));
            }
        }
    
        /** 初始化分页变量 */
        $this->parameter->setDefault(array('pageSize' => $this->options->pageSize));
        $this->_currentPage = isset($this->request->page) ? $this->request->page : 1;
        $hasPushed = false;

        /** 定时发布功能 */
        $select = $this->select()->where('table.contents.created < ?', $this->options->gmtTime);
        
        /** 初始化其它变量 */
        $this->_feedUrl = $this->options->feedUrl;
        $this->_feedRssUrl = $this->options->feedRssUrl;
        $this->_feedAtomUrl = $this->options->feedAtomUrl;
        $this->_keywords = $this->options->keywords;
        $this->_description = $this->options->description;

        switch (Typecho_Router::$current) {
            /** 单篇内容 */
            case 'page':
            case 'post':
                
                /** 如果是单篇文章或独立页面 */
                if (NULL !== $this->request->cid) {
                    $select->where('table.contents.cid = ?', $this->request->cid);
                } else if (NULL !== $this->request->slug) {
                    $select->where('table.contents.slug = ?', $this->request->slug);
                } else {
                    /** 对没有索引情况下的判断 */
                    throw new Typecho_Widget_Exception('post' == Typecho_Router::$current ? _t('文章不存在') : _t('页面不存在'), 404);
                }

                /** 保存密码至cookie */
                if ($this->request->isPost() && isset($this->request->protectPassword)) {
                    $this->response->setCookie('protectPassword', $this->request->protectPassword, 0, $this->options->siteUrl);
                }
                
                $select->where('table.contents.type = ?', Typecho_Router::$current)->limit(1);
                $post = $this->db->fetchRow($select, array($this, 'push'));

                if ($post && $post['category'] == $this->request->getParameter('category', $post['category'])
                && $post['year'] == $this->request->getParameter('year', $post['year'])
                && $post['month'] == $this->request->getParameter('month', $post['month'])
                && $post['day'] == $this->request->getParameter('day', $post['day'])) {
                    /** 设置关键词 */
                    $this->_keywords = implode(',', Typecho_Common::arrayFlatten($this->tags, 'name'));
                    
                    /** 设置模板 */
                    if (!empty($post['template'])) {
                        /** 应用自定义模板 */
                        $this->_themeFile = $post['template'];
                    }
                    
                    /** 设置头部feed */
                    /** RSS 2.0 */
                    $this->_feedUrl = $post['feedUrl'];
                    
                    /** RSS 1.0 */
                    $this->_feedRssUrl = $post['feedRssUrl'];
                    
                    /** ATOM 1.0 */
                    $this->_feedAtomUrl = $post['feedAtomUrl'];
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $post['title'];
                    
                    /** 设置归档类型 */
                    $this->options->archiveType = Typecho_Router::$current;
                    
                    /** 设置403头 */
                    if ($post['hidden']) {
                        $this->response->setStatus(403);
                    }
                } else {
                    throw new Typecho_Widget_Exception('post' == Typecho_Router::$current ? _t('文章不存在') : _t('页面不存在'), 404);
                }
                
                /** 设置风格文件 */
                $this->_themeFile = 'post' == Typecho_Router::$current ? 'single.php' : 'page.php';
                $hasPushed = true;
                break;
                
            /** 分类归档 */
            case 'category':
            case 'category_page':
                /** 如果是分类 */
                $category = $this->db->fetchRow($this->db->select()
                ->from('table.metas')
                ->where('type = ?', 'category')
                ->where('slug = ?', $this->request->slug)->limit(1),
                array($this->widget('Widget_Abstract_Metas'), 'filter'));
                
                if (!$category) {
                    throw new Typecho_Widget_Exception(_t('分类不存在'), 404);
                }
            
                /** fix sql92 by 70 */
                $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
                ->where('table.relationships.mid = ?', $category['mid']);
                
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
                $this->options->archiveTitle = $category['name'];
                
                /** 设置归档类型 */
                $this->options->archiveType = 'category';
                
                /** 设置风格文件 */
                $this->_themeFile = 'archive.php';
                break;

            /** 标签归档 */
            case 'tag':
            case 'tag_page':

                /** 如果是标签 */
                $tag = $this->db->fetchRow($this->db->select()->from('table.metas')
                ->where('type = ?', 'tag')
                ->where('slug = ?', $this->request->slug)->limit(1),
                array($this->widget('Widget_Abstract_Metas'), 'filter'));
                
                if (!$tag) {
                    throw new Typecho_Widget_Exception(_t('标签%s不存在', $this->request->slug), 404);
                }
            
                /** fix sql92 by 70 */
                $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
                ->where('table.relationships.mid = ?', $tag['mid']);
                
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
                $this->options->archiveTitle = $tag['name'];
                
                /** 设置归档类型 */
                $this->options->archiveType = 'tag';
                
                /** 设置风格文件 */
                $this->_themeFile = 'archive.php';
                break;

            /** 日期归档 */
            case 'archive_year':
            case 'archive_month':
            case 'archive_day':
            case 'archive_year_page':
            case 'archive_month_page':
            case 'archive_day_page':

                /** 如果是按日期归档 */
                $year = $this->request->year;
                $month = $this->request->month;
                $day = $this->request->day;
                
                if (!empty($year) && !empty($month) && !empty($day)) {
                
                    /** 如果按日归档 */
                    $from = mktime(0, 0, 0, $month, $day, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, $month, $day, $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $year . '-' . $month . '-' . $day;
                } else if (!empty($year) && !empty($month)) {
                
                    /** 如果按月归档 */
                    $from = mktime(0, 0, 0, $month, 1, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, $month, idate('t', $from), $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $year . '-' . $month;
                } else if (!empty($year)) {
                
                    /** 如果按年归档 */
                    $from = mktime(0, 0, 0, 1, 1, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, 12, 31, $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $year;
                }
                
                $select->where('table.contents.created >= ?', $from)
                ->where('table.contents.created <= ?', $to);
                
                /** 设置归档类型 */
                $this->options->archiveType = 'date';
                
                /** 设置头部feed */
                $value = array('year' => $year, 'month' => $month, 'day' => $day);
                
                /** 设置分页 */
                $this->_pageRow = $value;
                
                /** 获取当前路由,过滤掉翻页情况 */
                $currentRoute = str_replace('_page', '', Typecho_Router::$current);
                
                /** RSS 2.0 */
                $this->_feedUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedUrl);
                
                /** RSS 1.0 */
                $this->_feedRssUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedRssUrl);
                
                /** ATOM 1.0 */
                $this->_feedAtomUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedAtomUrl);
                
                /** 设置风格文件 */
                $this->_themeFile = 'archive.php';
                break;

            /** 搜索归档 */
            case 'search':
            case 'search_page':
    
                /** 增加自定义搜索引擎接口 */
                //~ fix issue 40
                $this->plugin()->trigger($hasPushed)->search($this->request->keywords, $this);
    
                $keywords = Typecho_Common::filterSearchQuery($this->request->keywords);
                $searchQuery = '%' . $keywords . '%';
                
                /** 搜索无法进入隐私项保护归档 */
                $select->where('table.contents.password IS NULL')
                ->where('table.contents.title LIKE ? OR table.contents.text LIKE ?', $searchQuery, $searchQuery);
                
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
                $this->options->archiveTitle = $keywords;
                
                /** 设置归档类型 */
                $this->options->archiveType = 'search';
                
                /** 设置风格文件 */
                $this->_themeFile = 'archive.php';
                break;

            default:
                break;
        }
        
        /** 如果已经提前压入则直接返回 */
        if ($hasPushed) {
            return;
        }
        
        /** 仅输出文章 */
        $select->where('table.contents.type = ?', 'post');
        $this->_countSql = clone $select;

        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->parameter->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
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
        $this->plugin()->trigger($hasNav)->pageNav($prev, $next, $splitPage, $splitWord);
        
        if (!$hasNav) {
            $query = Typecho_Router::url(Typecho_Router::$current . 
            (false === strpos(Typecho_Router::$current, '_page') ? '_page' : NULL),
            $this->_pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
            $this->_currentPage, $this->parameter->pageSize, $query);
            $nav->render($prev, $next, $splitPage, $splitWord);
        }
    }
    
    /**
     * 获取评论归档对象
     * 
     * @access public
     * @param string $mode 评论模式
     * @param boolean $desc 是否倒序输出
     * @return Widget_Abstract_Comments
     */
    public function comments($mode = NULL, $desc = false)
    {
        $mode = strtolower($mode);
        $parameter = array('cid' => $this->hidden ? 0 : $this->cid, 'desc' => $desc, 'parentContent' => $this->row);
        
        switch ($mode) {
            case 'comment':
                return $this->widget('Widget_Comments_Archive_Comment', $parameter);
            case 'trackback':
                return $this->widget('Widget_Comments_Archive_Trackback', $parameter);
            case 'pingback':
                return $this->widget('Widget_Comments_Archive_Pingback', $parameter);
            default:
                return $this->widget('Widget_Comments_Archive', $parameter);
        }
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
     * @return Typecho_Widget
     */
    public function related($limit = 5)
    {
        /** 如果访问权限被设置为禁止,则tag会被置为空 */
        return $this->widget('Widget_Contents_Related', array('cid' => $this->cid, 'type' => $this->type, 'tags' => $this->tags, 'limit' => $limit));
    }
    
    /**
     * 输出头部元数据
     * 
     * @access public
     * @return void
     */
    public function header()
    {
        $header = new Typecho_Widget_Helper_Layout_Header();
        $header->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'description', 'content' => $this->_description)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'keywords', 'content' => $this->_keywords)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'generator', 'content' => $this->options->generator)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'template', 'content' => $this->options->theme)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'pingback', 'href' => $this->options->xmlRpcUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'EditURI', 'type' => 'application/rsd+xml', 'title' => 'RSD', 'href' => $this->options->xmlRpcUrl . '?rsd')))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'wlwmanifest', 'type' => 'application/wlwmanifest+xml',
        'href' => Typecho_Common::url('wlwmanifest.xml', $this->options->adminUrl))))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0', 'href' => $this->_feedUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 1.0', 'href' => $this->_feedRssUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'ATOM 1.0', 'href' => $this->_feedAtomUrl)));
        
        /** 插件支持 */
        $this->plugin()->header($header);
        
        /** 输出header */
        $header->render();
    }
    
    /**
     * 输出cookie记忆别名
     * 
     * @access public
     * @param string $cookieName 已经记忆的cookie名称
     * @return string
     */
    public function remember($cookieName)
    {
        echo $this->request->getCookie($cookieName);
    }
    
    /**
     * 设置主题文件
     * 
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function setTheme($fileName)
    {
        $this->_themeFile = $fileName;
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
    
        /** 输出模板 */
        require_once __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/' . $this->_themeFile;
        
        /** 挂接插件 */
        $this->plugin()->render($this);
    }

    /**
     * 输出feed
     * 
     * @access public
     * @return void
     */
    public function feed()
    {
        $this->_feed->setCharset($this->options->charset);
        $this->_feed->setTitle(($this->options->archiveTitle ? $this->options->archiveTitle . ' - ' : NULL) . $this->options->title);
        $this->_feed->setSubTitle($this->_description);

        if (Typecho_Feed::RSS2 == $this->_feedType) {
            $this->_feed->setChannelElement('language', _t('zh-cn'));
            $this->_feed->setLink($this->_feedUrl);
        }
        
        if (Typecho_Feed::RSS1 == $this->_feedType) {
            /** 如果是RSS1标准 */
            $this->_feed->setChannelAbout($this->_feedRssUrl);
            $this->_feed->setLink($this->_feedRssUrl);
        }
        
        if (Typecho_Feed::ATOM1 == $this->_feedType) {
            /** 如果是ATOM标准 */
            $this->_feed->setLink($this->_feedAtomUrl);
        }

        if (Typecho_Feed::RSS1 == $this->_feedType || Typecho_Feed::RSS2 == $this->_feedType) {
            $this->_feed->setDescription($this->_description);
        }

        if (Typecho_Feed::RSS2 == $this->_feedType || Typecho_Feed::ATOM1 == $this->_feedType) {
            $this->_feed->setChannelElement(Typecho_Feed::RSS2 == $this->_feedType ? 'pubDate' : 'updated',
            date(Typecho_Feed::dateFormat($this->_feedType), 
            $this->options->gmtTime + $this->options->timezone));
        }
        
        /** 插件接口 */
        $this->plugin()->feed($this->_feed, $this);
        
        /** 添加聚合频道 */
        switch (Typecho_Router::$current) {
            case 'post':
            case 'page':
            case 'comments':
                if ('comments' == Typecho_Router::$current) {
                    $comments = $this->widget('Widget_Comments_Recent', 'pageSize=10');
                } else {
                    $comments = $this->comments(NULL, true);
                }
                
                while ($comments->next()) {
                    $item = $this->_feed->createNewItem();
                    $item->setTitle($comments->author);
                    $item->setLink($comments->permalink);
                    $item->setDate($comments->date + $this->options->timezone);
                    $item->setDescription(Typecho_Common::cutParagraph($comments->content));

                    if (Typecho_Feed::RSS2 == $this->_feedType) {
                        $item->addElement('guid', $comments->permalink);
                        $item->addElement('content:encoded', Typecho_Common::subStr(Typecho_Common::stripTags($comments->content), 0, 100, '...'));
                        $item->addElement('author', $comments->author);
                        $item->addElement('dc:creator', $comments->author);
                    }
                    
                    $this->plugin()->commentFeedItem($item, $this->_feedType, $this);
                    $this->_feed->addItem($item);
                }
                break;
                
            case 'category':
            case 'category_page':
            case 'tag':
            case 'tag_page':
            case 'archive_year':
            case 'archive_month':
            case 'archive_day':
            case 'archive_year_page':
            case 'archive_month_page':
            case 'archive_day_page':
            case 'search':
            case 'search_page':
            default:
                while ($this->next()) {
                    $item = $this->_feed->createNewItem();
                    $item->setTitle($this->title);
                    $item->setLink($this->permalink);
                    $item->setDate($this->created + $this->options->timezone);
                    
                    /** RSS全文输出开关支持 */
                    if ($this->options->feedFullText) {
                        $item->setDescription($this->text);
                    } else {
                        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
                        $contents = explode('<!--more-->', $content);
                        
                        list($abstract) = $contents;
                        $item->setDescription(Typecho_Common::fixHtml($abstract) . (count($contents) > 1 ? '<p><a href="'
                        . $this->permalink . '">' . _t('阅读更多...') . '</a></p>' : NULL));
                    }
                    
                    $item->setCategory($this->categories);
                    
                    if (Typecho_Feed::RSS2 == $this->_feedType) {
                        $item->addElement('guid', $this->permalink);
                        $item->addElement('slash:comments', $this->commentsNum);
                        $item->addElement('comments', $this->permalink . '#comments');
                        $item->addElement('content:encoded', Typecho_Common::subStr(Typecho_Common::stripTags($this->text), 0, 100, '...'));
                        $item->addElement('author', $this->author);
                        $item->addElement('dc:creator', $this->author);
                        $item->addElement('wfw:commentRss', $this->feedUrl);
                    }
                    
                    $this->plugin()->feedItem($item, $this->_feedType, $this);
                    $this->_feed->addItem($item);
                }
                break;
        }
        
        $this->_feed->generateFeed();
    }
}
