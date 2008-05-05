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
 * @package Widget
 */
class NoticeWidget extends TypechoWidget
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
        $notice = array();
        
        if(is_array($name))
        {
            foreach($name as $key => $row)
            {
                $notice[$key] = $row;
            }
        }
        else
        {
            if(empty($value))
            {
                $notice[] = $name;
            }
            else
            {
                $notice[$name] = $value;
            }
        }
        
        TypechoRequest::setCookie('notice', $notice, Typecho::widget('Options')->gmtTime + Typecho::widget('Options')->timezone,
        Typecho::widget('Options')->siteURL);
    }

    /**
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        if(NULL !== TypechoRequest::getCookie('notice'))
        {
            $this->push(TypechoRequest::getCookie('notice'));
            TypechoRequest::deleteCookie('notice', Typecho::widget('Options')->siteURL);
        }
    }
}
