<?php
/**
 * API方法,Typecho命名空间
 *
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * Typecho公用方法
 *
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho
{
    /**
     * 系统启动函数
     * 
     * @access public
     * @param 字符集
     * @return void
     */
    public static function start($charset = 'UTF-8')
    {
        //初始化会话
        session_start();

        //关闭魔术引号功能
        if(get_magic_quotes_gpc())
        {
            $_GET = self::stripslashesDeep($_GET);
            $_POST = self::stripslashesDeep($_POST);
            $_COOKIE = self::stripslashesDeep($_COOKIE);

            reset($_GET);
            reset($_POST);
            reset($_COOKIE);
        }

        //开始监视输出区
        if(__TYPECHO_GZIP_ENABLE__
           && empty($_SERVER['HTTP_ACCEPT_ENCODING'])
           && false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
        {
            ob_start("ob_gzhandler");
        }
        else
        {
            ob_start();
        }

        //设置默认时区
        if(!ini_get("date.timezone") && function_exists("date_default_timezone_set"))
        {
            @date_default_timezone_set('UTC');
        }
        
        //设置文件头
        header('content-Type: text/html;charset= ' . $charset);
    }

    /**
     * 递归去掉数组反斜线
     *
     * @access public
     * @param mixed $value
     * @return mixed
     */
    public static function stripslashesDeep($value)
    {
        return is_array($value) ? array_map(array('Typecho', 'stripslashesDeep'), $value) : stripslashes($value);
    }

    /**
     * 抽取多维数组的某个元素,组成一个新数组,使这个数组变成一个扁平数组
     * 使用方法:
     * <code>
     * <?php
     * $fruit = array(array('apple' => 2, 'banana' => 3), array('apple' => 10, 'banana' => 12));
     * $banana = Typecho::arrayFlatten($fruit, 'banana');
     * print_r($banana);
     * //outputs: array(0 => 3, 1 => 12);
     * ?>
     * </code>
     *
     * @access public
     * @param array $value 被处理的数组
     * @param string $key 需要抽取的键值
     * @return array
     */
    public static function arrayFlatten(array $value, $key)
    {
        $result = array();

        if($value)
        {
            foreach($value as $inval)
            {
                if(is_array($inval) && isset($inval[$key]))
                {
                    $result[] = $inval[$key];
                }
                else
                {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * 自闭合html修复函数
     *
     * @access public
     * @param string $string 需要修复处理的字符串
     * @return string
     */
    public static function fixHtml($string)
    {
        //关闭自闭合标签
        $startPos = strrpos($string, "<");
        $trimString = substr($string, $startPos);

        if(false === strpos($trimString, ">"))
        {
            $string = substr($string, 0, $startPos);
        }

        //非自闭合html标签列表
        preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
        preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);

        if(!empty($startTags[1]) && is_array($startTags[1]))
        {
            krsort($startTags[1]);
            $closeTagsIsArray = is_array($closeTags[1]);
            foreach($startTags[1] as $key => $tag)
            {
                $attrLength = strlen($startTags[2][$key]);
                if($attrLength > 0 && "/" == $startTags[2][$key][$attrLength - 1])
                {
                    continue;
                }
                if(!empty($closeTags[1]) && $closeTagsIsArray)
                {
                    if(in_array($tag, $closeTags[1]))
                    {
                        continue;
                    }
                }
                $string .= "</{$tag}>";
            }
        }

        return $string;
    }

    /**
     * 去掉字符串中的html标签
     *
     * @access public
     * @param string $string 需要处理的字符串
     * @param string $except 需要忽略的html标签
     * @return string
     */
    public static function stripTags($string, $except = NULL)
    {
        $string = str_replace('<!DOC', '<DOC', $string);

        if(NULL === $except)
        {
            $string = preg_replace( "/<\/(div|h1|h2|h3|h4|h5|h6|p|th|td|li|ol|dt|dd|pre|caption|input|textarea|blockquote|code|pre|button|body)[^>]*>/", "\n\n", $string);
        }

        $string = preg_replace("/\s*<br\s*\/>\s*/is", "\n", $string);
        $string = strip_tags($string, $except);
        $string = str_replace("\r\n", "\n", $string);
        $string = str_replace("\r", "", $string);

        return trim($string);
    }

    /**
     * feed头部生成函数
     *
     * @access public
     * @param string $type feed类型
     * @param string $charset feed字符集
     * @param array $modules feed使用模块
     * @return void
     */
    public static function feedHeader($type, $charset, array $modules = NULL)
    {
        $supportModules = array(
            'content'   =>  'xmlns:content="http://purl.org/rss/1.0/modules/content/"',
            'wfw'       =>  'xmlns:wfw="http://wellformedweb.org/CommentAPI/"',
            'dc'        =>  'xmlns:dc="http://purl.org/dc/elements/1.1/"',
            'atom'      =>  'xmlns="http://www.w3.org/2005/Atom"',
            'thr'       =>  'xmlns:thr="http://purl.org/syndication/thread/1.0"',
            'lang'      =>  'xml:lang="en"'
        );

        switch  (strtoupper($type))
        {
            case 'RSS2.0':
            {
                header('content-Type: application/rss+xml;charset= ' . $charset, true);
                echo '<?xml version="1.0" encoding="' . $charset . '"?>';
                echo '<rss version="2.0"';
                break;
            }
            case 'RSS0.92':
            {
                header('content-Type: text/xml;charset= ' . $charset, true);
                echo '<?xml version="1.0" encoding="' . $charset . '"?>';
                echo '<rss version="0.92"';
            }
            case 'ATOM':
            {
                header('content-Type: application/atom+xml;charset= ' . $charset, true);
                echo '<?xml version="1.0" encoding="' . $charset . '"?>';
                echo '<feed';
            }
            default:
                break;
        }

        if($modules)
        {
            foreach($modules as $module)
            {
                if(isset($supportModules[$module]))
                {
                    echo "\r\n" . $supportModules[$module];
                }
            }
        }

        echo '>';
    }

    /**
     * 宽字符串截字函数
     *
     * @access public
     * @param string $str 需要截取的字符串
     * @param integer $start 开始截取的位置
     * @param integer $length 需要截取的长度
     * @param string $trim 截取后的截断标示符
     * @return string
     */
    public static function subStr($str, $start, $length, $trim = "...")
    {
        if(function_exists('mb_get_info'))
        {
            $iLength = mb_strlen($str);
            $str = mb_substr($str, $start, $length);
            return ($length < $iLength - $start) ? $str . $trim : $str;
        }
        else
        {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            $str = join("", array_slice($info[0], $start, $length));
            return ($length < (sizeof($info[0]) - $start)) ? $str . $trim : $str;
        }
    }

    /**
     * 获取宽字符串长度函数
     *
     * @access public
     * @param string $str 需要获取长度的字符串
     * @return integer
     */
    public static function strLen($str)
    {
        if(function_exists('mb_get_info'))
        {
            return mb_strlen($str);
        }
        else
        {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            return sizeof($info[0]);
        }
    }

    /**
     * 生成缩略名
     *
     * @access public
     * @param string $str 需要生成缩略名的字符串
     * @param string $default 默认的缩略名
     * @return string
     */
    public static function slugName($str, $default)
    {
        $str = str_replace(array("'", ":", "\\", "/"), "", $str);
        $str = str_replace(array("+", ",", " ", ".", "?", "=", "&", "!", "<", ">", "(", ")", "[", "]", "{", "}"), "-", $str);

        //cut string
        //from end
        $length = strlen($str);
        $i = $length;
        $cutOff = 0;

        while($i > 0)
        {
            $i--;
            if('-' == $str[$i])
            {
                $cutOff ++;
            }
            else
            {
                break;
            }
        }

        if($cutOff)
        {
            $str = substr($str, 0, - $cutOff);
        }

        //from start
        $length = strlen($str);
        $i = 0;
        $cutOff = 0;

        while($i < $length)
        {
            if('-' == $str[$i])
            {
                $cutOff ++;
            }
            else
            {
                break;
            }
            $i++;
        }

        if($cutOff)
        {
            $str = substr($str, $cutOff);
        }

        $str = urlencode($str);
        return NULL == $str ? $default : $str;
    }

    /**
     * 重定向函数
     *
     * @access public
     * @param string $location 重定向路径
     * @param boolean $isPermanently 是否为永久重定向
     * @return void
     */
    public static function redirect($location, $isPermanently = true)
    {
        if($isPermanently)
        {
            header('HTTP/1.1 301 Moved Permanently');
            header("location: {$location}\n");
            die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
    <html><head>
    <title>301 Moved Permanently</title>
    </head><body>
    <h1>Moved Permanently</h1>
    <p>The document has moved <a href="' . $location . '">here</a>.</p>
    </body></html>');
        }
        else
        {
            header('HTTP/1.1 302 Found');
            header("location: {$location}\n");
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
        else if(!empty($_SERVER["REDIRECT_URL"]))
        {
            $path = $_SERVER["REDIRECT_URL"];

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

    /**
     * 分析response参数
     *
     * @access public
     * @param string $response 回执字符串
     * @return array
     */
    public static function getHttpResponse($response)
    {
        if(!$response)
        {
            return NULL;
        }

        str_replace("\r", "", $response);
        $rows = explode("\n", $response);

        $foundStatus = false;
        $foundInfo = false;
        $result = array();
        $lines = array();

        foreach($rows as $key => $line)
        {
            if(!$foundStatus)
            {
                if(0 === strpos($line, "HTTP/"))
                {
                    if("" == trim($rows[$key + 1]))
                    {
                        continue;
                    }
                    else
                    {
                        $status = explode(" ", str_replace("  "," ",$line));
                        $result['status'] = intval($status[1]);
                        $foundStatus = true;
                    }
                }
            }
            else
            {
                if(!$foundInfo)
                {
                    if("" != trim($line))
                    {
                        $status = explode(":", $line);
                        $name = strtolower(array_shift($status));
                        $data = implode(":", $status);
                        $result[trim($name)] = trim($data);
                    }
                    else
                    {
                        $foundInfo = true;
                    }
                }
                else
                {
                    $lines[] = $line;
                }
            }
        }

        $result['body'] = implode("\n", $lines);
        return $result;
    }

    /**
     * 发送HTTP请求
     *
     * @access public
     * @param string $url 请求的URL地址
     * @param string $agent 客户端代号
     * @param array $getData HTTP的GET值
     * @param array $postData HTTP的POST值
     * @param array $fileData HTTP的FILE值,用于传输文件
     * @param integer $timeOut 超时设置
     * @param string $host 指定对方主机名
     * @param string $ip 指定对方ip
     * @param integer $locationTimes 转向次数,内部使用
     * @return array
     */
    public static function httpSender($url,
                                      $agent = NULL,
                                      $getData = NULL,
                                      $postData = NULL,
                                      $fileData = NULL,
                                      $timeOut = 5,
                                      $host = NULL,
                                      $ip = NULL,
                                      $locationTimes = 0)
    {
        //check locationTimes
        if($locationTimes >= 3)
        {
            return NULL;
        }

        //get user agent
        $agent = (NULL === $agent) ? $_SERVER['HTTP_USER_AGENT'] : $agent;

        if($url)
        {
            $url = $url.(NULL === $getData ? NULL : ((false != strpos($url,"?") ? "&" : "?").http_build_query($getData)));
            $parsedUrl = parse_url($url);
            if(!isset($parsedUrl['path']))
            {
                $parsedUrl['path'] = '/';
            }
            $path = $parsedUrl['path'].(!empty($parsedUrl['query']) ? '?'.$parsedUrl['query'] : NULL);

            if (empty($parsedUrl['host']))
            {
                return false;
            }

            $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : 80;

            if(NULL != $postData || NULL != $fileData)
            {
                $request  = 'POST ' . $path;
            }
            else
            {
                $request  = 'GET ' . $path;
            }

            if (isset($parsedUrl['query']))
            {
                $request .= '?' . $parsedUrl['query'];
            }

            $request .= " HTTP/1.1\r\n";
            $request .= "Accept: */*\r\n";
            $request .= "User-Agent: " . $agent . "\r\n";
            $request .= "Host: " . (empty($host) ? $parsedUrl['host'] : $host) . (isset($parsedUrl['port']) ? ':'. $port : NULL) . "\r\n";
            $request .= "Connection: Keep-Alive\r\n";
            $request .= "Keep-Alive: 300\r\n";
            $request .= "Cache-Control: no-cache\r\n";
            $request .= "Connection: Close\r\n";

            if(NULL != $postData || NULL != $fileData)
            {
                if(NULL == $fileData)
                {
                    $content = http_build_query($postData);
                    $request .= "Content-Length: " . strlen($content) . "\r\n";
                    $request .= "Content-Type: application/x-www-form-urlencoded\r\n";
                    $request .= "\r\n";
                    $request .= $content;
                }
                else
                {
                    $boundary = "---------------------------" . md5(uniqid(time()));
                    $content = "\r\n".$boundary;

                    if(NULL != $postData)
                    {
                        foreach($postData as $key => $val)
                        {
                            $content .= "\r\nContent-Disposition: form-data; name=\"{$key}\"\r\n\r\n";
                            $content .= $val . "\r\n";
                            $content .= $boundary;
                        }
                    }

                    foreach($fileData as $key => $val)
                    {
                        $content .= "\r\nContent-Disposition: form-data; name=\"{$key}\"; filename=\"{$val}\"\r\n";
                        $content .= "Content-Type: " . mime_content_type($val) . "\r\n\r\n";
                        $content .= file_get_contents($val)."\r\n";
                        $content .= $boundary;
                    }

                    $content .= "--\r\n";

                    $request .= "Content-Length: " . strlen($content) . "\r\n";
                    $request .= "Content-Type: multipart/form-data; boundary={$boundary}";
                    $request .= "\r\n";

                    $request .= $content;
                }
            }
            else
            {
                $request .= "\r\n";
            }

            $socket = @fsockopen(empty($ip) ? $parsedUrl['host'] : $ip, $port, $errno, $errstr,$timeOut);
            if(!$socket)
            {
                return false;
            }

            //sending it
            fputs( $socket, $request);
            stream_set_timeout($socket, $timeOut);
            stream_set_blocking($socket,0);
            $info = stream_get_meta_data($socket);

            $data = "";

            //get response
            while ( ! feof ( $socket )  && !$info['timed_out'] )
            {
                $data .= fgets( $socket, 4096 );
            }

            fclose($socket);
            $response = self::getHttpResponse($data);

            if(NULL != $response)
            {
                //do location
                if(isset($response['location']))
                {
                    $tmpParsed = parse_url($response['location']);
                    if(empty($tmpParsed['host']))
                    {
                        $response['location'] = 'http://'.$parsedUrl['host'].$response['location'];
                    }
                    return  self::httpSender($response['location'],
                                             $agent,
                                             $getData,
                                             $postData,
                                             $fileData,
                                             $timeOut,
                                             $host,
                                             $ip,
                                             $locationTimes + 1);
                }
                else
                {
                    $response["url"] = $url;
                    return $response;
                }
            }
            else
            {
                return NULL;
            }
        }
        else
        {
            return NULL;
        }
    }

    /**
     * 动态获取网站根目录
     *
     * @access public
     * @return string
     */
    public static function getSiteRoot()
    {
        return substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')) . '/';
    }
}
