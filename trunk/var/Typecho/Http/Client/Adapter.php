<?php
/**
 * 客户端适配器
 * 
 * @author qining
 * @category typecho
 * @package Http
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Http_Client */
require_once 'Typecho/Http/Client.php';

/**
 * 客户端适配器
 * 
 * @author qining
 * @category typecho
 * @package Http
 */
abstract class Typecho_Http_Client_Adapter
{    
    /**
     * 方法名
     * 
     * @access protected
     * @var string
     */
    protected $method = Typecho_Http_Client::METHOD_GET;
    
    /**
     * 传递参数
     * 
     * @access protected
     * @var string
     */
    protected $query;
    
    /**
     * 设置超时
     * 
     * @access protected
     * @var string
     */
    protected $timeout = 3;
    
    /**
     * 需要在body中传递的值
     * 
     * @access protected
     * @var array
     */
    protected $data = array();
    
    /**
     * 文件列表
     * 
     * @access protected
     * @var array
     */
    protected $files = array();
    
    /**
     * 参数
     * 
     * @access protected
     * @var array
     */
    protected $params = array();
    
    /**
     * cookies
     * 
     * @access protected
     * @var array
     */
    protected $cookies = array();
    
    /**
     * 请求编码
     * 
     * @access protected
     * @var string
     */
    protected $charset = 'UTF-8';
    
    /**
     * 协议名称及版本
     * 
     * @access protected
     * @var string
     */
    protected $rfc = 'HTTP/1.1';
    
    /**
     * 请求地址
     * 
     * @access protected
     * @var string
     */
    protected $url;
    
    /**
     * 主机名
     * 
     * @access protected
     * @var string
     */
    protected $host;
    
    /**
     * 路径
     * 
     * @access protected
     * @var string
     */
    protected $path = '/';
    
    /**
     * 端口
     * 
     * @access protected
     * @var integer
     */
    protected $port = 80;
    
    /**
     * 回执头部信息
     * 
     * @access protected
     * @var array
     */
    protected $responseHeader = array();
    
    /**
     * 回执代码
     * 
     * @access protected
     * @var integer
     */
    protected $responseStatus;
    
    /**
     * 回执身体
     * 
     * @access protected
     * @var string
     */
    protected $responseBody;

    /**
     * 判断适配器是否可用
     * 
     * @access public
     * @return boolean
     */
    abstract public static function isAvailable();
    
    /**
     * 设置方法名
     * 
     * @access public
     * @param string $method
     * @return Typecho_Http_Client_Adapter
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    
    /**
     * 设置指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $value 设置的值
     * @param integer $expire 过期时间,默认为0,表示随会话时间结束
     * @param string $url 路径(可以是域名,也可以是地址)
     * @return Typecho_Http_Client_Adapter
     */
    public function setCookie($key, $value, $expire = 0, $url = NULL)
    {
        return $this;
    }
    
    /**
     * 设置传递参数
     * 
     * @access public
     * @param mixed $query 传递参数
     * @return Typecho_Http_Client_Adapter
     */
    public function setQuery($query)
    {
        $query = is_array($query) ? http_build_query($query) : $query;
        $this->query = empty($this->query) ? $query : $this->params['query'] . '&' . $query;
        return $this;
    }
    
    /**
     * 设置需要POST的数据
     * 
     * @access public
     * @param array $data 需要POST的数据
     * @return Typecho_Http_Client_Adapter
     */
    public function setData(array $data)
    {
        $this->data = empty($this->data) ? $data : array_merge($this->data, $data);
        $this->setMethod(Typecho_Http_Client::METHOD_POST);
        return $this;
    }
    
    /**
     * 设置需要POST的文件
     * 
     * @access public
     * @param array $files 需要POST的文件
     * @return Typecho_Http_Client_Adapter
     */
    public function setFiles(array $files)
    {
        $this->files = empty($this->files) ? $files : array_merge($this->files, $files);
        $this->setMethod(Typecho_Http_Client::METHOD_POST);
        return $this;
    }
    
    /**
     * 设置超时时间
     * 
     * @access public
     * @param integer $timeout 超时时间
     * @return Typecho_Http_Client_Adapter
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
    
    /**
     * 设置http协议
     * 
     * @access public
     * @param string $rfc http协议
     * @return Typecho_Http_Client_Adapter
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;
        return $this;
    }
    
    /**
     * 设置参数
     * 
     * @access public
     * @param string $key 参数名称
     * @param string $value 参数值
     * @return Typecho_Http_Client_Adapter
     */
    public function setParam($key, $value)
    {
        $key = str_replace(' ', '-', ucwords(str_replace('-', ' ', $key)));
        $this->params[$key] = $value;
        return $this;
    }
    
    /**
     * 设置编码
     * 
     * @access public
     * @param string $charset 编码
     * @return Typecho_Http_Client_Adapter
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }
    
    /**
     * 发送请求
     * 
     * @access public
     * @param string $url 请求地址
     * @param string $rfc 请求协议
     * @return string
     */
    public function send($url)
    {
        $params = parse_url($url);
        
        if (!empty($params['host'])) {
            $this->host = $params['host'];
        } else {
            /** Typecho_Http_Client_Exception */
            require_once 'Typecho/Http/Client/Exception.php';
            throw new Typecho_Http_Client_Exception('Unknown Host', 500);
        }
        
        if (!empty($params['path'])) {
            $this->path = $params['path'];
        }
        
        if (!empty($params['query'])) {
            $this->path .= '?' . $params['query'] . (empty($this->query) ? NULL : '&' . $this->query);
            $url .= (empty($this->query) ? NULL : '&' . $this->query);
        } else {
            $url .= (empty($this->query) ? NULL : '?' . $this->query);
        }
        
        if (!empty($params['port'])) {
            $this->port = $params['port'];
        }
        
        $response = $this->httpSend($url);
        
        if (!$response) {
            return;
        }

        str_replace("\r", '', $response);
        $rows = explode("\n", $response);

        $foundStatus = false;
        $foundInfo = false;
        $lines = array();

        foreach ($rows as $key => $line) {
            if (!$foundStatus) {
                if (0 === strpos($line, "HTTP/")) {
                    if ('' == trim($rows[$key + 1])) {
                        continue;
                    } else {
                        $status = explode(' ', str_replace('  ', ' ', $line));
                        $this->responseStatus = intval($status[1]);
                        $foundStatus = true;
                    }
                }
            } else {
                if (!$foundInfo) {
                    if ('' != trim($line)) {
                        $status = explode(':', $line);
                        $name = strtolower(array_shift($status));
                        $data = implode(':', $status);
                        $this->responseHeader[trim($name)] = trim($data);
                    } else {
                        $foundInfo = true;
                    }
                } else {
                    $lines[] = $line;
                }
            }
        }

        $this->reponseBody = implode("\n", $lines);
        return $this->reponseBody;
    }
    
    /**
     * 获取回执的头部信息
     * 
     * @access public
     * @param string $key 头信息名称
     * @return string
     */
    public function getResponseHeader($key)
    {
        return isset($this->responseHeader[$key]) ? $this->responseHeader[$key] : NULL;
    }
    
    /**
     * 获取回执代码
     * 
     * @access public
     * @return integer
     */
    public function getResponseStatus()
    {
        return $this->responseStatus;
    }
    
    /**
     * 获取回执身体
     * 
     * @access public
     * @return string
     */
    public function getResponseBody()
    {
        return $this->reponseBody;
    }
    
    /**
     * 需要实现的请求方法
     * 
     * @access public
     * @param string $url 请求地址
     * @return string
     */
    abstract public function httpSend($url);
}
