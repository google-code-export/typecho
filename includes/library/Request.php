<?php
/**
 * Typecho Blog Platform * * @author     qining * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org) * @license    GNU General Public License 2.0 * @version    $Id$ */
/**
 * 服务器请求处理类 *  * @package Request */class TypechoRequest
{
    /**     * 获取指定的http传递参数     *      * @access public     * @param string $key 指定的参数     * @return mixed     */    static public function getParameter($key)    {        //优先选择GET方式        if(!empty($_GET[$key]))        {            return $_GET[$key];        }        //其次为POST方式        else if(!empty($_POST[$key]))        {            return $_POST[$key];        }        //如果都没有返回空        else        {            return NULL;        }    }    
    /**     * 从参数列表指定的值中获取http传递参数     *      * @access public     * @param string $parameter 指定的参数     * @return unknown     */    static public function getParametersFrom()    {        $args = func_get_args();        $parameters = array();        
        foreach($args as $arg)        {            $parameters[$arg] = self::getParameter($arg);        }        
        return $parameters;    }    
    /**     * 提交表单触发函数     *      * @access protected     * @param mixed $postData 表单触发值     * @param string $functionName 触发的函数名     * @return void     */    static public function bindParameter($postData, $functionName)    {        if(is_array($postData))        {            $doPost = true;            foreach($postData as $key => $val)            {                $parameter = self::getParameter($key);                if(NULL == $parameter || $val != $parameter)                {                    $doPost = false;                }            }            
            if($doPost)            {                call_user_func($functionName);            }        }        else if(NULL == self::getParameter($key))        {            call_user_func($functionName);        }    }    
    /**     * 获取指定的COOKIE值     *      * @access public     * @param string $key 指定的参数     * @return mixed     */    static public function getCookie($key)    {        return empty($_COOKIE[$key]) ? NULL : $_COOKIE[$key];    }    
    /**     * 设置指定的COOKIE值     *      * @access public     * @param string $key 指定的参数     * @param mixed $value 设置的值     * @param integer $ttl 过期时间,默认为0,表示随会话时间结束     * @return void     */    static public function setCookie($key, $value, $ttl = 0)    {        setcookie($key, $value, $ttl, typechoGetSiteRoot());    }    
    /**     * 删除指定的COOKIE值     *      * @access public     * @param string $key 指定的参数     * @return void     */    static public function deleteCookie($key)    {        setcookie($name, '', 0, typechoGetSiteRoot());    }    
    /**     * 获取指定的SESSION值     *      * @access public     * @param string $key 指定的参数     * @return string     */    static public function getSession($key)    {        return empty($_SESSION[$key]) ?  NULL : $_SESSION[$key];    }    
    /**     * 设置指定的SESSION值     *      * @access public     * @param string $key 指定的参数     * @param string $value 设置的值     * @return void     */    static public function setSession($key, $value)    {        $_SESSION[$key] = $value;    }    
    /**     * 删除指定的SESSION值     *      * @access public     * @param string $key 指定的参数     * @return void     */    static public function deleteSession($key)    {        session_unregister($key);    }    
    /**     * 销毁所有SESSION值     *      * @access public     * @return void     */    static public function destorySession()    {        session_unset();    }}
