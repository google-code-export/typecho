<?php
/**
 * 表单元素抽象帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Widget_Helper_Layout */
require_once 'Typecho/Widget/Helper/Layout.php';

/** Typecho_Request */
require_once 'Typecho/Request.php';

/**
 * 表单元素抽象类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
abstract class Typecho_Widget_Helper_Form_Abstract extends Typecho_Widget_Helper_Layout
{
    /**
     * 标题组件
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $label;
    
    /**
     * 表格左栏
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $leftTd;
    
    /**
     * 表格右栏
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $rightTd;
    
    /**
     * 输入栏
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $input;
    
    /**
     * 描述栏
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $description;

    /**
     * 表单验证器
     * 
     * @access public
     * @var unknown
     */
    public $rules = array();
    
    /**
     * 表单名称
     * 
     * @access public
     * @var string
     */
    public $name;
    
    /**
     * 表单值
     * 
     * @access public
     * @var mixed
     */
    public $value;

    /**
     * 构造函数
     * 
     * @access public
     * @param string $tagName 表单输入项标签
     * @param string $inputName 表单输入项名称
     * @param string $label 表单标题
     * @param string $description 表单描述
     * @return void
     */
    public function __construct($tagName, $inputName, $value = NULL, $label = NULL, $description = NULL)
    {
        /** 设置表单输入项标签 */
        parent::__construct('tr');
        $this->name = $inputName;
        
        /** 设置左边 */
        $this->leftTd = new Typecho_Widget_Helper_Layout('td');
        $this->label = new Typecho_Widget_Helper_Layout('label');
        $this->label->setAttribute('for', $inputName)->appendTo($this->leftTd);
        $this->addItem($this->leftTd);
        
        /** 设置右边 */
        $this->rightTd = new Typecho_Widget_Helper_Layout('td');
        $this->input = new Typecho_Widget_Helper_Layout($tagName);
        $this->input->setAttribute('name', $inputName)
        ->setAttribute('id', $inputName)
        ->appendTo($this->rightTd);
        
        /** 如果有错误提示 */
        $notice = Typecho_Request::getCookie('form_message');
        if(!empty($notice[$inputName]))
        {
            $detail = new Typecho_Widget_Helper_Layout('span');
            $detail->setAttribute('class', 'detail')->html($notice[$inputName])->appendTo($this->rightTd);
        }
        
        $this->addItem($this->rightTd);
        $this->label($label);
        
        if(!empty($description))
        {
            $this->description($description);
        }
        
        $this->value($value);
    }
    
    /**
     * 设置表单元素值
     * 
     * @access public
     * @param string $value 表单元素值
     * @return Typecho_Widget_Helper_Form_Abstract
     */
    abstract public function value($value);
    
    /**
     * 设置标题
     * 
     * @access public
     * @param string $labelName 标题
     * @return string
     */
    public function label($labelName)
    {
        $this->label->html($labelName);
    }
    
    /**
     * 设置描述语句
     * 
     * @access public
     * @param string $description 描述语句
     * @return string
     */
    public function description($description)
    {
        $this->description = new Typecho_Widget_Helper_Layout('small');
        $this->description->html($description)->appendTo($this->rightTd);
        return $this;
    }
    
    /**
     * 增加验证器
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form_Abstract
     */
    public function addRule($name)
    {
        $this->rules[] = func_get_args();
        return $this;
    }
}
