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
     * 获取反序列化的meta数据
     * 
     * @access protected
     * @return array
     */
    protected function ___metaData()
    {
        return unserialize($this->meta);
    }

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
    public function size(Typecho_Db_Query $condition)
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
    
    /**
     * 获取用户meta信息
     * 
     * @access public
     * @param string $metaName meta名称
     * @param string $key meta索引
     * @return mixed
     */
    public function meta($metaName, $key = NULL)
    {
        $meta = $this->metaData;
        $result = NULL;
        
        if (isset($meta[$metaName])) {
            $result = $meta[$metaName];
        } else {
            $meta = $this->options->plugin($metaName);
            $result = isset($meta->personalConfig) ? $meta->personalConfig : NULL;
        }
        
        if (NULL !== $key && is_array($result)) {
            return isset($result[$key]) ? $result[$key] : NULL;
        } else {
            return $result;
        }
    }
}
