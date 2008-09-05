<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/**
 * 表单修饰器接口
 * 
 * @category typecho
 * @package Typecho_Widget_Helper_Header
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
interface Typecho_Widget_Helper_Decorator_Form
{
    /**
     * 创建表单标题
     * 
     * @access public
     * @param string $value 标题字符串
     * @return Typecho_Widget_Helper_Decorator_Form
     */
    public function label($value);
    
    /**
     * 创建表单容器
     * 
     * @access public
     * @param Typecho_Widget_Helper_Layout $item 表单容器
     * @return void
     */
    public function container(Typecho_Widget_Helper_Layout $item);
    
    /**
     * 设置提示信息
     * 
     * @access public
     * @param string $message 提示信息
     * @return Typecho_Widget_Helper_Decorator_Form
     */
    public function message($message);
    
    /**
     * 设置描述信息
     * 
     * @access public
     * @param string $description 描述信息
     * @return Typecho_Widget_Helper_Decorator_Form
     */
    public function description($description);
}
