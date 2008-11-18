<?php
/**
 * 单选框帮手
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
 * 单选框帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Element_Radio extends Typecho_Widget_Helper_Form_Element
{
    /**
     * 选择值
     * 
     * @access private
     * @var array
     */
    private $_options = array();

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
        foreach ($options as $value => $label) {
            $this->_options[$value] = new Typecho_Widget_Helper_Layout('input');
            $this->container($this->_options[$value]->setAttribute('name', $this->name)
            ->setAttribute('type', 'radio')
            ->setAttribute('value', $value)
            ->setAttribute('id', $this->name . '-' . $value));
            
            $labelItem = new Typecho_Widget_Helper_Layout('label');
            $this->container($labelItem->setAttribute('for', $this->name . '-' . $value)
            ->html($label . '&nbsp;'));
        }
        
        return current($this->_options);
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
        if (isset($this->_options[$value])) {
            $this->value = $value;
            $this->_options[$value]->setAttribute('checked', 'true');
            $this->input = $this->_options[$value];
        }
    }
}
