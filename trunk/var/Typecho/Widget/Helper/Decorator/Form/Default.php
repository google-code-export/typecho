<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/** Typecho_Widget_Helper_Layout */
require_once 'Typecho/Widget/Helper/Layout.php';

/** Typecho_Widget_Helper_Decorator_Form */
require_once 'Typecho/Widget/Helper/Decorator/Form.php';

/**
 * 表单默认修饰器
 * 
 * @category typecho
 * @package Typecho_Widget_Helper_Header
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Decorator_Form_Default extends Typecho_Widget_Helper_Layout implements Typecho_Widget_Helper_Decorator_Form
{
    /**
     * 表单元素容器
     * 
     * @access private
     * @var Typecho_Widget_Helper_Layout
     */
    private $container;

    /**
     * 入口函数,构造layout元素
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 创建html元素,并设置class */
        parent::__construct('div', array('class' => 'typecho-option-item'));
    }
    
    /**
     * 创建表单标题
     * 
     * @access public
     * @param string $value 标题字符串
     * @return void
     */
    public function label($value)
    {
        /** 创建标题元素 */
        $title = new Typecho_Widget_Helper_Layout('h4');
        $title->html($value);
        $this->addItem($title);
    }
    
    /**
     * 创建表单容器
     * 
     * @access public
     * @param Typecho_Widget_Helper_Layout $item 表单容器
     * @return unknown
     */
    public function container(Typecho_Widget_Helper_Layout $item)
    {
        /** 创建表单容器 */
        $this->container = new Typecho_Widget_Helper_Layout('p');
        $this->container->addItem($item);
    }
    
    /**
     * 设置提示信息
     * 
     * @access public
     * @param string $message 提示信息
     * @return void
     */
    public function message($message)
    {
        $item = new Typecho_Widget_Helper_Layout('span', array('class' => 'typecho-option-item-message'));
        $item->html($message);
        $this->container->addItem($item);
    }
    
    /**
     * 设置描述信息
     * 
     * @access public
     * @param string $description 描述信息
     * @return void
     */
    public function description($description)
    {
        /** 创建描述元素 */
        $item = new Typecho_Widget_Helper_Layout('p', array('class' => 'summary'));
        $item->html($description);
        $this->container->addItem($item);
    }
}
