<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 服务器请求处理类
 * 
 * @package TypechoRequest
 */
class TypechoRequest
{
    static public function getParameter($key)
    {
        if(!empty($_GET[$key]))
        {
            return $_GET[$key];
        }
        else if(!empty($_POST[$key]))
        {
            return $_POST[$key];
        }
        else
        {
            return NULL;
        }
    }
    
    static public function getParametersFrom()
    {
        $args = func_get_args();
        $parameters = array();
        
        foreach($args as $arg)
        {
            $parameters[$arg] = self::getParameter($arg);
        }
        
        return $parameters;
    }
    
    static public function getCookie($key)
    {
        return empty($_COOKIE[$key]) ? NULL : $_COOKIE[$key];
    }
    
    static public function setCookie($key, $value, $ttl = 0)
    {
        setcookie($key, $value, $ttl, typechoGetSiteRoot());
    }
    
    static public function deleteCookie($key)
    {
        setcookie($name, '', 0, typechoGetSiteRoot());
    }
    
    static public function getSession($key)
    {
        return empty($_SESSION[$key]) ?  NULL : $_SESSION[$key];
    }
    
    static public function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    static public function deleteSession($key)
    {
        session_unregister($key);
    }
    
    static public function destorySession()
    {
        session_unset();
    }
}
