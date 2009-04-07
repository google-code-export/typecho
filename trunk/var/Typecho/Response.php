<?php
/**
 * API方法,Typecho命名空间
 *
 * @category typecho
 * @package Response
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入api支持 */
require_once 'Typecho/Common.php';

/**
 * Typecho公用方法
 *
 * @category typecho
 * @package Response
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Response
{
    /**
     * http code
     * 
     * @access private
     * @var array
     */
    private static $_httpCode = array(
        100 => 'Continue',
        101	=> 'Switching Protocols',
        200	=> 'OK',
        201	=> 'Created',
        202	=> 'Accepted',
        203	=> 'Non-Authoritative Information',
        204	=> 'No Content',
        205	=> 'Reset Content',
        206	=> 'Partial Content',
        300	=> 'Multiple Choices',
        301	=> 'Moved Permanently',
        302	=> 'Found',
        303	=> 'See Other',
        304	=> 'Not Modified',
        305	=> 'Use Proxy',
        307	=> 'Temporary Redirect',
        400	=> 'Bad Request',
        401	=> 'Unauthorized',
        402	=> 'Payment Required',
        403	=> 'Forbidden',
        404	=> 'Not Found',
        405	=> 'Method Not Allowed',
        406	=> 'Not Acceptable',
        407	=> 'Proxy Authentication Required',
        408	=> 'Request Timeout',
        409	=> 'Conflict',
        410	=> 'Gone',
        411	=> 'Length Required',
        412	=> 'Precondition Failed',
        413	=> 'Request Entity Too Large',
        414	=> 'Request-URI Too Long',
        415	=> 'Unsupported Media Type',
        416	=> 'Requested Range Not Satisfiable',
        417	=> 'Expectation Failed',
        500	=> 'Internal Server Error',
        501	=> 'Not Implemented',
        502	=> 'Bad Gateway',
        503	=> 'Service Unavailable',
        504	=> 'Gateway Timeout',
        505	=> 'HTTP Version Not Supported'
    );

    /**
     * 默认的字符编码
     * 
     * @access private
     * @var string
     */
    private static $_defaultCharset = 'UTF-8';

    /**
     * 解析ajax回执的内部函数
     * 
     * @access private
     * @param mixed $message 格式化数据
     * @return string
     */
    private static function _parseXml($message)
    {
        /** 对于数组型则继续递归 */
        if (is_array($message)) {
            $result = '';
            
            foreach ($message as $key => $val) {
                $tagName = is_int($key) ? 'item' : $key;
                $result .= '<' . $tagName . '>' . self::_parseXml($val) . '</' . $tagName . '>';
            }
            
            return $result;
        } else {
            return preg_match("/^[^<>]+$/is", $message) ? $message : '<![CDATA[' . $message . ']]>';
        }
    }
        
    /**
     * 设置默认回执编码
     * 
     * @access public
     * @param string $charset 字符集
     * @return void
     */
    public static function setDefaultCharset($charset)
    {
        self::$_defaultCharset = $charset;
    }
    
    /**
     * 在http头部请求中声明类型和字符集
     * 
     * @access public
     * @param string $contentType 文档类型
     * @param string $charset 字符集
     * @return void
     */
    public static function setContentType($contentType = 'text/html', $charset = NULL)
    {
        header('content-Type: ' . $contentType . '; charset=' . (empty($charset) ? self::$_defaultCharset : $charset), true);
    }
    
    /**
     * 设置http头
     * 
     * @access public
     * @param string $name 名称 
     * @param string $value 对应值
     * @param boolean $replace 是否替换重复
     * @param integer $responseCode 回执代码
     * @return void
     */
    public static function setHeader($name, $value, $replace = false, $responseCode = 0)
    {
        /** 设置回执代码 */
        if ($responseCode > 0) {
            header($name . ': ' . $value, $replace, $responseCode);
        } else {
            header($name . ': ' . $value, $replace);
        }
    }
    
    /**
     * 设置HTTP状态
     * 
     * @access public
     * @param integer $code http代码
     * @return void
     */
    public static function setStatus($code)
    {
        if (isset(self::$_httpCode[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . self::$_httpCode[$code], true, $code);
        }
    }
    
    /**
     * 抛出ajax的回执信息
     * 
     * @access public
     * @param string $message 消息体
     * @param string $charset 信息编码
     * @return void
     */
    public static function throwXml($message, $charset = NULL)
    {
        /** 设置http头信息 */
        self::setContentType('text/xml', $charset);
        
        /** 构建消息体 */
        echo '<?xml version="1.0" encoding="' . self::$_defaultCharset . '"?>',
        '<response>',
        self::_parseXml($message),
        '</response>';
        
        /** 终止后续输出 */
        exit;
    }
    
    /**
     * 抛出json回执信息
     * 
     * @access public
     * @param string $message 消息体
     * @param string $charset 信息编码
     * @return void
     */
    public static function throwJson($message, $charset = NULL)
    {
        /** 设置http头信息 */
        self::setContentType('application/json', $charset);
        
        /** Typecho_Json */
        require_once 'Typecho/Json.php';
        echo Typecho_Json::encode($message);
        
        /** 终止后续输出 */
        exit;
    }

    /**
     * 重定向函数
     *
     * @access public
     * @param string $location 重定向路径
     * @param boolean $isPermanently 是否为永久重定向
     * @return void
     */
    public static function redirect($location, $isPermanently = false)
    {
        /** Typecho_Common */
        require_once 'Typecho/Common.php';
        $location = Typecho_Common::safeUrl($location);
    
        if ($isPermanently) {
            self::setHeader('location', $location, false, 301);
            die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
    <html><head>
    <title>301 Moved Permanently</title>
    </head><body>
    <h1>Moved Permanently</h1>
    <p>The document has moved <a href="' . $location . '">here</a>.</p>
    </body></html>');
        } else {
            self::setHeader('location', $location, false, 302);
            die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
    <html><head>
    <title>302 Moved Temporarily</title>
    </head><body>
    <h1>Moved Temporarily</h1>
    <p>The document has moved <a href="' . $location . '">here</a>.</p>
    </body></html>');
        }
    }
    
    /**
     * 返回来路
     *
     * @access protected
     * @param string $anchor 锚点地址
     * @param string $default 默认来路
     * @return void
     */
    public static function goBack($anchor = NULL, $default = NULL)
    {
        /** Typecho_Request */
        require_once 'Typecho/Request.php';
        $referer = Typecho_Request::getReferer();
    
        //判断来源
        if (!empty($referer)) {
            // ~ fix Issue 38
            if (!empty($anchor)) {
                $parts = parse_url($referer);
                if (isset($parts['fragment'])) {
                    $referer = substr($referer, 0, strlen($referer) - strlen($parts['fragment']) - 1);
                }
            }
            
            self::redirect($referer . (empty($anchor) ? NULL : '#' . $anchor), false);
        } else if (!empty($default)) {
            self::redirect($default);
        }
    }
    
    /**
     * 设置指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $value 设置的值
     * @param integer $expire 过期时间,默认为0,表示随会话时间结束
     * @param string $url 路径(可以是域名,也可以是地址)
     * @return void
     */
    public static function setCookie($key, $value, $expire = 0, $url = NULL)
    {
        $path = '/';
        if (!empty($url)) {
            $parsed = parse_url($url);
            
            /** 在路径后面强制加上斜杠 */
            $path = empty($parsed['path']) ? '/' : Typecho_Common::url(NULL, $parsed['path']);
        }
        
        /** 对数组型COOKIE的写入支持 */
        if (is_array($value)) {
            foreach ($value as $name => $val) {
                setcookie("{$key}[{$name}]", $val, $expire, $path);
            }
        } else {
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
        if (!isset($_COOKIE[$key])) {
            return;
        }

        $path = '/';
        if (!empty($url)) {
            $parsed = parse_url($url);
            
            /** 在路径后面强制加上斜杠 */
            $path = empty($parsed['path']) ? '/' : Typecho_Common::url(NULL, $parsed['path']);
        }

        /** 对数组型COOKIE的删除支持 */
        if (is_array($_COOKIE[$key])) {
            foreach ($_COOKIE[$key] as $name => $val) {
                setcookie("{$key}[{$name}]", '', time() - 2592000, $path);
            }
        } else {
            setcookie($key, '', time() - 2592000, $path);
        }
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
}
