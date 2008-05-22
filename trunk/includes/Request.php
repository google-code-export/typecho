<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入api支持 */
require_once 'Typecho.php';

/**
 * 服务器请求处理类
 *
 * @package Request
 */
class TypechoRequest
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
        //优先选择GET方式
        if(!empty($_GET[$key]))
        {
            return $_GET[$key];
        }
        //其次为POST方式
        else if(!empty($_POST[$key]))
        {
            return $_POST[$key];
        }
        //如果都没有返回空
        else
        {
            return $default;
        }
    }

    /**
     * 从参数列表指定的值中获取http传递参数
     *
     * @access public
     * @param string $parameter 指定的参数
     * @return unknown
     */
    public static function getParametersFrom()
    {
        $args = func_get_args();
        $parameters = array();

        foreach($args as $arg)
        {
            $parameters[$arg] = self::getParameter($arg);
        }

        return $parameters;
    }
    
    /**
     * 参数条件输出
     * 
     * @access public
     * @param string $name 参数名
     * @param string $value 参数值
     * @param string $string 输出值
     * @return void
     */
    public static function callParameter($name, $value, $string)
    {
        if($value == self::getParameter($name))
        {
            echo $string;
        }
    }

    /**
     * 提交表单触发函数
     *
     * @access protected
     * @param mixed $postData 表单触发值
     * @param string $functionName 触发的函数名
     * @return void
     */
    public static function bindParameter($postData, $functionName)
    {
        if(is_array($postData))
        {
            $doPost = true;
            foreach($postData as $key => $val)
            {
                $parameter = self::getParameter($key);
                if(NULL == $parameter || $val != $parameter)
                {
                    $doPost = false;
                }
            }

            if($doPost)
            {
                call_user_func($functionName);
            }
        }
        else if(NULL == self::getParameter($key))
        {
            call_user_func($functionName);
        }
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
        return empty($_COOKIE[$key]) ? NULL : $_COOKIE[$key];
    }

    /**
     * 设置指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $value 设置的值
     * @param integer $ttl 过期时间,默认为0,表示随会话时间结束
     * @return void
     */
    public static function setCookie($key, $value, $expire = 0, $url = NULL)
    {
        $path = '/';
        if(!empty($url))
        {
            $parsed = parse_url($url);
            
            /** 在路径后面强制加上斜杠 */
            $path = empty($parsed['path']) ? '/' : Typecho::pathToUrl(NULL, $parsed['path']);
        }
        
        /** 对数组型COOKIE的写入支持 */
        if(is_array($value))
        {
            foreach($value as $name => $val)
            {
                setcookie("{$key}[{$name}]", $val, $expire, $path);
            }
        }
        else
        {
            setcookie($key, $value, $expire, $path);
        }
    }

    /**
     * 删除指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @return void
     */
    public static function deleteCookie($key, $url = NULL)
    {
        if(!isset($_COOKIE[$key]))
        {
            return;
        }

        $path = '/';
        if(!empty($url))
        {
            $parsed = parse_url($url);
            
            /** 在路径后面强制加上斜杠 */
            $path = empty($parsed['path']) ? '/' : Typecho::pathToUrl(NULL, $parsed['path']);
        }

        /** 对数组型COOKIE的删除支持 */
        if(is_array($_COOKIE[$key]))
        {
            foreach($_COOKIE[$key] as $name => $val)
            {
                setcookie("{$key}[{$name}]", '', time() - 2592000, $path);
            }
        }
        else
        {
            setcookie($key, '', time() - 2592000, $path);
        }
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
        return empty($_SESSION[$key]) ?  $default : $_SESSION[$key];
    }

    /**
     * 设置指定的SESSION值
     *
     * @access public
     * @param string $key 指定的参数
     * @param string $value 设置的值
     * @return void
     */
    public static function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * 删除指定的SESSION值
     *
     * @access public
     * @param string $key 指定的参数
     * @return void
     */
    public static function deleteSession($key)
    {
        session_unregister($key);
    }

    /**
     * 销毁所有SESSION值
     *
     * @access public
     * @return void
     */
    public static function destorySession()
    {
        session_unset();
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
