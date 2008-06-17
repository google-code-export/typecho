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
    private $themeFile = 'index.php';
    
    /**
     * 分页计算对象
     * 
     * @access private
     * @var void
     */
    private $countSql;
    
    /**
     * header内容
     * 
     * @access public
     * @var void
     */
    public $header;

    /**
     * 入口函数
     *
     * @access public
     * @param integer $pageSize 每页文章数
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
    
        /** 初始化分页变量 */
        $this->pageSize = empty($pageSize) ? $this->options->pageSize : $pageSize;
        $this->currentPage = Typecho_Router::getParameter('page', 1);
        $hasPushed = false;
    
        $select = $this->select()->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime);

        if('post' == Typecho_Router::$current || 'page' == Typecho_Router::$current)
        {
            /** 如果是单篇文章或独立页面 */
            if(NULL !== Typecho_Router::getParameter('cid'))
            {
                $select->where('table.contents.`cid` = ?', Typecho_Router::getParameter('cid'));
            }
        
            if(NULL !== Typecho_Router::getParameter('slug'))
            {
                $select->where('table.contents.`slug` = ?', Typecho_Router::getParameter('slug'));
            }
            
            $select->where('table.contents.`type` = ?', Typecho_Router::$current)
            ->group('table.contents.`cid`')->limit(1);
            $post = $this->db->fetchRow($select, array($this, 'singlePush'));

            if($post && $post['category'] == Typecho_Router::getParameter('category', $post['category'])
            && $post['year'] == Typecho_Router::getParameter('year', $post['year'])
            && $post['month'] == Typecho_Router::getParameter('month', $post['month'])
            && $post['day'] == Typecho_Router::getParameter('day', $post['day']))
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
            }
            else
            {
                throw new Typecho_Widget_Exception('post' == Typecho_Router::$current ? _t('文章不存在') : _t('页面不存在'), Typecho_Exception::NOTFOUND);
            }
            
            /** 设置风格文件 */
            $this->themeFile = Typecho_Router::$current . '.php';
            $hasPushed = true;
        }
        else if('category' == Typecho_Router::$current || 'category_page' == Typecho_Router::$current)
        {
            /** 如果是分类 */
            $category = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('`type` = ?', 'category')
            ->where('`slug` = ?', Typecho_Router::getParameter('slug'))->limit(1),
            array($this->abstractMetasWidget, 'filter'));
            
            if(!$category)
            {
                throw new Typecho_Widget_Exception(_t('分类不存在'), Typecho_Exception::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $category['mid']);
            
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
            
            /** 设置风格文件 */
            $this->themeFile = 'archive.php';
        }
        else if('tag' == Typecho_Router::$current || 'tag_page' == Typecho_Router::$current)
        {
            /** 如果是标签 */
            $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('`type` = ?', 'tag')
            ->where('`slug` = ?', Typecho_Router::getParameter('slug'))->limit(1),
            array($this->abstractMetasWidget, 'filter'));
            
            if(!$tag)
            {
                throw new Typecho_Widget_Exception(_t('标签%s不存在', Typecho_Router::getParameter('slug')), Typecho_Exception::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $tag['mid']);
            
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
            
            /** 设置风格文件 */
            $this->themeFile = 'archive.php';
        }
        else if('archive_year' == Typecho_Router::$current || 'archive_month' == Typecho_Router::$current
        || 'archive_day' == Typecho_Router::$current)
        {
            /** 如果是按日期归档 */
            $year = Typecho_Router::getParameter('year');
            $month = Typecho_Router::getParameter('month');
            $day = Typecho_Router::getParameter('day');
            
            /** 如果按日归档 */
            if(!empty($year) && !empty($month) && !empty($day))
            {
                $from = mktime(0, 0, 0, $month, $day, $year) - $this->options->timezone;
                $to = mktime(23, 59, 59, $month, $day, $year) - $this->options->timezone;
                
                /** 设置标题 */
                $this->options->archiveTitle = _t('%s年%s月%s日', $year, $month, $day);
            }
            /** 如果按月归档 */
            else if(!empty($year) && !empty($month))
            {
                $from = mktime(0, 0, 0, $month, 1, $year) - $this->options->timezone;
                $to = mktime(23, 59, 59, $month, idate('t', $from), $year) - $this->options->timezone;
                
                /** 设置标题 */
                $this->options->archiveTitle = _t('%s年%s月', $year, $month);
            }
            /** 如果按年归档 */
            else if(!empty($year))
            {
                $from = mktime(0, 0, 0, 1, 1, $year) - $this->options->timezone;
                $to = mktime(23, 59, 59, 12, 31, $year) - $this->options->timezone;
                
                /** 设置标题 */
                $this->options->archiveTitle = _t('%s年', $year);
            }
            
            $select->where('table.contents.`created` >= ?', $from)
            ->where('table.contents.`created` <= ?', $to);
            
            /** 设置头部feed */
            $value = array('year' => $year, 'month' => $month, 'day' => $day);
            
            /** RSS 2.0 */
            $this->options->feedUrl = Typecho_Router::parse(Typecho_Router::$current, $value, $this->options->feedUrl);
            
            /** RSS 1.0 */
            $this->options->feedRssUrl = Typecho_Router::parse(Typecho_Router::$current, $value, $this->options->feedRssUrl);
            
            /** ATOM 1.0 */
            $this->options->feedAtomUrl = Typecho_Router::parse(Typecho_Router::$current, $value, $this->options->feedAtomUrl);
            
            /** 设置风格文件 */
            $this->themeFile = 'archive.php';
        }
        
        /** 如果已经提前压入则直接返回 */
        if($hasPushed)
        {
            return;
        }
        
        $this->countSql = clone $select;

        $select->where('table.contents.`type` = ?', 'post')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
        ->page($this->currentPage, $this->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = Typecho_Router::parse(Typecho_Router::$current . '_page', $this->_row, $this->options->index);
        
        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->size($this->countSql), $this->currentPage, $this->pageSize, $query);
        $nav->render(_t('上一页'), _t('下一页'));
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
            $this->themeFile = $value['template'];
        }
        
        return parent::push($value);
    }
    
    /**
     * 输出头部元数据
     * 
     * @access public
     * @return void
     */
    public function header()
    {
        $this->header->render();
    }
    
    /**
     * 输出视图
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->header = new Typecho_Widget_Helper_HtmlHeader();
        $this->header->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'description', 'content' => $this->options->description)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'keywords', 'content' => $this->options->keywords)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'generator', 'content' => $this->options->generator)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'template', 'content' => $this->options->theme)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('rel' => 'pingback', 'href' => $this->options->xmlRpcUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'EditURI', 'type' => 'application/rsd+xml', 'title' => 'RSD', 'href' => $this->options->xmlRpcUrl . '?rsd')))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'wlwmanifest', 'type' => 'application/wlwmanifest+xml',
        'href' => Typecho_API::pathToUrl('wlwmanifest.xml', $this->options->adminUrl))))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0', 'href' => $this->options->feedUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 1.0', 'href' => $this->options->feedRssUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'ATOM 1.0', 'href' => $this->options->feedAtomUrl)));
    
        /** 添加Pingback */
        header('X-Pingback:' . $this->options->xmlRpcUrl);
    
        require_once __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/' . $this->themeFile;
    }
}
