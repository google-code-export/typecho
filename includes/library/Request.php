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
    
    static public function getParameterList($key)
    {
        $parameter = self::getParameter($key);
        return is_array($parameter) ? array_unique($parameter) : array($parameter);
    }
    
    static public function getCookie($key)
    {
        return empty($_COOKIES[$key]) ? $_COOKIES[$key] : NULL;
    }
    
    static public function setCookie($key, $value, $ttl)
    static public function deleteCookie($key, $value, $ttl)
    static public function getSession($key)
    static public function setSession($key, $value)
    static public function deleteSession($key)
    static public function destorySession($key)
}
