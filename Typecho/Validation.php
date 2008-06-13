<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * Typecho Blog Platform
 *
 * 验证类
 * <code>
 * $test = "hello";
 * $Validation  = new TypechoValidation();
 * $Validation->form($test, array("alpha" => "不是字符");
 * var_dump($Validation->getErrorMsg());
 * </code>
 *
 * @todo       需要批量的处理规则
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Validation.php 106 2008-04-11 02:23:54Z magike.net $
 */

/** 验证异常支持 */
require_once 'Typecho/Validation/Exception.php';

/**
 * 验证类
 *
 * @package Validation
 */
class Typecho_Validation
{
    /**
     * 默认对象
     *
     * @access private
     * @var object
     */
    private $_object;

    /**
     * 内部数据
     *
     * @access private
     * @var array
     */
    private $_data;

    /**
     * 验证规则数组
     *
     * @access private
     * @var array
     */
    private $_rules = array();

    /**
     * 初始化函数
     *
     * @access public
     * @param string $object 默认对象
     * @return void
     */
    public function __construct($object = NULL)
    {
        //载入对象
        $this->_object = $object;
    }

    /**
     * 增加验证规则
     * 
     * @access public
     * @param string $key 数值键值
     * @param string $rule 规则名称
     * @param string $message 错误字符串
     * @return void
     */
    public function addRule($key, $rule, $message)
    {
        if(func_num_args() <= 3)
        {
            $this->_rules[$key][$rule] = $message;
        }
        else
        {
            $params = func_get_args();
            $params = array_splice($params, 3);
            $this->_rules[$key][$rule] = array_merge(array($message), $params);
        }
    }

   /**
     * Run the Validator
     * This function does all the work.
     *
     * @access	public
     * @param   array $data 需要验证的数据
     * @param   array $rules 验证数据遵循的规则
     * @return	array
     */
    public function run(array $data, $rules = NULL)
    {
        $result = array();
        $this->_data = $data;
        $rules = empty($rules) ? $this->_rules : $rules;

        // Cycle through the rules and test for errors
        foreach($rules as $key => $rule)
        {
            if(empty($data[$key]))
            {
                if(isset($rule['required']))
                {
                    $message = is_array($rule['required']) ? $rule['required'][0] : $rule['required'];
                    $result[$key] = $message;
                }
            }
            else
            {
                foreach($rule as $method => $params)
                {
                    if(is_array($params))
                    {
                        $message = $params[0];
                        $params[0] = $data[$key];
                    }
                    else
                    {
                        $message = $params;
                        $params = array($data[$key]);
                    }

                    if(method_exists($this, $method))
                    {
                        $method = array(&$this, $method);
                    }
                    else if(!empty($this->_object) && method_exists($this->_object, $method))
                    {
                        $method = array(&$this->_object, $method);
                    }
                    else if(function_exists($method))
                    {
                        $method = $method;
                    }
                    else
                    {
                        continue;
                    }

                    if(!call_user_func_array($method, $params))
                    {
                        $result[$key] = $message;
                    }
                }
            }
        }

        if($result)
        {
            throw new Typecho_Validation_Exception($result);
        }
    }

    /**
     * 最小长度
     * 
     * @access public
     * @param string $str 待处理的字符串
     * @param integer $length 最小长度
     * @return boolean
     */
    public function minLength($str, $length)
    {
        return (typechoStrLen($str) > $length);
    }

    /**
     * 验证输入是否一致
     *
     * @access public
     * @param string $str 待处理的字符串
     * @param string $key 需要一致性检查的键值
     * @return boolean
     */
    public function confirm($str, $key)
    {
        return !empty($this->_data[$key]) && ($str == $this->_data[$key]);
    }

    /**
     * 虚函数
     *
     * @access public
     * @param string $str 待处理的字符串
     * @return boolean
     */
    public function required($str)
    {
        return true;
    }
    
    /**
     * 枚举类型判断
     * 
     * @access public
     * @param string $str 待处理的字符串
     * @param mixed $param 枚举值
     * @return unknown
     */
    public function enum($str)
    {
        $args = func_get_args();
        array_shift($args);
        
        return in_array($str, $args);
    }

    /**
     * Max Length
     *
     * @access public
     * @param string
     * @return boolean
     */
    public function maxLength($str, $length)
    {
        return (typechoStrLen($str) < $length);
    }

    /**
     * Valid Email
     *
     * @access public
     * @param string
     * @return boolean
     */
    public function email($str)
    {
        return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str);
    }

    /**
     * 验证是否为网址
     *
     * @access public
     * @param string $str
     * @return boolean
     */
    public function url($str)
    {
        return preg_match("|^http://[_=&///?\.a-zA-Z0-9-]+$|i", $str);
    }

    /**
     * Alpha
     *
     * @access public
     * @param string
     * @return boolean
     */
    public function alpha($str)
    {
        return preg_match("/^([a-z])+$/i", $str) ? true : false;
    }

    /**
     * Alpha-numeric
     *
     * @access public
     * @param string
     * @return boolean
     */
    public function alphaNumeric($str)
    {
        return preg_match("/^([a-z0-9])+$/i", $str);
    }

    /**
     * Alpha-numeric with underscores and dashes
     *
     * @access public
     * @param string
     * @return boolean
     */
    public function alphaDash($str)
    {
        return preg_match("/^([_a-z0-9-])+$/i", $str) ? true : false;
    }

    /**
     * Numeric
     *
     * @access public
     * @param integer
     * @return boolean
     */
    public function isFloat($str)
    {
        return ereg("^[0-9\.]+$", $str);
    }

    /**
     * Is Numeric
     *
     * @access public
     * @param string
     * @return boolean
     */
    public function isInteger($str)
    {
        return is_numeric($str);
    }
}
