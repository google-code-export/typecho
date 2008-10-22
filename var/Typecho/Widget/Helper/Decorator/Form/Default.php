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
abstract class Typecho_Widget_Helper_Decorator_Form_Default extends Typecho_Widget_Helper_Layout implements Typecho_Widget_Helper_Decorator_Form
{
    /**
     * 表单标题
     * 
     * @access private
     * @var string
     */
    private $_label;
    
    /**
     * 表单描述
     * 
     * @access private
     * @var string
     */
    private $_description;
    
    /**
     * 表单消息
     * 
     * @access private
     * @var string
     */
    private $_message;

    /**
     * 表单元素容器
     * 
     * @access protected
     * @var Typecho_Widget_Helper_Layout
     */
    protected $container;

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
        
        /** 创建表单容器 */
        $this->container = new Typecho_Widget_Helper_Layout('p');
        
        /** 运行自定义初始函数 */
        $this->init();
    }
    
    /**
     * 自定义初始函数
     * 
     * @access public
     * @return void
     */
    public function init(){}
    
    /**
     * 创建表单标题
     * 
     * @access public
     * @param string $value 标题字符串
     * @return Typecho_Widget_Helper_Decorator_Form_Default
     */
    public function label($value)
    {
        /** 创建标题元素 */
        if(empty($this->_label))
        {
            $this->_label = new Typecho_Widget_Helper_Layout('h4');
            $this->addItem($this->_label);
        }

        $this->_label->html($value);
        return $this;
    }
    
    /**
     * 设置提示信息
     * 
     * @access public
     * @param string $message 提示信息
     * @return Typecho_Widget_Helper_Decorator_Form_Default
     */
    public function message($message)
    {
        if(empty($this->_message))
        {
            $this->_message =  new Typecho_Widget_Helper_Layout('span', array('class' => 'typecho-option-item-message'));
            $this->container->addItem($this->_message);
        }
        
        $this->_message->html($message);
        return $this;
    }
    
    /**
     * 设置描述信息
     * 
     * @access public
     * @param string $description 描述信息
     * @return Typecho_Widget_Helper_Decorator_Form_Default
     */
    public function description($description)
    {
        /** 创建描述元素 */
        if(empty($this->_description))
        {
            $this->_description = new Typecho_Widget_Helper_Layout('p', array('class' => 'summary')) : $this->_description;
            $this->container->addItem($this->_description);
        }
        
        $this->_description->html($description);
        return $this;
    }
}
