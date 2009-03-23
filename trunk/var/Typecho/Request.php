<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 服务器请求处理类
 *
 * TODO getSiteUrl
 * @package Request
 */
class Typecho_Request
{
    /**
     * 内部参数
     * 
     * @access private
     * @var array
     */
    private static $_params = array();

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
        switch (true) {
            case isset(self::$_params[$key]):
                $value = self::$_params[$key];
                break;
            case isset($_GET[$key]):
                $value = $_GET[$key];
                break;
            case isset($_POST[$key]):
                $value = $_POST[$key];
                break;
            case isset($_COOKIE[$key]):
                $value = $_COOKIE[$key];
                break;
            default:
                return $default;
        }
        
        return strlen($value) > 0 ? $value : $default;
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
        self::$_params[$name] = $value;
    }
    
    /**
     * 参数是否存在
     * 
     * @access public
     * @param string $key 指定的参数
     * @return boolean
     */
    public static function isSetParameter($key)
    {
        return isset(self::$_params[$key])
        || isset($_GET[$key])
        || isset($_POST[$key])
        || isset($_COOKIE[$key]);
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
        if (is_array($parameter)) {
            $args = $parameter;
        } else {
            $args = func_get_args();
            $parameters = array();
        }

        foreach ($args as $arg) {
            $parameters[$arg] = self::getParameter($arg);
        }

        return $parameters;
    }
    
    /**
     * 根据当前uri构造指定参数的uri
     * 
     * @access public
     * @param mixed $parameter 指定的参数
     * @return string
     */
    public static function uri($parameter = NULL)
    {
        /** 初始化地址 */
        list($scheme) = explode('/', $_SERVER["SERVER_PROTOCOL"]);
        $requestUri = strtolower($scheme) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($requestUri);
    
        /** 初始化参数 */
        if (is_string($parameter)) {
            parse_str($parameter, $args);
        } else if (is_array($parameter)) {
            $args = $parameter;
        } else {
            return $requestUri;
        }
        
        /** 构造query */
        if (isset($parts['query'])) {
            parse_str($parts['query'], $currentArgs);
            $args = array_merge($currentArgs, $args);
        }
        $parts['query'] = http_build_query($args);
        
        /** Typecho_Common */
        require_once 'Typecho/Common.php';
        
        /** 返回地址 */
        return Typecho_Common::buildUrl($parts);
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
        if (!empty($_SERVER['PATH_INFO']) || NULL != getenv('PATH_INFO')) {
            $pathInfo = empty($_SERVER['PATH_INFO']) ? getenv('PATH_INFO') : $_SERVER['PATH_INFO'];
            if (0 === strpos($pathInfo,$_SERVER['SCRIPT_NAME'])) {
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            } else {
                $path = $pathInfo;
            }
        } else if (!empty($_SERVER['ORIG_PATH_INFO']) || NULL != getenv('ORIG_PATH_INFO')) {
            $pathInfo = empty($_SERVER['ORIG_PATH_INFO']) ? getenv('ORIG_PATH_INFO') : $_SERVER['ORIG_PATH_INFO'];
            if (0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']) && 0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME'])) {
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            } else {
                $path = $pathInfo;
            }
        } else if (!empty($_SERVER["REDIRECT_URL"])) {
            $path = $_SERVER["REDIRECT_URL"];

            if (empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == $_SERVER["REDIRECT_QUERY_STRING"]) {
                $parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
                if (!empty($parsedUrl['query'])) {
                    $_SERVER['QUERY_STRING'] = $parsedUrl['query'];
                    parse_str($parsedUrl['query'], $GET);
                    $_GET = array_merge($_GET, $GET);

                    reset($_GET);
                } else {
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
        switch (true) {
            case getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'):
                return getenv('HTTP_CLIENT_IP');
            case getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'): 
                return getenv('HTTP_X_FORWARDED_FOR');
            case getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'):
                return getenv('REMOTE_ADDR');
            case isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'):
                return $_SERVER['REMOTE_ADDR'];
            default:
                return 'unknown';
        }
    }
    
    /**
     * 获取网页来源
     * 
     * @access public
     * @return string
     */
    public static function getReferer()
    {
        return getenv('HTTP_REFERER');
    }
    
    /**
     * 获取客户端
     * 
     * @access public
     * @return string
     */
    public static function getAgent()
    {
        return getenv('HTTP_USER_AGENT');
    }
    
    /**
     * 请求方法是否为POST
     *
     * @return boolean
     */
    public static function isPost()
    {
        return ('POST' == $_SERVER['REQUEST_METHOD']);
    }

    /**
     * 请求方法是否为GET
     *
     * @return boolean
     */
    public static function isGet()
    {
        return ('GET' == $_SERVER['REQUEST_METHOD']);
    }

    /**
     * 请求方法是否为PUT
     *
     * @return boolean
     */
    public static function isPut()
    {
        return ('PUT' == $_SERVER['REQUEST_METHOD']);
    }
}
