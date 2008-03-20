<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Widget.php 48 2008-03-16 02:51:40Z magike.net $
 */

/**
 * 提示框组件
 * 
 * @package Notice
 */
class Notice extends TypechoWidget
{
    /**
     * 列表显示所有提示内容
     * 
     * @access public
     * @param string $tag 列表html标签
     * @return void
     */
    public function lists($tag = 'li')
    {
        foreach($this->_row as $row)
        {
            echo "<$tag>" . $row . "</$tag>";
        }
    }
    
    /**
     * 显示相应提示字段
     * 
     * @access public
     * @param string $name 字段名称
     * @param string $format 字段格式
     * @return void
     */
    public function display($name, $format = '%s')
    {
        echo empty($this->_row[$name]) ? NULL : 
        ((false === strpos($format, '%s')) ? $format : sprintf($format, $this->_row[$name]));
    }
    
    /**
     * 设定堆栈每一行的值
     *
     * @param string $name 值对应的键值
     * @param mixed $name 相应的值
     * @return array
     */
    public function set($name, $value = NULL)
    {
        if(is_array($name))
        {
            foreach($name as $key => $row)
            {
                $_SESSION['notice'][$key] = $row;
            }
        }
        else
        {
            if(empty($value))
            {
                $_SESSION['notice'][] = $name;
                
            }
            else
            {
                $_SESSION['notice'][$name] = $value;
            }
        }
    }
    
    /**
     * 入口函数初始化session
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        if(empty($_SESSION['notice']))
        {
            $_SESSION['notice'] = array();
            $session = $_SESSION['notice'];
        }
        else
        {
            $session = $_SESSION['notice'];
            session_unregister('notice');
        }
        
        $this->push($session);
    }
}
