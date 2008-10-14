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
    
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init(Typecho_Widget_Request $request, Typecho_Widget_Response $response, Typecho_Config $parameter)
    {
        if(NULL !== $request->getCookie('notice'))
        {
            $this->noticeType = $request->getCookie('noticeType');
            $this->push($request->getCookie('notice'));
            $request->deleteCookie('notice', $this->options()->siteUrl);
            $request->deleteCookie('noticeType', $this->options()->siteUrl);
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
        
        $this->request()->setCookie('notice', $notice, $this->options()->gmtTime + $this->options()->timezone + 86400,
        $this->options()->siteUrl);
        $this->request()->setCookie('noticeType', $type, $this->options()->gmtTime + $this->options()->timezone + 86400,
        $this->options()->siteUrl);
    }
}
