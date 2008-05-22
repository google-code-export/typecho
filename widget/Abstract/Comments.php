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
 * 评论基类
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class CommentsWidget extends TypechoWidget
{
    /**
     * 实例化的抽象Meta类
     * 
     * @access private
     * @var MetasWidget
     */
    private $abstractContentsWidget;

    /**
     * 分页数目
     *
     * @access protected
     * @var integer
     */
    protected $pageSize;

    /**
     * 当前页
     *
     * @access protected
     * @var integer
     */
    protected $currentPage;

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
     * 公用的选择器
     * 
     * @access protected
     * @var TypechoDbQuery
     */
    protected $selectSql;

    /**
     * 用于计算总数的sql对象
     * 
     * @access protected
     * @var TypechoDbQuery
     */
    protected $countSql;

    /**
     * 过滤器名称
     *
     * @access protected
     * @var string
     */
    protected $filterName;

    /**
     * 构造函数,初始化数据库
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 初始化数据库 */
        $this->db = TypechoDb::get();
        
        /** 初始化常用widget */
        $this->options = Typecho::widget('Options');
        $this->access = Typecho::widget('Access');
        $this->abstractContentsWidget = Typecho::widget('Abstract.Contents');
        
        /** 初始化过滤器名称 */
        $this->filterName = TypechoPlugin::name(__FILE__);
        
        /** 初始化共用选择器 */
        $this->selectSql = $this->db->sql()->select('table.comments', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.comments.`coid`, table.comments.`created` AS `date`, table.comments.`author`, table.comments.`mail`, table.comments.`url`, table.comments.`ip`,
        table.comments.`agent`, table.comments.`text`, table.comments.`type`, table.comments.`status`, table.comments.`parent`')
        ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`');
        
        /** 初始化分页变量 */
        $this->pageSize = 20;
        $this->currentPage = TypechoRequest::getParameter('page') ? 1 : TypechoRequest::getParameter('page');
    }
    
    /**
     * 通用过滤器
     * 
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value)
    {
        /** 取出所有分类 */
        $value = $this->abstractContentsWidget->filter($value);
        
        $value['permalink'] = $value['permalink'] . '#comments-' . $value['coid'];
        TypechoPlugin::callFilter($this->filterName, $value);
        
        return $value;
    }

    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        $value = $this->filter($value);
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
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        /** Just Return */
        return;
    }
}
