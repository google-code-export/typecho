<?php
/**
 * 表单处理帮手
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

/** Typecho_Validate */
require_once 'Typecho/Validate.php';

/**
 * 表单处理帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form extends Typecho_Widget_Helper_Layout
{
    /** 表单post方法 */
    const POST_METHOD = 'post';
    
    /** 表单get方法 */
    const GET_METHOD = 'get';
    
    /** 标准编码方法 */
    const STANDARD_ENCODE = 'application/x-www-form-urlencoded';
    
    /** 混合编码 */
    const MULTIPART_ENCODE = 'multipart/form-data';
    
    /** 文本编码 */
    const TEXT_ENCODE= 'text/plain';
    
    /**
     * 输入元素列表
     * 
     * @access private
     * @var array
     */
    private $_inputs = array();
    
    /**
     * 构造函数,设置基本属性
     * 
     * @access public
     * @return void
     */
    public function __construct($action = NULL, $method = self::GET_METHOD, $enctype = self::STANDARD_ENCODE)
    {
        /** 设置表单标签 */
        parent::__construct('form');
        
        /** 关闭自闭合 */
        $this->setClose(false);
        
        /** 设置表单属性 */
        $this->setAction($action);
        $this->setMethod($method);
        $this->setEncodeType($enctype);
    }
    
    /**
     * 设置表单编码方案
     * 
     * @access public
     * @param string $enctype 编码方法
     * @return Typecho_Widget_Helper_Form
     */
    public function setEncodeType($enctype)
    {
        $this->setAttribute('enctype', $enctype);
        return $this;
    }
    
    /**
     * 增加输入元素
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form_Abstract $input 输入元素
     * @return Typecho_Widget_Helper_Form
     */
    public function addInput(Typecho_Widget_Helper_Form_Element $input)
    {
        $this->_inputs[$input->name] = $input;
        $this->addItem($input);
        return $this;
    }
    
    /**
     * 增加元素(重载)
     * 
     * @access public
     * @param Typecho_Widget_Helper_Layout $item 表单元素
     * @return Typecho_Widget_Helper_Layout
     */
    public function addItem(Typecho_Widget_Helper_Layout $item)
    {
        if ($item instanceof Typecho_Widget_Helper_Form_Submit) {
            $this->addItem($item);
        } else {
            parent::addItem($item);
        }
        
        return $this;
    }
    
    /**
     * 获取输入项
     * 
     * @access public
     * @param string $name 输入项名称
     * @return mixed
     */
    public function getInput($name)
    {
        return $this->_inputs[$name];
    }
    
    /**
     * 获取所有输入项的提交值
     * 
     * @access public
     * @return array
     */
    public function getAllRequest()
    {
        $values = array();
        
        foreach ($this->_inputs as $name => $input) {
            $values[$name] = Typecho_Request::getParameter($name);
        }
        return $values;
    }
    
    /**
     * 设置表单提交方法
     * 
     * @access public
     * @param string $method 表单提交方法
     * @return Typecho_Widget_Helper_Form
     */
    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
        return $this;
    }
    
    /**
     * 设置表单提交目的
     * 
     * @access public
     * @param string $action 表单提交目的
     * @return Typecho_Widget_Helper_Form
     */
    public function setAction($action)
    {
        $this->setAttribute('action', $action);
        return $this;
    }
    
    /**
     * 获取此表单的所有输入项固有值
     * 
     * @access public
     * @return array
     */
    public function getValues()
    {
        $values = array();
        
        foreach ($this->_inputs as $name => $input) {
            $values[$name] = $input->value;
        }
        return $values;
    }
    
    /**
     * 获取此表单的所有输入项
     * 
     * @access public
     * @return array
     */
    public function getInputs()
    {
        return $this->_inputs;
    }
    
    /**
     * 验证表单
     * 
     * @access public
     * @return void
     */
    public function validate()
    {
        $validator = new Typecho_Validate();
        $rules = array();
        
        foreach ($this->_inputs as $name => $input) {
            $rules[$name] = $input->rules;
        }
        
        /** 表单值 */
        $formData = Typecho_Request::getParametersFrom(array_keys($rules));
        $error = $validator->run($formData, $rules);
        
        if ($error) {
            /** 利用cookie记录错误 */
            Typecho_Response::setCookie('__typecho_form_message', $error);
            
            /** 利用cookie记录表单值 */
            Typecho_Response::setCookie('__typecho_form_record', $formData);
        }
        
        return $error;
    }
    
    /**
     * 显示表单
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        /** 恢复表单值 */
        if ($record = Typecho_Request::getCookie('__typecho_form_record')) {
            $message = Typecho_Request::getCookie('__typecho_form_message');
            foreach ($this->_inputs as $name => $input) {
                $input->value(isset($record[$name]) ? $record[$name] : $input->value);
                
                /** 显示错误消息 */
                if (isset($message[$name])) {
                    $input->message($message[$name]);
                }
            }
            
            Typecho_Response::deleteCookie('__typecho_form_record');
        }
    
        parent::render();
        Typecho_Response::deleteCookie('__typecho_form_message');
    }
}
