<?php
/**
 * 分类输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 分类输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class CategoriesWidget extends TypechoWidget
{
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

        return parent::push($value);
    }
    
    /**
     * 初始化数据
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->db->fetchAll($this->db->sql()->select('table.metas')->where('`type` = ?', 'category')
        ->order('table.metas.`sort`', TypechoDb::SORT_ASC), array($this, 'push'));
    }
}
