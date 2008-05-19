<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 内容的文章基类
 * 定义的css类
 * p.more:阅读全文链接所属段落
 *
 * @package Widget
 */
class PostsWidget extends TypechoWidget
{
    /**
     * 分类列表
     * 
     * @access protected
     * @var array
     */
    protected $_categories = array();

    /**
     * 分页数目
     *
     * @access protected
     * @var integer
     */
    protected $_pageSize;

    /**
     * 当前页
     *
     * @access protected
     * @var integer
     */
    protected $_currentPage;

    /**
     * 数据库对象
     *
     * @access protected
     * @var TypechoDb
     */
    protected $db;

    /**
     * 实例化的配置对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $options;

    /**
     * 实例化的权限对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $access;

    /**
     * 过滤器名称
     *
     * @access private
     * @var string
     */
    private $_filterName;
    
    /**
     * 用于计算总数的sql对象
     * 
     * @access private
     * @var TypechoDbQuery
     */
    public $countSql;

    /**
     * 构造函数,初始化数据库
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->db = TypechoDb::get();
        $this->options = Typecho::widget('Options');
        $this->access = Typecho::widget('Access');
        $this->_filterName = TypechoPlugin::name(__FILE__);
    }

    /**
     * 输出内容分页
     *
     * @access public
     * @param string $class 分页类型
     * @return void
     */
    public function pageNav($class)
    {
        $args = func_get_args();

        $num = $this->db->fetchObject($this->countSql->select('table.contents', 'COUNT(table.contents.`cid`) AS `num`'))->num;
        $nav = new TypechoWidgetNavigator($num,
                                          $this->_currentPage,
                                          $this->_pageSize,
                                          TypechoRoute::parse(TypechoRoute::$current . '_page', $this->_row, $this->options->index));

        call_user_func_array(array($nav, 'make'), $args);
    }

    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push($value)
    {
        /** 生成分类 */
        $this->_categories[$value['cid']] = $this->db->fetchAll($this->db->sql()
        ->select('table.metas')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $value['cid'])
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')
        ->order('sort', 'ASC'));
        
        $value['category'] = implode('+', Typecho::arrayFlatten($this->_categories[$value['cid']], 'slug'));
    
        //生成日期
        $value['year'] = date('Y', $value['created'] + $this->options->timezone);
        $value['month'] = date('n', $value['created'] + $this->options->timezone);
        $value['day'] = date('j', $value['created'] + $this->options->timezone);

        //生成静态链接
        $type = $value['type'];
        $routeExists = isset(TypechoConfig::get('Route')->$type);
        
        $value['permalink'] = $routeExists ? TypechoRoute::parse($type, $value, $this->options->index) : '#';
        
        /** 生成聚合链接 */
        $value['feedUrl'] = $routeExists ? TypechoRoute::parse('feed', 
        array('feed' => TypechoRoute::parse($type, $value)), $this->options->index) : '#';

