<?php
/**
 * 描述记录输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 描述记录输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class MetasWidget extends TypechoWidget
{
    /**
     * 数据库对象
     *
     * @access protected
     * @var TypechoDb
     */
    protected $db;
    
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
     * 用于计算总数的sql对象
     * 
     * @access private
     * @var TypechoDbQuery
     */
    public $countSql;
    
    /**
     * 过滤器名称
     *
     * @access private
     * @var string
     */
    private $_filterName;
    
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
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push($value)
    {
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
     * 输出内容分页
     *
     * @access public
     * @param string $class 分页类型
     * @return void
     */
    public function pageNav($class)
    {
        $args = func_get_args();

        $num = $this->db->fetchObject($this->countSql->select('table.metas', 'COUNT(table.metas.`mid`) AS `num`'))->num;
        $nav = new TypechoWidgetNavigator($num,
                                          $this->_currentPage,
                                          $this->_pageSize,
                                          TypechoRoute::parse(TypechoRoute::$current . '_page', $this->_row, $this->options->index));

        call_user_func_array(array($nav, 'make'), $args);
    }

    /**
     * 入口函数
     * 
     * @access public
     * @param string $type 类型
     * @param string $pageSize 分页参数,为0时表示不分页
     * @return unknown
     */
    public function render($type = 'category', $pageSize = 0)
    {
        $select = $this->db->sql()->select('table.metas')->where('type = ?', $type)
        ->order('table.metas.`sort`', TypechoDb::SORT_ASC);
        $this->countSql = clone $select;
        
        if($pageSize > 0)
        {
            $this->_pageSize = $pageSize;
            $this->_currentPage = TypechoRequest::getParameter('page') ? 1 : TypechoRequest::getParameter('page');
            $select->page($this->_currentPage, $this->_pageSize);
        }
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
