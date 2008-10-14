<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入api支持 */
require_once 'Typecho/Common.php';

/**
 * 服务器请求处理类
 *
 * @package Request
 */
class Typecho_Request
{
    /**
     * 获取指定的http传递参数
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $default 默认的参数
     * @return mixed
     */
    public static function getParameter($key, $default = NULL)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }
    
    /**
     * 设置http传递参数
     * 
     * @access public
     * @param string $name 指定的参数
     * @param mixed $value 参数值
     * @return void
     */
    public static function setParameter($name, $value)
    {
        $_REQUEST[$name] = $value;
        reset($_REQUEST);
    }

    /**
     * 从参数列表指定的值中获取http传递参数
     *
     * @access public
     * @param mixed $parameter 指定的参数
     * @return unknown
     */
    public static function getParametersFrom($parameter)
    {
        if(is_array($parameter))
        {
            $args = $parameter;
        }
        else
        {
            $args = func_get_args();
            $parameters = array();
        }

        foreach($args as $arg)
        {
            $parameters[$arg] = self::getParameter($arg);
        }

        return $parameters;
    }

    /**
     * 获取指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @param string $default 默认的参数
     * @return mixed
     */
    public static function getCookie($key, $default = NULL)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    /**
     * 获取指定的SESSION值
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $default 默认的参数
     * @return string
     */
    public static function getSession($key, $default = NULL)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * 判断请求是否为Ajax请求
     * 
     * @access public
     * @return boolean
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHTTPREQUEST' == strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']);
    }
    

    /**
     * 获取当前pathinfo
     *
     * @access public
     * @return string
     */
    public static function getPathInfo()
    {
        if(!empty($_SERVER['PATH_INFO']) || NULL != getenv('PATH_INFO'))
        {
            $pathInfo = empty($_SERVER['PATH_INFO']) ? getenv('PATH_INFO') : $_SERVER['PATH_INFO'];
            if(0 === strpos($pathInfo,$_SERVER['SCRIPT_NAME']))
            {
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            }
            else
            {
                $path = $pathInfo;
            }
        }
        else if(!empty($_SERVER['ORIG_PATH_INFO']) || NULL != getenv('ORIG_PATH_INFO'))
        {
            $pathInfo = empty($_SERVER['ORIG_PATH_INFO']) ? getenv('ORIG_PATH_INFO') : $_SERVER['ORIG_PATH_INFO'];
            if(0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']) && 0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
            {
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            }
            else
            {
                $path = $pathInfo;
            }
        }
        else if(!empty($_SERVER["REDIRECT_Url"]))
        {
            $path = $_SERVER["REDIRECT_Url"];

            if(empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == $_SERVER["REDIRECT_QUERY_STRING"])
            {
                $parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
                if(!empty($parsedUrl['query']))
                {
                    $_SERVER['QUERY_STRING'] = $parsedUrl['query'];
                    parse_str($parsedUrl['query'], $GET);
                    $_GET = array_merge($_GET, $GET);

                    reset($_GET);
                }
                else
                {
                    unset($_SERVER['QUERY_STRING']);
                }

                reset($_SERVER);
            }
        }

        return empty($path) ? '/' : $path;
    }

    /**
     * 获取客户端ip
     *
     * @access public
     * @return string
     */
    public static function getClientIp()
    {
        if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        else if(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        else if(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ip = "unknown";
        }

        return addslashes($ip);
    }
}