        TypechoPlugin::callFilter($this->_filterName, $value);
        return parent::push($value);
    }

    /**
     * 输出文章发布日期
     *
     * @access public
     * @param string $format 日期格式
     * @return void
     */
    public function date($format)
    {
        echo date($format, $this->created + $this->options->timezone);
    }
    
    /**
     * 输出词义化日期
     * 
     * @access public
     * @return void
     */
    public function dateWord()
    {
        echo TypechoI18n::dateWord($this->created + $this->options->timezone, $this->options->gmtTime + $this->options->timezone);
    }

    /**
     * 输出文章评论提交地址
     *
     * @access public
     * @return void
     */
    public function commentsPostUrl()
    {
        printf(TypechoRoute::parse('do', array('do' => 'CommentsPost'), $this->options->index) . '?%d.%d',
        $this->cid, $this->created);
    }

    /**
     * 输出文章引用通告地址
     *
     * @access public
     * @return void
     */
    public function trackbackUrl()
    {
        printf(TypechoRoute::parse('do', array('do' => 'Trackback'), $this->options->index) . '?%d.%d',
        $this->cid, $this->created);
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
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        list($abstract) = explode('<!--more-->', $content);
        echo Typecho::fixHtml($abstract) . ($more ? '<p class="more"><a href="'
        . $this->permalink . '">' . $more . '</a></p>' : NULL);
    }

    /**
     * 输出文章摘要
     *
     * @access public
     * @param integer $length 摘要截取长度
     * @return void
     */
    public function excerpt($length = 100)
    {
        echo Typecho::subStr(Typecho::stripTags($this->text), 0, $length);
    }

    /**
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @param string $tag 评论链接锚点
     * @return void
     */
    public function commentsNum($string = 'Comments %d')
    {
        $args = func_get_args();
        $num = intval($this->commentsNum);
        
        echo '<a href="' . $this->permalink . '#comments">' . 
        sprintf(isset($args[$num]) ? $args[$num] : array_pop($args), $num) . '</a>';
    }

    /**
     * 获取文章权限
     *
     * @access public
     * @param string $permission 权限
     * @return unknown
     */
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;

        foreach($permissions as $permission)
        {
            $allow &= ($this->_row['allow' . ucfirst($permission)] == 'enable');
        }

        return $allow;
    }

    /**
     * 输出文章静态地址
     *
     * @access public
     * @param string $anchor 文章锚点
     * @return void
     */
    public function permalink($anchor = NULL)
    {
        echo $this->permalink . $anchor;
    }

    /**
     * 输出文章分类
     *
     * @access public
     * @param string $split 多个分类之间分隔符
     * @param boolean $link 是否输出链接
     * @return void
     */
    public function category($split = ',', $link = true)
    {
        $categories = $this->_categories[$this->cid];
        if($categories)
        {
            $result = array();
            
            foreach($categories as $row)
            {
                $result[] = $link ? '<a href="' . TypechoRoute::parse('category', $row, $this->options->index) . '">'
                . $row['name'] . '</a>' : $row['name'];
            }

            echo implode($split, $result);
        }
        else
        {
            echo _t('没有归类');
        }
    }

    /**
     * 输出文章标签
     *
     * @access public
     * @param string $split 多个标签之间分隔符
     * @param boolean $link 是否输出链接
     * @return void
     */
    public function tags($split = ',', $link = true)
    {
        $tags = $this->db->fetchAll($this->db->sql()
        ->select('table.metas', 'table.metas.`name`, table.metas.`slug`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $this->cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`'));

        $result = array();
        foreach($tags as $tag)
        {
            $result[] = $link ? '<a href="' . TypechoRoute::parse('tag', $tag, $this->options->index) . '">'
            . $tag['name'] . '</a>' : $tag['name'];
        }

        echo implode($split, $result);
    }

    /**
     * 入口函数
     *
     * @access public
     * @param integer $pageSize 每页文章数
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $this->_pageSize = empty($pageSize) ? $this->options->pageSize : $pageSize;
        $this->_currentPage = TypechoRoute::getParameter('page') ? 1 : TypechoRoute::getParameter('page');
        
        $select = $this->db->sql()
        ->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, table.users.`screenName` AS `author`')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', TypechoDb::LEFT_JOIN)
        ->where('table.contents.`type` = ?', 'post')
        ->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime);

        if('tag' == TypechoRoute::$current)
        {
            $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('type = ?', 'tag')
            ->where('slug = ?', urlencode(TypechoRoute::getParameter('slug')))->limit(1));
            
            if(!$tag)
            {
                throw new TypechoWidgetException(_t('标签%s不存在', TypechoRoute::getParameter('slug')), TypechoException::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $tag['mid']);
            
            /** 设置关键词 */
            Typecho::header('meta', array('name' => 'keywords', 'content' => $tag['name']));
            
            /** 设置头部feed */
            /** RSS 2.0 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0',
            'href' => TypechoRoute::parse('feed', array('feed' => TypechoRoute::parse('tag', $tag)), $this->options->index)));
            
            /** RSS 0.92 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92',
            'href' => TypechoRoute::parse('feed', array('feed' => '/rss' .
            TypechoRoute::parse('tag', $tag)), $this->options->index)));
            
            /** Atom 0.3 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'Atom 0.3',
            'href' => TypechoRoute::parse('feed', array('feed' => '/atom' .
            TypechoRoute::parse('tag', $tag)), $this->options->index)));
        }
        else if('category' == TypechoRoute::$current)
        {
            $category = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('type = ?', 'category')
            ->where('slug = ?', TypechoRoute::getParameter('slug'))->limit(1));
            
            if(!$category)
            {
                throw new TypechoWidgetException(_t('分类不存在'), TypechoException::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $category['mid']);
            
            /** 设置关键词 */
            Typecho::header('meta', array('name' => 'keywords', 'content' => $category['name']));
            
            /** 设置头部feed */
            /** RSS 2.0 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0',
            'href' => TypechoRoute::parse('feed', array('feed' => TypechoRoute::parse('category', $category)), $this->options->index)));
            
            /** RSS 0.92 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92',
            'href' => TypechoRoute::parse('feed', array('feed' => '/rss' .
            TypechoRoute::parse('category', $category)), $this->options->index)));
            
            /** Atom 0.3 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'Atom 0.3',
            'href' => TypechoRoute::parse('feed', array('feed' => '/atom' .
            TypechoRoute::parse('category', $category)), $this->options->index)));
        }
        else
        {
            /** 设置关键词 */
            Typecho::header('meta', array('name' => 'keywords', 'content' => $this->options->keywords));
        
            /** 设置头部feed */
            /** RSS 2.0 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0',
            'href' => $this->options->feedUrl));
            
            /** RSS 0.92 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92',
            'href' => TypechoRoute::parse('feed', array('feed' => '/rss'), $this->options->index)));
            
            /** Atom 0.3 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'Atom 0.3',
            'href' => TypechoRoute::parse('feed', array('feed' => '/atom'), $this->options->index)));
        }
        
        $this->countSql = clone $select;

        $select->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
