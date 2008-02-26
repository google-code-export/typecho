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

class TypechoValidation
{
    /**
     * 错误信息
     */
    protected $_error;

    /**
     * 需要验证规则
     *
     * @type array
     */
    protected $_rules;

    /**
     * 错误变量
     *
     * @type array
     */
    protected $_error_array;

    /**
     *
     *
     */
    function __construct()
    {
        // 重设错误信息
        $this->reSet();
    }


   /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @access	public
     * @return	bool
     */		
    public function run($data)
    {
        /**
         * 如果规则为空，则直接放弃
         */
        if (empty($this->_rules)) {
            return false;
        }


        // Cycle through the rules and test for errors
        foreach ($this->_rules as $rule)
        {
            // 没有这个测试项目则直接跳过
            if ( ! method_exists($this, $rule)) {
                continue;
            }

            if (!$this->$rule($data)) {
                $this->_error = $this->_error_messages[$rule];
                return false;
            }
        }

        return true;
    }


    /**
     * 验证字符串
     *
     * @usage 
     * <code>
     * TypechoValidation::form('name', array('alpha' => '对不起,您的输入必须为纯字符', 
     *              'mail' => '对不起,您必须输入一个合法的email地址'));
     * </code>
     * @param $data  string
     * @param $rules array
     *
     */
    public function form($data, $rules)
    {
        if (!is_array) {
            return ;
        }

        foreach ($rules as $rule => $error_message) {
            $this->_rules[] = $rule;
            $this->_error_messages[$rule] = $error_message;
        }

        return $data ? $this->run($data) : false;
    }


    /**
     * 重设状态信息
     *
     * @return void
     */
    public function reSet()
    {
        $this->_error_messages = array();
        $this->_rules = array();
        $this->_error = "";
    }


    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->_error;
    }

    /**
     * Required
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    function required($str)
    {
        if (!is_array($str))
        {
            return (trim($str) == '') ? false : true;
        }
        else
        {
            return ( ! empty($str));
        }
    }


    /**
     * Minimum Length
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function min_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return false;
        }

        return (strlen($str) < $val) ? false : true;
    }


    /**
     * Max Length
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function max_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return false;
        }

        // 验证中文长度，仅使用 utf-8 编码
        if (function_exists('mb_strlen')) {
            $strlen = mb_strlen($str, 'utf-8');
        } else {
            $strlen = strlen($str);
        }

        return ($strlen > $val) ? false : true;
    }


    /**
     * Exact Length
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function exact_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return false;
        }

        // 验证中文长度，仅使用 utf-8 编码
        if (function_exists('mb_strlen')) {
            $strlen = mb_strlen($str, 'utf-8');
        } else {
            $strlen = strlen($str);
        }

        return ($strlen != $val) ? false : true;
    }


    /**
     * Valid Email
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function valid_email($str)
    {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;
    }


    /**
     * 验证 IP 地址
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function valid_ip($ip)
    {
        // ...
    }


    /**
     * Alpha
     *
     * @access	public
     * @param	string
     * @return	bool
     */		
    function alpha($str)
    {
        return preg_match("/^([a-z])+$/i", $str) ? true : false;
    }


    /**
     * Alpha-numeric
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function alpha_numeric($str)
    {
        return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? false : true;
    }


    /**
     * Alpha-numeric with underscores and dashes
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function alpha_dash($str)
    {
        return preg_match("/^([-a-z0-9_-])+$/i", $str) ? true : false;
    }


    /**
     * Numeric
     *
     * @access	public
     * @param	int
     * @return	bool
     */	
    function numeric($str)
    {
        return ( ! ereg("^[0-9\.]+$", $str)) ? false : true;
    }


    /**
     * Is Numeric
     *
     * @access	public
     * @param	string
     * @return	bool
     */	
    function is_numeric($str)
    {
        return ( ! is_numeric($str)) ? false : true;
    }


    /**
     * 验证中文 utf-8 字符
     *
     * @access	public
     * @param	string
     * @return	boolean
     */
    function zh_alpha($str)
    {
        if (preg_match("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF]|[\x81-\xfe]|[a-z])+$/i", $str)) {
            return true;
        }
        else
        {
            return preg_match("/^([a-z])+$/i", $str) ? true : false;
        }
    }


    /**
     * 验证中文全角数字
     *
     * @access	public
     * @param	string
     * @return	boolean
     */
    function zh_alpha_numeric($str)
    {
        if (preg_match("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF]|[\x81-\xfe]|[a-z0-9])+$/i", $str)) {
            return true;
        }
        else
        {
            return preg_match("/^([a-z0-9])+$/i", $str) ? true : false;
        }
    }
}

