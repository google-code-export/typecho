<?php
/**
 * 多行文字域帮手
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
 * 多行文字域帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Element_Textarea extends Typecho_Widget_Helper_Form_Element
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
        $input = new Typecho_Widget_Helper_Layout('textarea', array('name' => $name));
        $container->addItem($input);
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
        $this->input->html($value);
    }
}
