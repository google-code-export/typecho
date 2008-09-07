<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Widget.php 48 2008-03-16 02:51:40Z magike.net $
 */

/**
 * 提示框组件
 *
 * @package Widget
 */
class Widget_Notice extends Typecho_Widget
{
    /**
     * 提示类型
     * 
     * @access public
     * @var string
     */
    public $noticeType = 'notice';
    
    public function __construct()
    {
        if(NULL !== Typecho_Request::getCookie('notice'))
        {
            $this->noticeType = Typecho_Request::getCookie('noticeType');
            $this->push(Typecho_Request::getCookie('notice'));
            Typecho_Request::deleteCookie('notice', Typecho_API::factory('Widget_Options')->siteUrl);
            Typecho_Request::deleteCookie('noticeType', Typecho_API::factory('Widget_Options')->siteUrl);
        }
    }
    
    /**
     * 输出提示类型
     * 
     * @access public
     * @return void
     */
    public function noticeType()
    {
        echo $this->noticeType;
    }

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
     * @param string $type 提示类型
     * @return array
     */
    public function set($name, $value = NULL, $type = 'notice')
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
        
        Typecho_Request::setCookie('notice', $notice, Typecho_API::factory('Widget_Options')->gmtTime + Typecho_API::factory('Widget_Options')->timezone + 86400,
        Typecho_API::factory('Widget_Options')->siteUrl);
        Typecho_Request::setCookie('noticeType', $type, Typecho_API::factory('Widget_Options')->gmtTime + Typecho_API::factory('Widget_Options')->timezone + 86400,
        Typecho_API::factory('Widget_Options')->siteUrl);
    }
}
