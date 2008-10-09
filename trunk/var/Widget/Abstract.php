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
     * 获取全局选项
     * 
     * @access public
     * @return Widget_Options
     */
    public function options()
    {
        return $this->widget('Widget_Options');
    }
    
    /**
     * 获取notice组件
     * 
     * @access public
     * @return Widget_Notice
     */
    public function notice()
    {
        return $this->widget('Widget_Notice');
    }
    
    /**
     * 获取用户支持
     * 
     * @access public
     * @return Widget_User
     */
    public function user()
    {
        return $this->widget('Widget_User');
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

