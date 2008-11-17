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

/** Typecho_Widget_Helper_Decorator_Form_Default */
require_once 'Typecho/Widget/Helper/Decorator/Form/Default.php';

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
abstract class Typecho_Widget_Helper_Form_Element extends Typecho_Widget_Helper_Decorator_Form_Default
{    
    /**
     * 输入栏
     * 
     * @access public
     * @var Typecho_Widget_Helper_Layout
     */
    public $input;

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
     * @param string $name 表单输入项名称
     * @param array $options 选择项
     * @param mixed $value 表单默认值
     * @param string $label 表单标题
     * @param string $description 表单描述
     * @return void
     */
    public function __construct($name = NULL, array $options = NULL, $value = NULL, $label = NULL, $description = NULL)
    {
        /** 设置表单输入项标签 */
        parent::__construct();
        $this->name = $name;
        
        /** 初始化表单项 */
        $this->input = $this->input($this->container, $name, $options);
        
        /** 初始化表单值 */
        if (NULL !== $value) {
            $this->value($value);
        }
        
        /** 初始化表单标题 */
        if (NULL !== $label) {
            $this->label($label);
        }
        
        /** 初始化表单描述 */
        if (NULL !== $description) {
            $this->description($description);
        }
    }
    
    /**
     * 设置表单元素值
     * 
     * @access public
     * @param mixed $value 表单元素值
     * @return Typecho_Widget_Helper_Form_Element
     */
    public function value($value)
    {
        $this->value = $value;
        $this->_value($value);
        return $this;
    }
    
    /**
     * 初始化当前输入项
     * 
     * @access public
     * @param Typecho_Widget_Helper_Layout $container 容器对象
     * @param string $name 表单元素名称
     * @param array $options 选择项
     * @return Typecho_Widget_Helper_Layout
     */
    abstract public function input(Typecho_Widget_Helper_Layout $container, $name = NULL, array $options = NULL);
    
    /**
     * 设置表单元素值
     * 
     * @access protected
     * @param mixed $value 表单元素值
     * @return void
     */
    abstract protected function _value($value);
    
    /**
     * 增加验证器
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form_Element
     */
    public function addRule($name)
    {
        $this->rules[] = func_get_args();
        return $this;
    }
}
