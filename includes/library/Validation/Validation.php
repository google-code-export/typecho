<?php
/**
 * Typecho Blog Platform
 *
 * 验证类
 *
 * @usage
 *
 * @todo       错误信息控制需要重写
 * @author     feelinglucky
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

class Validation
{
    /**
     * 错误信息
     */
    protected $_error_array;

    /**
     * 需要验证的变量
     */
    protected $_rules;

    /**
     * 验证规则
     */
    protected $_rules_key;


    function __construct()
    {
        // ...
    }


    /**
     * Set Rules
     *
     * This function takes an array of field names and validation
     * rules as input ad simply stores is for use later.
     *
     * @access	public
     * @param	mixed
     * @param	string
     * @return	void
     */
    public function set_rules($data, $rules = '')
    {
        if ( ! is_array($data))
        {
            if ($rules == '') {
                return;
            }

            $data[$data] = $rules;
        }

        foreach ($data as $key => $val)
        {
            if (is_array($val)) {
                $this->_rules[$key]     = $val[0];
                $this->_rules_key[$key] = $val[1];
            } else {
                $this->_rules[$key] = $val;
            }
        }
    }


   /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @access	public
     * @return	bool
     */		
    public function run()
    {
        // Do we even have any data to process?  Mm?
        if (count($_POST) == 0 OR count($this->_rules) == 0)
        {
            return false;
        }

        // Cycle through the rules and test for errors
        foreach ($this->_rules as $field => $rules)
        {
            //Explode out the rules!
            $ex = explode('|', $rules);

            // Is the field required?  If not, if the field is blank  we'll move on to the next test
            if ( ! in_array('required', $ex, true) AND strpos($rules, 'callback_') === false)
            {
                if ( ! isset($_POST[$field]) OR $_POST[$field] == '')
                {
                    continue;
                }
            }

            /*
             * Are we dealing with an "isset" rule?
             *
             * Before going further, we'll see if one of the rules
             * is to check whether the item is set (typically this
             * applies only to checkboxes).  If so, we'll
             * test for it here since there's not reason to go
             * further
             */
            if ( ! isset($_POST[$field]))
            {			
                if (in_array('isset', $ex, true) OR in_array('required', $ex))
                {
                    if ( ! isset($this->_error_messages['isset']))
                    {
                        if (false === ($line = $this->CI->lang->line('isset')))
                        {
                            $line = 'The field was not set';
                        }							
                    }
                    else
                    {
                        $line = $this->_error_messages['isset'];
                    }

                    $field = ( ! isset($this->_rules_key[$field])) ? $field : $this->_rules_key[$field];

                    $this->_error_array[] = sprintf($line, $field);	
                }

                continue;
            }

            /*
             * Set the current field
             *
             * The various prepping functions need to know the
             * current field name so they can do this:
             *
             * $_POST[$this->_current_field] == 'bla bla';
             */
            $this->_current_field = $field;

            // Cycle through the rules!
            foreach ($ex As $rule)
            {
                // Is the rule a callback?			
                $callback = false;
                if (substr($rule, 0, 9) == 'callback_')
                {
                    $rule = substr($rule, 9);
                    $callback = true;
                }

                // Strip the parameter (if exists) from the rule
                // Rules can contain a parameter: max_length[5]
                $param = false;

                if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
                {
                    $rule	= $match[1];
                    $param	= $match[2];
                }

                // Call the function that corresponds to the rule
                if ($callback === true)
                {
                    if ( ! method_exists($this->CI, $rule))
                    {
                        continue;
                    }

                    $result = $this->CI->$rule($_POST[$field], $param);	

                    // If the field isn't required and we just processed a callback we'll move on...
                    if ( ! in_array('required', $ex, true) AND $result !== false)
                    {
                        continue 2;
                    }

                }
                else
                {				
                    if ( ! method_exists($this, $rule))
                    {
                        /*
                         * Run the native PHP function if called for
                         *
                         * If our own wrapper function doesn't exist we see
                         * if a native PHP function does. Users can use
                         * any native PHP function call that has one param.
                         */
                        if (function_exists($rule))
                        {
                            $_POST[$field] = $rule($_POST[$field]);
                            $this->$field = $_POST[$field];
                        }

                        continue;
                    }

                    $result = $this->$rule($_POST[$field], $param);
                }

                // Did the rule test negatively?  If so, grab the error.
                if ($result === false)
                {
                    $line = $this->_error_messages[$rule];;

                    // Build the error message
                    //$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
                    //$mparam = ( ! isset($this->_fields[$param])) ? $param : $this->_fields[$param];
                    $mfield = ( ! isset($this->_rules_key[$field])) ? $field : $this->_rules_key[$field];
                    $mparam = ( ! isset($this->_rules_key[$param])) ? $param : $this->_rules_key[$param];

                    $message = sprintf($line, $mfield, $mparam);

                    // Set the error variable.  Example: $this->username_error
                    $error = $field.'_error';
                    $this->$error = $this->_error_prefix.$message.$this->_error_suffix;

                    // Add the error to the error array
                    $this->_error_array[] = $message;				
                    continue 2;
                }
            }

        }
        $total_errors = count($this->_error_array);

        /*
         * Recompile the class variables
         *
         * If any prepping functions were called the $_POST data
         * might now be different then the corresponding class
         * variables so we'll set them anew.
         */	
        if ($total_errors > 0)
        {
            $this->_safe_form_data = true;
        }

        // Did we end up with any errors?
        if ($total_errors == 0)
        {
            return true;
        }

        return false;
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
        if ( ! is_array($str))
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
        return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;
    }


    /**
     * Validate IP Address
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
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
?>
