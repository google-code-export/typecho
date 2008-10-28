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

/** Typecho_Widget_Helper_Form_Element */
require_once 'Typecho/Widget/Helper/Form/Element.php';

/**
 * 文字输入表单项帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Element_Submit extends Typecho_Widget_Helper_Form_Element
{
    /**
     * 初始化当前输入项
     * 
     * @access public
     * @param Typecho_Widget_Helper_Layout $container 容器对象
     * @param string $name 表单元素名称
     * @param array $options 选择项
     * @return Typecho_Widget_Helper_Layout
     */
    public function input(Typecho_Widget_Helper_Layout $container, $name = NULL, array $options = NULL)
    {
        $input = new Typecho_Widget_Helper_Layout('input', array('type' => 'button'));
        $container->addItem($input);
        return $input;
    }
    
    /**
     * 设置表单元素值
     * 
     * @access protected
     * @param mixed $value 表单元素值
     * @return void
     */
    protected function _value($value)
    {
        $this->input->setAttribute('value', $value);
    }
}