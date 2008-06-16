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
require_once 'Typecho/Widget/Helper/Form/Abstract.php';

/**
 * 文字输入表单项帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Text extends Typecho_Widget_Helper_Form_Abstract
{
    /**
     * 重载构造函数
     * 
     * @access public
     * @param string $inputName 表单名称
     * @param string $label 表单标题
     * @param string $description 表单描述
     * @return void
     */
    public function __construct($inputName, $value = NULL, $label = NULL, $description = NULL)
    {
        parent::__construct('input', $inputName, $value, $label, $description);
        $this->input->setAttribute('type', 'text');
    }
    
    /**
     * 设置表单项默认值
     * 
     * @access public
     * @param string $value 表单项默认值
     * @return Typecho_Widget_Helper_Form_Input
     */
    public function value($value)
    {
        $this->input->setAttribute('value', $value);
        return $this;
    }
}
