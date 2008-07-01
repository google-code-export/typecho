<?php
/**
 * 文字输入表单项帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Widget_Helper_Form_Abstract */
require_once 'Typecho/Widget/Helper/Layout.php';

/**
 * 文字输入表单项帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Submit extends Typecho_Widget_Helper_Layout
{
    /**
     * 提交按钮
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $button;

    /**
     * 重载构造函数
     * 
     * @access public
     * @param string $title 按钮标题
     * @return void
     */
    public function __construct($title = NULL)
    {
        parent::__construct('div');
        $this->button = new Typecho_Widget_Helper_Layout('input');
        $this->button->setAttribute('type', 'submit')->setAttribute('value', $title);
        $this->addItem($this->button);
    }
    
    /**
     * 设置表单项默认值
     * 
     * @access public
     * @param string $value 表单项默认值
     * @return Typecho_Widget_Helper_Form_Submit
     */
    public function value($value)
    {
        $this->value = $value;
        $this->button->setAttribute('value', $value);
        return $this;
    }
}
