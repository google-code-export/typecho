<?php

class Te_Request
{
    /**
     * 客户端ip 
     * 
     * @var mixed
     * @access private
     */
    private $_ip;

    /**
     * 当前时间戳
     * 
     * @var integer
     * @access private
     */
    private $_now;

    /**
     * _request 
     * 
     * @var array
     * @access private
     */
    private $_httpRequest = NULL;

    /**
     * 基础目录 
     * 
     * @var string
     * @access private
     */
    private $_baseUrl = NULL;
    
    /**
     * 路径信息
     *
     * @access private
     * @var string
     */
    private $_pathInfo = NULL;

    /**
     * 请求方法 
     * 
     * @var string
     * @access private
     */
    private $_method;

    /**
     * 请求完整地址
     * 
     * @var string
     * @access private
     */
    private $_requestUri;

    /**
     * 参数列表 
     * 
     * @var array
     * @access private
     */
    private $_params = array();

    /**
     * json参数列表
     * 
     * @var array
     * @access private
     */
    private $_jsonParams = array();

    /**
     * 命令行模式
     * 
     * @var boolean
     * @access private
     */
    private $_commandMode = NULL;

    /**
     * 来路
     * 
     * @var string
     * @access private
     */
    private $_referer = false;

    /**
     * 是否ssl 
     * 
     * @var mixed
     * @access private
     */
    private $_isSecure = NULL;

    /**
     * 获取传递参数 
     * 
     * @param string $key 
     * @access private
     * @return string
     */
    private function getArg($key)
    {
        $value = strlen($key) > 1 ? getopt('', $key . ':') : getopt($key . ':');
        return $value[$key];
    }
    
    /**
     * 是否为命令行模式运行
     * 
     * @static
     * @access public
     * @return void
     */
    public function commandMode()
    {
        if (NULL === $this->_commandMode) {
            $this->_commandMode = isset($_SERVER['_']) || isset($_SERVER['COMMAND_MODE']);
        }

        return $this->_commandMode;
    }

    /**
     * 获取客户端识别串 
     * 
     * @static
     * @access public
     * @return void
     */
    public function getAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    /**
     * 获取对客户端身份的唯一标识 
     * 
     * @static
     * @access public
     * @return void
     */
    public function getClientId()
    {
        return md5($this->getAgent() . "\0" . $this->getIp());
    }

    /**
     * 获取前端传递参数
     * 
     * @param string $key 参数值 
     * @param mixed $default 
     * @access public
     * @return void
     */
    public function get($key, $default = NULL)
    {
        if (!isset($this->_params[$key])) {
            $paramKeys = explode('|', $key);
            $this->_params[$key] = $default;
            
            $request = array();
            if ($this->commandMode()) {
                $short = '';
                $long = array();
                
                foreach ($paramKeys as $paramKey) {
                    if (strlen($paramKey) > 1) {
                        $long[] = $paramKey . '::';
                    } else {
                        $short .= $paramKey . '::';
                    }
                }

                $request = getopt($short, $long);
            } else {
                $request = $this->getHttpRequest();
            }

            foreach ($paramKeys as $paramKey) {
                if (isset($request[$paramKey])) {
                    $this->_params[$key] = $request[$paramKey];
                } else if (false !== strpos($paramKey, ':')) {
                    list($jsonParamKey, $jsonKey) = explode(':', $paramKey);
                    $json = $this->getJson($jsonParamKey);

                    if (isset($json[$jsonKey])) {
                        $this->_params[$key] = $json[$jsonKey];
                    }
                }
            }
        }

        return $this->_params[$key];
    }

    /**
     * 从请求中获取json数据
     * 
     * @param mixed $key 
     * @access public
     * @return void
     */
    public function getJson($key)
    {
        if (!isset($this->_jsonParams[$key])) {
            $this->_jsonParams[$key] = NULL;
            
            if (!empty($_REQUEST[$key])) {
                $result = json_decode($this->get($key), true);
                if (NULL !== $result) {
                    $this->_jsonParams[$key] = $result;
                }
            }
        }

        return $this->_jsonParams[$key];
    }

    /**
     * 获取数组化的参数
     * 
     * @param mixed $key 
     * @access public
     * @return array
     */
    public function getArray($key)
    {
        if (is_array($key)) {
            $result = array();
            foreach ($key as $k) {
                $result[$k] = $this->get($k);
            }
            return $result;
        } else {
            $result = $this->get($key, array());
            return is_array($result) ? $result : array($result);
        }
    }
   
