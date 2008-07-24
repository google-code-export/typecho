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
 * @package Widget
 */
class Widget_Archive extends Widget_Abstract_Contents implements Typecho_Widget_Interface_ViewRenderer
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
     * 分页大小
     * 
     * @access private
     * @var integer
     */
    private $_pageSize;
    
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
     * 当前登录用户
     * 
     * @access public
     * @var Typecho_Widget
     */
    public $user;

    /**
     * 入口函数
     *
     * @access public
     * @param integer $_pageSize 每页文章数
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
        
        /** 初始化用户 */
        $this->user = Typecho_API::factory('Widget_Users_Current');
    
        /** 处理搜索结果跳转 */
        if(NULL != ($keywords = Typecho_Request::getParameter('keywords')) &&
        'search' != Typecho_Router::$current && 'search_page' != Typecho_Router::$current)
        {
            /** 跳转到搜索页 */
            Typecho_API::redirect(Typecho_Router::url('search', 
            array('keywords' => urlencode(Typecho_API::filterSearchQuery($keywords))), $this->options->index));
        }
    
        /** 初始化分页变量 */
        $this->_pageSize = empty($pageSize) ? $this->options->pageSize : $pageSize;
        $this->_currentPage = Typecho_Request::getParameter('page', 1);
        $hasPushed = false;
    
        $select = $this->select()->where('table.contents.`created` < ?', $this->options->gmtTime);

        switch(Typecho_Router::$current)
        {
            /** 单篇内容 */
            case 'page':
            case 'post':
                
                /** 如果是单篇文章或独立页面 */
                if(NULL !== Typecho_Request::getParameter('cid'))
                {
                    $select->where('table.contents.`cid` = ?', Typecho_Request::getParameter('cid'));
                }
            
                if(NULL !== Typecho_Request::getParameter('slug'))
                {
                    $select->where('table.contents.`slug` = ?', Typecho_Request::getParameter('slug'));
                }
                
                $select->where('table.contents.`type` = ?', Typecho_Router::$current)
                ->group('table.contents.`cid`')->limit(1);
                $post = $this->db->fetchRow($select, array($this, 'singlePush'));

                if($post && $post['category'] == Typecho_Request::getParameter('category', $post['category'])
                && $post['year'] == Typecho_Request::getParameter('year', $post['year'])
                && $post['month'] == Typecho_Request::getParameter('month', $post['month'])
                && $post['day'] == Typecho_Request::getParameter('day', $post['day']))
                {
                    /** 设置关键词 */
                    $this->options->keywords = implode(',', Typecho_API::arrayFlatten($post['tags'], 'name'));
                    
                    /** 设置头部feed */
                    /** RSS 2.0 */
                    $this->options->feedUrl = $post['feedUrl'];
                    
                    /** RSS 1.0 */
                    $this->options->feedRssUrl = $post['feedRssUrl'];
                    
                    /** ATOM 1.0 */
                    $this->options->feedAtomUrl = $post['feedAtomUrl'];
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $post['title'];
                    
                    /** 设置归档类型 */
                    $this->options->archiveType = Typecho_Router::$current;
                    
                    /** 设定HTTP头 */
                    if(!empty($post['password']) && 
                    $post['password'] != Typecho_Request::getParameter('protect_password', Typecho_Request::getCookie('protect_password')))
                    {
                        /** 避免被搜索引擎收录 */
                        header('HTTP/1.1 403 Forbidden');
                    }
                    
                    /** 设置密码的cookie记录 */
                    if(!empty($post['password']) && Typecho_Request::getParameter('protect_password'))
                    {
                        if($post['password'] == Typecho_Request::getParameter('protect_password'))
                        {
                            Typecho_Request::setCookie('protect_password', 
                            Typecho_Request::getParameter('protect_password'), 0, $this->options->siteUrl);
                        }
                        else
                        {
                            throw new Typecho_Widget_Exception(_t('对不起,您输入的密码错误'));
                        }
                    }
                }
                else
                {
                    throw new Typecho_Widget_Exception('post' == Typecho_Router::$current ? _t('文章不存在') : _t('页面不存在'), Typecho_Exception::NOTFOUND);
                }
                
                /** 设置风格文件 */
                $this->_themeFile = 'post' == Typecho_Router::$current ? 'single.php' : 'page.php';
                $hasPushed = true;
                break;
                
            /** 分类归档 */
            case 'category':
            case 'category_page':
            
                /** 如果是分类 */
                $category = $this->db->fetchRow($this->db->sql()->select('table.metas')
                ->where('`type` = ?', 'category')
                ->where('`slug` = ?', Typecho_Request::getParameter('slug'))->limit(1),
                array($this->abstractMetasWidget, 'filter'));
                
                if(!$category)
                {
                    throw new Typecho_Widget_Exception(_t('分类不存在'), Typecho_Exception::NOTFOUND);
                }
            
                $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
                ->where('table.relationships.`mid` = ?', $category['mid']);
                
                /** 设置分页 */
                $this->_pageRow = $category;
                
                /** 设置关键词 */
                $this->options->keywords = $category['name'];
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->options->feedUrl = $category['feedUrl'];
                
                /** RSS 1.0 */
                $this->options->feedRssUrl = $category['feedRssUrl'];
                
                /** ATOM 1.0 */
                $this->options->feedAtomUrl = $category['feedAtomUrl'];
                
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
                $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
                ->where('`type` = ?', 'tag')
                ->where('`slug` = ?', Typecho_Request::getParameter('slug'))->limit(1),
                array($this->abstractMetasWidget, 'filter'));
                
                if(!$tag)
                {
                    throw new Typecho_Widget_Exception(_t('标签%s不存在', Typecho_Request::getParameter('slug')), Typecho_Exception::NOTFOUND);
                }
            
                $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
                ->where('table.relationships.`mid` = ?', $tag['mid']);
                
                /** 设置分页 */
                $this->_pageRow = $tag;
                
                /** 设置关键词 */
                $this->options->keywords = $tag['name'];
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->options->feedUrl = $tag['feedUrl'];
                
                /** RSS 1.0 */
                $this->options->feedRssUrl = $tag['feedRssUrl'];
                
                /** ATOM 1.0 */
                $this->options->feedAtomUrl = $tag['feedAtomUrl'];
                
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
                $year = Typecho_Request::getParameter('year');
                $month = Typecho_Request::getParameter('month');
                $day = Typecho_Request::getParameter('day');
                
                /** 如果按日归档 */
                if(!empty($year) && !empty($month) && !empty($day))
                {
                    $from = mktime(0, 0, 0, $month, $day, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, $month, $day, $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $year . '-' . $month . '-' . $day;
                }
                /** 如果按月归档 */
                else if(!empty($year) && !empty($month))
                {
                    $from = mktime(0, 0, 0, $month, 1, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, $month, idate('t', $from), $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $year . '-' . $month;
                }
                /** 如果按年归档 */
                else if(!empty($year))
                {
                    $from = mktime(0, 0, 0, 1, 1, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, 12, 31, $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->options->archiveTitle = $year;
                }
                
                $select->where('table.contents.`created` >= ?', $from)
                ->where('table.contents.`created` <= ?', $to);
                
                /** 设置归档类型 */
                $this->options->archiveType = 'date';
                
                /** 设置头部feed */
                $value = array('year' => $year, 'month' => $month, 'day' => $day);
                
                /** 设置分页 */
                $this->_pageRow = $value;
                
                /** 获取当前路由,过滤掉翻页情况 */
                $currentRoute = str_replace('_page', '', Typecho_Router::$current);
                
                /** RSS 2.0 */
                $this->options->feedUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedUrl);
                
                /** RSS 1.0 */
                $this->options->feedRssUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedRssUrl);
                
                /** ATOM 1.0 */
                $this->options->feedAtomUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedAtomUrl);
                
                /** 设置风格文件 */
                $this->_themeFile = 'archive.php';
                break;

            /** 搜索归档 */
            case 'search':
            case 'search_page':
    
                /** 增加自定义搜索引擎接口 */
                $hasPushed = _p(__FILE__, 'Action')->search($keywords, $this);
    
                $keywords = Typecho_API::filterSearchQuery($keywords);
                $searchQuery = '%' . $keywords . '%';
                
                /** 搜索无法进入隐私项保护归档 */
                $select->where('table.contents.`password` IS NULL')
                ->where('table.contents.`title` LIKE ? OR table.contents.`text` LIKE ?', $searchQuery, $searchQuery);
                
                /** 设置关键词 */
                $this->options->keywords = $keywords;
                
                /** 设置分页 */
                $this->_pageRow = array('keywords' => $keywords);
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->options->feedUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedUrl);
                
                /** RSS 1.0 */
                $this->options->feedRssUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedAtomUrl);
                
                /** ATOM 1.0 */
                $this->options->feedAtomUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedAtomUrl);
                
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
        if($hasPushed)
        {
            return;
        }
        
        /** 仅输出文章 */
        $select->where('table.contents.`type` = ?', 'post');
        $this->_countSql = clone $select;

        $select->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return void
     */
    public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...')
    {
        if(!_p(__FILE__, 'Action')->pageNav($prev, $next, $splitPage, $splitWord))
        {
            $query = Typecho_Router::url(Typecho_Router::$current . 
            (false === strpos(Typecho_Router::$current, '_page') ? '_page' : NULL),
            $this->_pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->size($this->_countSql), $this->_currentPage, $this->_pageSize, $query);
            $nav->render($prev, $next, $splitPage, $splitWord);
        }
    }
    
    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function singlePush($value)
    {
        $value['tags'] = $this->db->fetchAll($this->db->sql()
        ->select('table.metas')->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $value['cid'])
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`'), array($this->abstractMetasWidget, 'filter'));
        
        if(!empty($value['template']))
        {
            /** 应用自定义模板 */
            $this->_themeFile = $value['template'];
        }
        
        return parent::push($value);
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
        if(NULL == $this->password || 
        $this->password == Typecho_Request::getParameter('protect_password', Typecho_Request::getCookie('protect_password')))
        {
            $mode = strtolower($mode);
            switch($mode)
            {
                case 'comment':
                    return Typecho_API::factory('Widget_Comments_Archive_Comment', $this->cid, $desc);
                case 'trackback':
                    return Typecho_API::factory('Widget_Comments_Archive_Trackback', $this->cid, $desc);
                case 'pingback':
                    return Typecho_API::factory('Widget_Comments_Archive_Pingback', $this->cid, $desc);
                default:
                    return Typecho_API::factory('Widget_Comments_Archive', $this->cid, $desc);
            }
        }
        else
        {
            return Typecho_API::factory('Widget_Abstract_Comments');
        }
    }
    
    /**
     * 输出文章内容,支持隐私项输出
     *
     * @access public
     * @param string $more 文章截取后缀
     * @return void
     */
    public function content($more = NULL)
    {
        if(NULL == $this->password || 
        $this->password == Typecho_Request::getParameter('protect_password', Typecho_Request::getCookie('protect_password')))
        {
            $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
            $contents = explode('<!--more-->', $content);
            
            list($abstract) = $contents;
            echo empty($more) ? $content : Typecho_API::fixHtml($abstract) . (count($contents) > 1 ? '<p class="more"><a href="'
            . $this->permalink . '">' . $more . '</a></p>' : NULL);
        }
        else
        {
            echo '<form class="protected" action="' . $this->permalink . '" method="post">' .
            '<p class="word">' . _t('请输入密码访问') . '</p>' .
            '<p><input type="password" class="text" name="protect_password" />
            <input type="submit" class="submit" value="' . _t('提交') . '" /></p>' .
            '</form>';
        }
    }
    
    /**
     * 输出标题,支持隐私项输出
     * 
     * @access public
     * @return void
     */
    public function title()
    {
        echo NULL == $this->password || 
        $this->password == Typecho_Request::getParameter('protect_password', Typecho_Request::getCookie('protect_password'))
        ? $this->title : _t('此文章被密码保护');
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
        $content = $this->db->fetchRow($this->select()->where('table.contents.`created` > ? AND table.contents.`created` < ?',
        $this->created, $this->options->gmtTime)
        ->where('table.contents.`type` = ?', $this->type)
        ->where('table.contents.`password` IS NULL')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_ASC)
        ->limit(1));
        
        if($content)
        {
            $content = $this->filter($content);
            $link = '<a href="' . $content['permalink'] . '" title="' . $content['title'] . '">' . $content['title'] . '</a>';
            printf($format, $link);
        }
        else
        {
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
        $content = $this->db->fetchRow($this->select()->where('table.contents.`created` < ?', $this->created)
        ->where('table.contents.`type` = ?', $this->type)
        ->where('table.contents.`password` IS NULL')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
        ->limit(1));
        
        if($content)
        {
            $content = $this->filter($content);
            $link = '<a href="' . $content['permalink'] . '" title="' . $content['title'] . '">' . $content['title'] . '</a>';
            printf($format, $link);
        }
        else
        {
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
        $this->tags = isset($this->tags) ? $this->tags : $this->db->fetchAll($this->db->sql()
        ->select('table.metas')->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $this->cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`'), array($this->abstractMetasWidget, 'filter'));
        
        return Typecho_API::factory('Widget_Contents_Related', $this->cid, $this->type, $this->tags, $limit);
    }
    
    /**
     * 输出头部元数据
     * 
     * @access public
     * @return void
     */
    public function header()
    {
        $header = new Typecho_Widget_Helper_HtmlHeader();
        $header->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'description', 'content' => $this->options->description)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'keywords', 'content' => $this->options->keywords)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'generator', 'content' => $this->options->generator)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'template', 'content' => $this->options->theme)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'pingback', 'href' => $this->options->xmlRpcUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'EditURI', 'type' => 'application/rsd+xml', 'title' => 'RSD', 'href' => $this->options->xmlRpcUrl . '?rsd')))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'wlwmanifest', 'type' => 'application/wlwmanifest+xml',
        'href' => Typecho_API::pathToUrl('wlwmanifest.xml', $this->options->adminUrl))))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0', 'href' => $this->options->feedUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 1.0', 'href' => $this->options->feedRssUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'ATOM 1.0', 'href' => $this->options->feedAtomUrl)));
        
        /** 插件支持 */
        _p(__FILE__, 'Action')->header($header);
        
        /** 输出header */
        $header->render();
    }
    
    /**
     * 根据别名获取widget对象
     * 
     * @access public
     * @param string $alias 组件别名
     * @return Typecho_Widget
     */
    public function widget($alias)
    {
        $args = func_get_args();
        $args[0] = str_replace('/', '_', $alias);
        return call_user_func_array(array('Typecho_API', 'factory'), $args);
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
        echo Typecho_Request::getCookie($cookieName);
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
        header('X-Pingback:' . $this->options->xmlRpcUrl);
    
        /** 输出模板 */
        require_once __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/' . $this->_themeFile;
        
        /** 挂接插件 */
        _p(__FILE__, 'Action')->render($this);
    }
}