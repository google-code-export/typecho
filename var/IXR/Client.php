<?php
/* 
   IXR - The Inutio XML-RPC Library - (c) Incutio Ltd 2002
   Version 1.61 - Simon Willison, 11th July 2003 (htmlentities -> htmlspecialchars)
   Site:   http://scripts.incutio.com/xmlrpc/
   Manual: http://scripts.incutio.com/xmlrpc/manual.php
   Made available under the Artistic License: http://www.opensource.org/licenses/artistic-license.php
*/

/** IXR值 */
require_once 'IXR/Value.php';

/** IXR消息 */
require_once 'IXR/Message.php';

/** IXR请求体 */
require_once 'IXR/Request.php';

/** IXR错误 */
require_once 'IXR/Error.php';

/** IXR日期 */
require_once 'IXR/Date.php';

/** IXR Base64编码 */
require_once 'IXR/Base64.php';

/**
 * IXR客户端
 * reload by typecho team(http://www.typecho.org)
 *
 * @package IXR
 */
class IXR_Client
{
    const DEFAULT_USERAGENT = 'The Incutio XML-RPC PHP Library(Reload By Typecho)';

    /**
     * 服务端地址
     * 
     * @access private
     * @var string
     */
    private $server;
    
    /**
     * 端口名称
     * 
     * @access private
     * @var integer
     */
    private $port;
    
    /**
     * 路径名称
     * 
     * @access private
     * @var string
     */
    private $path;
    
    /**
     * 客户端
     * 
     * @access private
     * @var string
     */
    private $useragent;
    
    /**
     * 回执结构体
     * 
     * @access private
     * @var string
     */
    private $response;
    
    /**
     * 消息体
     * 
     * @access private
     * @var string
     */
    private $message = false;
    
    /**
     * 调试开关
     * 
     * @access private
     * @var boolean
     */
    private $debug = false;
    
    /**
     * 请求前缀
     * 
     * @access private
     * @var string
     */
    private $_prefix = NULL;
    
    // Storage place for an error message
    private $error = false;
    
    /**
     * 客户端构造函数
     * 
     * @access public
     * @param string $server 服务端地址
     * @param string $path 路径名称
     * @param integer $port 端口名称
     * @param string $useragent 客户端
     * @return void
     */
    public function __construct($server, $path = false, $port = 80, $useragent = self::DEFAULT_USERAGENT, $prefix = NULL)
    {
        if (!$path)
        {
            // Assume we have been given a Url instead
            $bits = parse_url($server);
            $this->server = $bits['host'];
            $this->port = isset($bits['port']) ? $bits['port'] : 80;
            $this->path = isset($bits['path']) ? $bits['path'] : '/';
            
            // Make absolutely sure we have a path
            if (!$this->path)
            {
                $this->path = '/';
            }
        }
        else
        {
            $this->server = $server;
            $this->path = $path;
            $this->port = $port;
        }
        
        $this->useragent = $useragent;
    }
    
    /**
     * 设置调试模式
     * 
     * @access public
     * @return void
     */
    public function __setDebug()
    {
        $this->debug = true;
    }

    /**
     * 执行请求
     * 
     * @access public
     * @return void
     */
    public function __rpcCall()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $request = new IXR_Request($method, $args);
        $length = $request->getLength();
        $xml = $request->getXml();
        
        $r = "\r\n";
        $request  = "POST {$this->path} HTTP/1.0$r";
        $request .= "Host: {$this->server}$r";
        $request .= "Content-Type: text/xml$r";
        $request .= "User-Agent: {$this->useragent}$r";
        $request .= "Content-length: {$length}$r$r";
        $request .= $xml;
        
        // Now send the request
        if ($this->debug)
        {
            echo '<pre>'.htmlspecialchars($request)."\n</pre>\n\n";
        }
        
        $fp = @fsockopen($this->server, $this->port);
        
        if (!$fp)
        {
            $this->error = new IXR_Error(-32300, 'transport error - could not open socket');
            return false;
        }
        
        fputs($fp, $request);
        $contents = '';
        $gotFirstLine = false;
        $gettingHeaders = true;
        
        while (!feof($fp))
        {
            $line = fgets($fp, 4096);
            
            if (!$gotFirstLine)
            {
                // Check line for '200'
                if (strstr($line, '200') === false)
                {
                    $this->error = new IXR_Error(-32300, 'transport error - HTTP status code was not 200');
                    return false;
                }
                
                $gotFirstLine = true;
            }
            
            if (trim($line) == '')
            {
                $gettingHeaders = false;
            }
            
            if (!$gettingHeaders)
            {
                $contents .= trim($line)."\n";
            }
        }
        
        if ($this->debug)
        {
            echo '<pre>'.htmlspecialchars($contents)."\n</pre>\n\n";
        }
        
        // Now parse what we've got back
        $this->message = new IXR_Message($contents);
        if (!$this->message->parse())
        {
            // XML error
            $this->error = new IXR_Error(-32700, 'parse error. not well formed');
            return false;
        }
        
        // Is the message a fault?
        if ($this->message->messageType == 'fault')
        {
            $this->error = new IXR_Error($this->message->faultCode, $this->message->faultString);
            return false;
        }
        
        // Message must be OK
        return true;
    }
    
    /**
     * 增加前缀
     * <code>
     * $rpc->metaWeblog->newPost();
     * </code>
     * 
     * @access public
     * @param string $prefix 前缀
     * @return void
     */
    public function __get($prefix)
    {
        return new IXR_Client($this->server, $this->path, $this->port, $this->useragent, $this->prefix . $prefix . '.');
    }
    
    /**
     * 增加魔术特性
     * by 70
     * 
     * @access public
     * @return mixed
     */
    public function __call($method, $args)
    {
        array_unshift($args, $this->prefix . $method);
        $return = call_user_func(array($this, '__rpcCall'), $args);
        
        if($return)
        {
            return $this->__getResponse();
        }
        else
        {
            require_once 'IXR/Exception.php';
            throw new IXR_Exception($this->__getErrorMessage(), $this->__getErrorCode());
        }
    }
    
    /**
     * 获得返回值
     * 
     * @access public
     * @return void
     */
    public function __getResponse()
    {
        // methodResponses can only have one param - return that
        return $this->message->params[0];
    }
    
    /**
     * 是否为错误
     * 
     * @access public
     * @return void
     */
    public function __isError()
    {
        return (is_object($this->error));
    }
    
    /**
     * 获取错误代码
     * 
     * @access public
     * @return void
     */
    public function __getErrorCode()
    {
        return $this->error->code;
    }
    
    /**
     * 获取错误消息
     * 
     * @access public
     * @return void
     */
    public function __getErrorMessage()
    {
        return $this->error->message;
    }
}