    /**
     * 获取前端传递变量
     * 
     * @static
     * @access public
     * @return array
     */
    public function getHttpRequest()
    {
        if (NULL === $this->_httpRequest) {
            $this->_httpRequest = array_merge($_POST, $_GET);
        }

        return $this->_httpRequest;
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
     * 获取客户端ip 
     * 
     * @access public
     * @return void
     */
    public function getIp()
    {
        if (empty($this->_ip)) {
            switch (true) {
                case !empty($_SERVER['HTTP_X_FORWARDED_FOR']):
                    list($this->_ip) = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
                    break;
                case !empty($_SERVER['HTTP_CLIENT_IP']):
                    $this->_ip = $_SERVER['HTTP_CLIENT_IP'];
                    break;
                case !empty($_SERVER['REMOTE_ADDR']):
                    $this->_ip = $_SERVER['REMOTE_ADDR'];
                    break;
                default:
                    $this->_ip = '-';
                    break;
            }
        }

        return $this->_ip;
    }

    /**
     * 获取当前时间戳 
     * 
     * @static
     * @access public
     * @return void
     */
    public function getNow()
    {
        if (empty($this->_now)) {
            $this->_now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        }

        return $this->_now;
    }

    /**
     * 请求方式
     * 
     * @access public
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->_method)) {
            $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return $this->_method;
    }

    /**
     * 获取请求地址
     * 
     * @access public
     * @return string
     */
    public function getRequestUri()
    {
        if (!empty($this->_requestUri)) {
            return $this->_requestUri;
        }

        //处理requestUri
        $requestUri = '/';

        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (
            // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
            isset($_SERVER['IIS_WasUrlRewritten'])
            && $_SERVER['IIS_WasUrlRewritten'] == '1'
            && isset($_SERVER['UNENCODED_URL'])
            && $_SERVER['UNENCODED_URL'] != ''
            ) {
            $requestUri = $_SERVER['UNENCODED_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if (isset($_SERVER['HTTP_HOST']) && strstr($requestUri, $_SERVER['HTTP_HOST'])) {
                $parts       = @parse_url($requestUri);

                if (false !== $parts) {
                    $requestUri  = (empty($parts['path']) ? '' : $parts['path'])
                                 . ((empty($parts['query'])) ? '' : '?' . $parts['query']);
                }
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }

        return $this->_requestUri = $requestUri;
    }

    /**
     * 获取基础目录
     * 
     * @static
     * @access public
     * @return string
     */
    public function getBaseUrl()
    {
        //处理baseUrl
        if (NULL !== $this->_baseUrl) {
            return $this->_baseUrl;
        }

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
        $requestUri = $this->getRequestUri();

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

        return ($this->_baseUrl = (NULL === $finalBaseUrl) ? rtrim($baseUrl, '/') : $finalBaseUrl);
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
        if (NULL !== $this->_pathInfo) {
            return $this->_pathInfo;
        }

        //参考Zend Framework对pahtinfo的处理, 更好的兼容性
        $pathInfo = NULL;
        $requestUri = $this->getRequestUri();
        $finalBaseUrl = $this->getBaseUrl();

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if ((NULL !== $finalBaseUrl)
            && (false === ($pathInfo = substr($requestUri, strlen($finalBaseUrl)))))
        {
            // If substr() returns false then PATH_INFO is set to an empty string
            $pathInfo = '/';
        } elseif (NULL === $finalBaseUrl) {
            $pathInfo = $requestUri;
        }

        if (empty($pathInfo)) {
            $pathInfo = '/';
        }

        // fix issue 456
        return ($this->_pathInfo = '/' . ltrim(urldecode($pathInfo), '/'));
    }

    /**
     * 获取客户端
     *
     * @access public
     * @return void
     */
    public function getReferer()
    {
        if (false === $this->_referer) {
            $this->_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
        }

        return $this->_referer;
    }

    /**
     * 判断参数传递是否为POST形式 
     * 
     * @access public
     * @return boolean
     */
    public function isPost()
    {
        return 'POST' == $this->getMethod();
    }

    /**
     * 判断参数传递是否为GET形式 
     * 
     * @access public
     * @return boolean
     */
    public function isGet()
    {
        return 'GET' == $this->getMethod();
    }

    /**
     * 是否为上传模式 
     * 
     * @static
     * @access public
     * @return boolean
     */
    public function isUpload()
    {
        return !empty($_FILES);
    }

    /**
     * 判断是否为ajax
     *
     * @access public
     * @return boolean
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'];
    }

    /**
     * 判断是否为flash
     *
     * @access public
     * @return boolean
     */
    public function isFlash()
    {
        return 'Shockwave Flash' == $_SERVER['USER_AGENT'];
    }

    /**
     * 是否为安全连接 
     * 
     * @access public
     * @return boolean
     */
    public function isSecure()
    {
        return NULL === $this->_isSecure ? ($this->_isSecure = 
            (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) || 443 == $_SERVER['SERVER_PORT']) : $this->_isSecure;
    }

    /**
     * 检查来路是否与设定值一致
     * 
     * @param string $url 
     * @static
     * @access public
     * @return boolean
     */
    public function checkReferer($url)
    {
        $referer = $this->getReferer();
        if (empty($referer)) {
            return false;
        }

        $refererParts = parse_url($referer);
        $urlParts = parse_url($url);

        if (false === $refererParts) {
            return false;
        }

        if ($refererParts['host'] != $urlParts['host']) {
            return false;
        }

        return true;
    }

    /**
     * 判断复杂的参数情况 
     * 
     * @param mixed $query 前端传递的参数 
     * @access public
     * @return void
     */
    public function is($query)
    {
        $validated = false;

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
                $validated = empty($val) ? ($val != $this->get($key)) : ($val == $this->get($key));

                if (!$validated) {
                    break;
                }
            }
        }

        return $validated;
    }
}


