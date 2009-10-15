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
     * 判断用户名称是否存在
     * 
     * @access public
     * @param string $name 用户名称
     * @return boolean
     */
    public function nameExists($name)
    {
        $select = $this->db->select()
        ->from('table.users')
        ->where('name = ?', $name)
        ->limit(1);
        
        if ($this->request->uid) {
            $select->where('uid <> ?', $this->request->uid);
        }

        $user = $this->db->fetchRow($select);
        return $user ? false : true;
    }
    
    /**
     * 判断电子邮件是否存在
     * 
     * @access public
     * @param string $mail 电子邮件
     * @return boolean
     */
    public function mailExists($mail)
    {
        $select = $this->db->select()
        ->from('table.users')
        ->where('mail = ?', $mail)
        ->limit(1);
        
        if ($this->request->uid) {
            $select->where('uid <> ?', $this->request->uid);
        }

        $user = $this->db->fetchRow($select);
        return $user ? false : true;
    }
    
    /**
     * 判断用户昵称是否存在
     * 
     * @access public
     * @param string $screenName 昵称
     * @return boolean
     */
    public function screenNameExists($screenName)
    {
        $select = $this->db->select()
        ->from('table.users')
        ->where('screenName = ?', $screenName)
        ->limit(1);
        
        if ($this->request->uid) {
            $select->where('uid <> ?', $this->request->uid);
        }
    
        $user = $this->db->fetchRow($select);
        return $user ? false : true;
    }

    /**
     * 获取页面偏移
     * 
     * @access protected
     * @param string $column 字段名
     * @param integer $offset 偏移值
     * @param string $group 用户组
     * @param integer $pageSize 分页值
     * @return integer
     */
    protected function getPageOffset($column, $offset, $group = NULL, $pageSize = 20)
    {
        $select = $this->db->select(array('COUNT(uid)' => 'num'))->from('table.users')
        ->where("table.users.{$column} > {$offset}");
        
        if (!empty($group)) {
            $select->where('table.users.group = ?', $group);
        }
        
        $count = $this->db->fetchObject($select)->num + 1;
        return ceil($count / $pageSize);
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
        //生成静态链接
        $routeExists = (NULL != Typecho_Router::get('author'));
        
        $value['permalink'] = $routeExists ? Typecho_Router::url('author', $value, $this->options->index) : '#';
        
        /** 生成聚合链接 */
        /** RSS 2.0 */
        $value['feedUrl'] = $routeExists ? Typecho_Router::url('author', $value, $this->options->feedUrl) : '#';
        
        /** RSS 1.0 */
        $value['feedRssUrl'] = $routeExists ? Typecho_Router::url('author', $value, $this->options->feedRssUrl) : '#';
        
        /** ATOM 1.0 */
        $value['feedAtomUrl'] = $routeExists ? Typecho_Router::url('author', $value, $this->options->feedAtomUrl) : '#';

        $value = $this->pluginHandle(__CLASS__)->filter($value, $this);
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
}
