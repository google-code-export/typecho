<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * Typecho Blog Platform
 *
 * 验证类
 *
 * @usage 
 * <code>
 * $test = "hello";
 * $Validation  = new TypechoValidation();
 * $Validation->form($test, array("alpha" => "不是字符");
 * var_dump($Validation->getErrorMsg());
 * </code>
 *
 * @todo       需要批量的处理规则
 * @author     feelinglucky
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 验证类
 * 
 * @package Validation
 */
class TypechoValidation
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
     *
     *
     */
    public function __construct($object = NULL)
    {
        //载入对象
        $this->_object = $object;
    }
    
   /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @access	public
     * @param   array $data
     * @param   array $rules
     * @return	array
     */		
    public function run(array $data, array $rules)
    {
        $result = array();
        $this->_data = $data;
    
        // Cycle through the rules and test for errors
        foreach ($rules as $key => $rule)
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

        return $result;
    }

    /**
     * Minimum Length
     *
     * @access	public
     * @param	string
     * @return	boolean
     */	
    public function minLength($str, $length)
    {
        return (typechoStrLen($str) > $length);
    }
    
    /**
     * 验证输入是否一致
     * 
     * @access public
     * @param string $str
     * @param string $key
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
     * @param string $str
     * @return boolean
     */
    public function required($str)
    {
        return true;
    }


    /**
     * Max Length
     *
     * @access	public
     * @param	string
     * @return	boolean
     */	
    function maxLength($str, $length)
    {
        return (typechoStrLen($str) < $length);
    }


    /**
     * Valid Email
     *
     * @access	public
     * @param	string
     * @return	boolean
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
     * @access	public
     * @param	string
     * @return	boolean
     */		
    public function alpha($str)
    {
        return preg_match("/^([a-z])+$/i", $str) ? true : false;
    }


    /**
     * Alpha-numeric
     *
     * @access	public
     * @param	string
     * @return	boolean
     */	
    public function alphaNumeric($str)
    {
        return preg_match("/^([a-z0-9])+$/i", $str);
    }


    /**
     * Alpha-numeric with underscores and dashes
     *
     * @access	public
     * @param	string
     * @return	boolean
     */	
    public function alphaDash($str)
    {
        return preg_match("/^([-a-z0-9_-])+$/i", $str) ? true : false;
    }


    /**
     * Numeric
     *
     * @access	public
     * @param	integer
     * @return	boolean
     */	
    public function isFloat($str)
    {
        return ereg("^[0-9\.]+$", $str);
    }


    /**
     * Is Numeric
     *
     * @access	public
     * @param	string
     * @return	boolean
     */	
    public function isInteger($str)
    {
        return is_numeric($str);
    }
}

