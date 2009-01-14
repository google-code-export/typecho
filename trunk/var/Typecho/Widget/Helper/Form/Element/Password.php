<?php
/**
 * 密码输入表单项帮手
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
 * 密码输入表单项帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Element_Password extends Typecho_Widget_Helper_Form_Element
{
    /**
     * 初始化当前输入项
     * 
     * @access public
     * @param string $name 表单元素名称
     * @param array $options 选择项
     * @return Typecho_Widget_Helper_Layout
     */
    public function input($name = NULL, array $options = NULL)
    {
        $input = new Typecho_Widget_Helper_Layout('input', array('id' => $name . '-0', 
        'name' => $name, 'type' => 'password', 'class' => 'password'));
        $this->label->setAttribute('for', $name . '-0');
        $this->container($input);
        return $input;
    }
    
    /**
     * 设置表单项默认值
     * 
     * @access protected
     * @param string $value 表单项默认值
     * @return void
     */
    protected function _value($value)
    {
        $this->input->setAttribute('value', $value);
    }
}