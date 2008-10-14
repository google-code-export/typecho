<?php
/**
 * 纯数据抽象组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 纯数据抽象组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
abstract class Widget_Abstract extends Typecho_Widget
{
    /**
     * 全局选项
     * 
     * @access protected
     * @var Widget_Options
     */
    protected $options;

    /**
     * 用户对象
     * 
     * @access protected
     * @var Widget_User
     */
    protected $user;
    
    /**
     * 构造函数
     * 
     * @access public
     * @param mixed $params 传递的参数
     * @return void
     */
    public function __construct($params = array())
    {
        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        $this->user = $this->widget('Widget_User');
    
        /** 初始化参数 */
        parent::__construct($params);
    }
    
    /**
     * 查询方法
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    abstract public function select();
    
    /**
     * 获得所有记录数
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    abstract public function count(Typecho_Db_Query $condition);
    
    /**
     * 增加记录方法
     * 
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    abstract public function insert(array $rows);
    
    /**
     * 更新记录方法
     * 
     * @access public
     * @param array $rows 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    abstract public function update(array $rows, Typecho_Db_Query $condition);
    
    /**
     * 删除记录方法
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    abstract public function delete(Typecho_Db_Query $condition);
}

