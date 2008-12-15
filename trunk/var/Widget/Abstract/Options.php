<?php
/**
 * 全局选项
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 全局选项组件
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Abstract_Options extends Widget_Abstract
{    
    /**
     * 获取原始查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select()->from('table.options');
    }
    
    /**
     * 插入一条记录
     * 
     * @access public
     * @param array $options 记录插入值
     * @return integer
     */
    public function insert(array $options)
    {
        return $this->db->query($this->db->insert('table.options')->rows($options));
    }
    
    /**
     * 更新记录
     * 
     * @access public
     * @param array $options 记录更新值
     * @param Typecho_Db_Query $condition 更新条件
     * @return integer
     */
    public function update(array $options, Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->update('table.options')->rows($options));
    }
    
    /**
     * 删除记录
     * 
     * @access public
     * @param Typecho_Db_Query $condition 删除条件
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.options'));
    }
    
    /**
     * 获取记录总数
     * 
     * @access public
     * @param Typecho_Db_Query $condition 计算条件
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select(array('COUNT(name)' => 'num'))->from('table.options'))->num;
    }
}
