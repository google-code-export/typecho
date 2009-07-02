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
    private $_params = array();
    
    /**
     * 路径信息
     * 
     * @access private
     * @var string
     */
    private $_pathInfo = NULL;
    
    /**
     * 服务端参数
     * 
     * @access private
     * @var array
     */
    private $_server = array();
    
    /**
     * 客户端ip地址
     * 
     * @access private
     * @var string
     */
    private $_ip = null;
    
    /**
     * 获取实际传递参数(magic)
     * 
     * @access public
     * @param string $key 指定参数
     * @return void
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    /**
     * 判断参数是否存在
     * 
     * @access public
     * @param string $key 指定参数
     * @return void
     */
    public function __isset($key)
    {
        return isset($_GET[$key])
        || isset($_POST[$key])
        || isset($_COOKIE[$key])
        || $this->isSetParam($key);
    }
    
    /**
     * 获取实际传递参数
     * 
     * @access public
     * @param string $key 指定参数
     * @param mixed $default 默认参数 (default: NULL)
     * @return void
     */
    public function get($key, $default = NULL)
    {
        switch (true) {
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            default:
                return $this->getParam($key, $default);
        }
    }

    /**
     * 获取指定的http传递参数
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $default 默认的参数
     * @return mixed
     */
    public function getParam($key, $default = NULL)
    {
        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }
    
    /**
     * 设置http传递参数
     * 
     * @access public
     * @param string $name 指定的参数
     * @param mixed $value 参数值
     * @return void
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
    }
    
    /**
     * 删除参数
     * 
     * @access public
     * @param string $name 指定的参数
     * @return void
     */
    public function unSetParam($name)
    {
        unset($this->_params[$name]);
    }
    
    /**
     * 参数是否存在
     * 
     * @access public
     * @param string $key 指定的参数
     * @return boolean
     */
    public function isSetParam($key)
    {
        return isset($this->_params[$key]);
    }
    
    /**
     * 设置多个参数
     * 
     * @access public
     * @param array $params 参数列表
     * @return void
     */
    public function setParams(array $params)
    {
        array_merge($this->_params, $params);
    }

    /**
     * 从参数列表指定的值中获取http传递参数
     *
     * @access public
     * @param mixed $parameter 指定的参数
     * @return array
     */
    public function getParams($params)
    {
        if (is_array($params)) {
            $args = $params;
        } else {
            $args = func_get_args();
            $params = array();
        }

        foreach ($args as $arg) {
            $params[$arg] = $this->get($arg);
        }

        return $params;
    }
    
    /**
     * 根据当前uri构造指定参数的uri
     * 
     * @access public
     * @param mixed $parameter 指定的参数
     * @return string
     */
    public function getRequestUri($parameter = NULL)
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
    public function getCookie($key, $default = NULL)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }
    

    /**
     * 获取当前pathinfo
     *
     * @access public
     * @return string
     */
    public function getPathInfo()
    {
        /** 缓存信息 */
        if (NULL !== self::$_pathInfo) {
            return self::$_pathInfo;
        }
    
        //参考Zend Framework对pahtinfo的处理, 更好的兼容性
        $pathInfo = NULL;
        
        //处理requestUri
        $requestUri = NULL;
        
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if (isset($_SERVER['HTTP_HOST']) && strstr($requestUri, $_SERVER['HTTP_HOST'])) {
                $parts       = @parse_url($requestUri);
                
                if (false !== $parts) {
                    $requestUri  = (empty($parts['path']) ? '' : $parts['path'])
                                 . ((empty($parts['query'])) ? '' : '?' . $queryString);
                }
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            return self::$_pathInfo = '/';
        }
        
        //处理baseUrl
        $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

        if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['SCRIPT_NAME'];
        } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
            $baseUrl = $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
            $file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/' . $seg . $baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
        }
        
        // Does the baseUrl have anything in common with the request_uri?
        $finalBaseUrl = NULL;
        
        if (0 === strpos($requestUri, $baseUrl)) {
            // full $baseUrl matches
            $finalBaseUrl = $baseUrl;
        } else if (0 === strpos($requestUri, dirname($baseUrl))) {
            // directory portion of $baseUrl matches
            $finalBaseUrl = rtrim(dirname($baseUrl), '/');
        } else if (!strpos($requestUri, basename($baseUrl))) {
            // no match whatsoever; set it blank
            $finalBaseUrl = '';
        } else if ((strlen($requestUri) >= strlen($baseUrl))
            && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0)))
        {
            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        
        $finalBaseUrl = (NULL === $finalBaseUrl) ? rtrim($baseUrl, '/') : $finalBaseUrl;
        
        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        if ((null !== $finalBaseUrl)
            && (false === ($pathInfo = substr($requestUri, strlen($finalBaseUrl)))))
        {
            // If substr() returns false then PATH_INFO is set to an empty string
            $pathInfo = '/';
        } elseif (null === $finalBaseUrl) {
            $pathInfo = $requestUri;
        }

        return (self::$_pathInfo = urldecode(empty($pathInfo) ? '/' : $pathInfo));
    }
        
    /**
     * 设置服务端参数
     * 
     * @access public
     * @param string $name 参数名称
     * @param mixed $value 参数值
     * @return void
     */
    public function setServer($name, $value = null)
    {
        if (null == $value) {
            if (isset($_SERVER[$name])) {
                $value = $_SERVER[$name];
            } else if (isset($_ENV[$name])) {
                $value = $_ENV[$name];
            }
        }
        
        $this->_server[$name] = $value;
    }
    
    /**
     * 获取环境变量
     * 
     * @access public
     * @param string $name 获取环境变量名
     * @return string
     */
    public function getServer($name)
    {
        if (!isset($this->_server[$name])) {
            $this->setServer($name);
        }
        
        return $this->_server[$name];
    }
    
    /**
     * 设置ip地址
     * 
     * @access public
     * @param unknown $ip
     * @return unknown
     */
    public function setIp($ip = null)
    {
        switch (true) {
            case null !== $this->getServer('REMOTE_ADDR'):
                $this->_ip = $this->getServer('REMOTE_ADDR');
                return;
            case null !== $this->getServer('HTTP_CLIENT_IP'):
                $this->_ip = $this->getServer('HTTP_CLIENT_IP');
                return;
            case null !== $this->getServer('HTTP_X_FORWARDED_FOR'):
                $this->_ip = $this->getServer('HTTP_X_FORWARDED_FOR');
                return;
            default:
                break;
        }
        
        $this->_ip = 'unknown';
    }
    
    /**
     * 获取ip地址
     * 
     * @access public
     * @return string
     */
    public function getIp()
    {
        if (null === $this->_ip) {
            $this->setIp();
        }
        
        return $this->_ip;
    }
    
    /**
     * 判断输入是否满足要求
     * 
     * @access public
     * @param mixed $query 条件
     * @return boolean
     */
    public function is($query)
    {
        $validated = false;
        $querys = func_get_args();

        foreach ($querys as $query) {
            switch ($query) {
                case 'GET':
                case 'POST':
                case 'PUT':
                case 'DELETE':
                case 'HEAD':
                case 'OPTIONS':
                
                    /** 各种http方法 */
                    $validated = ($query == $this->getServer('REQUEST_METHOD'));
                    break;
                    
                case 'SECURE':
                    
                    /** 是否为https连接 */
                    $validated = ('on' == $this->getServer('HTTPS'));
                    break;
                    
                case 'AJAX':
                
                    /** 是否为ajax方法 */
                    $validated = ('XMLHttpRequest' == $this->getServer('HTTP_X_REQUESTED_WITH'));
                    break;
                    
                case 'FLASH':
                
                    /** 是否为flash方法 */
                    $validated = ('Shockwave Flash' == $this->getServer('USER_AGENT'));
                    break;
                    
                default:
                
                    /** 解析串 */
                    if (is_string($query)) {
                        parse_str($query, $params);
                    } else if (is_array($query)) {
                        $params = $query;
                    }
                    
                    /** 验证串 */
                    if ($params) {
                        $validated = true;
                        foreach ($params as $key => $val) {
                            if ($val != $this->{$key}) {
                                $validated = false;
                                break;
                            }
                        }
                    }
                    
                    break;
            }
        }
        
        return $validated;
        
    }
}
