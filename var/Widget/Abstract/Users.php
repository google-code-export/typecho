<?php
/**
 * 用户抽象组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 用户抽象类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Abstract_Users extends Widget_Abstract
{
    /**
     * 用户组
     *
     * @access public
     * @var array
     */
    public $groups = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2,
            'subscriber'	=> 3,
            'visitor'		=> 4
            );

    /**
     * 查询方法
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select()->from('table.users');
    }
    
    /**
     * 获得所有记录数
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function count(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select(array('COUNT(uid)' => 'num'))->from('table.users'))->num;
    }
    
    /**
     * 增加记录方法
     * 
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    public function insert(array $rows)
    {
        return $this->db->query($this->db->insert('table.users')->rows($rows));
    }
    
    /**
     * 更新记录方法
     * 
     * @access public
     * @param array $rows 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function update(array $rows, Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->update('table.users')->rows($rows));
    }
    
    /**
     * 删除记录方法
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.users'));
    }
}
