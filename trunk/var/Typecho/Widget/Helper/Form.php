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
     * 表单体
     * 
     * @access private
     * @var Typecho_Widget_Helper_Layout
     */
    private $_formBody;
    
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
     * 准备formBody元素
     * 
     * @access private
     * @return void
     */
    private function prepare()
    {
        if(empty($this->_formBody))
        {
            $this->_formBody = new Typecho_Widget_Helper_Layout('table');
            
            $tr = new Typecho_Widget_Helper_Layout('tr');
            $tr->addItem(new Typecho_Widget_Helper_Layout('th', array('width' => '20%')))
            ->addItem(new Typecho_Widget_Helper_Layout('th', array('width' => '80%')))
            ->appendTo($this->_formBody);
            
            $this->_formBody->setAttribute('class', 'setting')->appendTo($this);
        }
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
    public function addInput(Typecho_Widget_Helper_Form_Abstract $input)
    {
        $this->_inputs[$input->name] = $input;
        $this->prepare();
        $this->_formBody->addItem($input);
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
        if($item instanceof Typecho_Widget_Helper_Form_Submit)
        {
            $this->prepare();
            $this->_formBody->addItem($item);
        }
        else
        {
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
     * 获取输入项
     * 
     * @access public
     * @param string $name 输入项名称
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getInput($name);
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
     * 获取此表单的所有输入项投递值
     * 
     * @access public
     * @return array
     */
    public function getParameters()
    {
        $parameters = array();
        
        foreach($this->_inputs as $name => $input)
        {
            $parameters[$name] = Typecho_Request::getParameter($name);
        }
        return $parameters;
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
        
        foreach($this->_inputs as $name => $input)
        {
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
        
        foreach($this->_inputs as $name => $input)
        {
            $rules[$name] = $input->rules;
        }
        
        /** 表单值 */
        $formData = Typecho_Request::getParametersFrom(array_keys($rules));

        try
        {
            $validator->run($formData, $rules);
        }
        catch(Typecho_Validate_Exception $e)
        {
            /** 利用cookie记录错误 */
            Typecho_Request::setCookie('form_message', $e->getMessages());
            
            /** 利用cookie记录表单值 */
            Typecho_Request::setCookie('form_record', $formData);

            /** 载入异常支持 */
            require_once 'Typecho/Widget/Exception.php';
        
            /** 继续抛出异常 */
            throw new Typecho_Widget_Exception($e->getMessages());
        }
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
        if($record = Typecho_Request::getCookie('form_record'))
        {
            foreach($this->_inputs as $name => $input)
            {
                $input->value(isset($record[$name]) ? $record[$name] : NULL);
            }
            
            Typecho_Request::deleteCookie('form_record');
        }
    
        parent::render();
        Typecho_Request::deleteCookie('form_message');
    }
}
