<?php
/**
 * 隐藏域帮手类
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
 * 隐藏域帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Hidden extends Typecho_Widget_Helper_Form_Abstract
{
    /**
     * 重载构造函数
     * 
     * @access public
     * @param string $inputName 表单名称
     * @return void
     */
    public function __construct($inputName, $value = NULL)
    {
        $this->setTagName('input');
        
        $this->name = $inputName;
        $this->input = $this;
        $this->setAttribute('name', $inputName)
        ->setAttribute('id', $inputName)
        ->setAttribute('type', 'hidden');
        
        $this->value($value);
    }
    
    /**
     * 设置表单项默认值
     * 
     * @access public
     * @param string $value 表单项默认值
     * @return Typecho_Widget_Helper_Form_Hidden
     */
    public function value($value)
    {
        $this->setAttribute('value', $value);
        return $this;
    }
}
