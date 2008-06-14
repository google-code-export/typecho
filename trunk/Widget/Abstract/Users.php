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
class Widget_Abstract_Users extends Typecho_Widget_Dataset
{
    /**
     * 用户组
     *
     * @access public
     * @var array
     */
    public $groups;

    /**
     * 构造函数,初始化用户组
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->group = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2,
            'subscriber'	=> 3,
            'visitor'		=> 4
        );
    }

    /**
     * 查询方法
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->sql()->select('table.users');
    }
    
    /**
     * 获得所有记录数
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select('table.users', 'COUNT(`uid`) AS `num`'))->num;
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
        return $this->db->query($this->db->sql()->insert('table.users')->rows($rows));
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
        return $this->db->query($condition->update('table.comments')->rows($options));
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
        return $this->db->query($condition->delete('table.comments'));
    }
}
