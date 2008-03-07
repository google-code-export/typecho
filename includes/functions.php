<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

function typechoStripslashesDeep($value)
{
    return is_array($value) ? array_map('typechoStripslashesDeep', $value) : stripslashes($value);
}

/**
 * 适用于utf8的字符串函数
 */
if(function_exists('mb_get_info'))
{
	/**
	 * UTF-8截字函数
	 *
	 * @param string $str
	 * @param integer $start
	 * @param integer $length
	 * @param string $trim
	 * @return string
	 */
	function typechoSubStr($str,$start,$length,$trim = "...")
	{
		$iLength = mb_strlen($str);
		$str = mb_substr($str,$start,$length);
		return ($length < $iLength - $start) ? $str.$trim : $str;
	}
	
	/**
	 * UTF-8字符串长度函数
	 *
	 * @param string $str
	 * @return integer
	 */
	function typechoStrLen($str)
	{
		return mb_strlen($str);
	}
}
else
{
	/**
	 * UTF-8截字函数
	 *
	 * @param string $str
	 * @param integer $start
	 * @param integer $length
	 * @param string $trim
	 * @return string
	 */
	function typechoSubStr($str,$start,$length,$trim = "...")
	{
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
		$str = join("",array_slice($info[0],$start,$length));
		return ($length < (sizeof($info[0]) - $start)) ? $str.$trim : $str;
	}

	/**
	 * UTF-8字符串长度函数
	 *
	 * @param string $str
	 * @return integer
	 */
	function typechoStrLen($str)
	{
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
		return sizeof($info[0]);
	}
}

/**
 * 生成缩略名
 * 
 * @param string $str 需要生成缩略名的字符串
 * @param string $default 默认的缩略名
 * @return string
 */
function typechoSlugName($str, $default)
{
    $str = str_replace(array("'",":","\\","/"),"",$str);
    $str = str_replace(array("+",","," ",".","?","=","&","!","<",">","(",")","[","]","{","}"),"-",$str);

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
        $str = substr($str,0,- $cutOff);
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
        $str = substr($str,$cutOff);
    }

    $str = urlencode($str);
    return NULL == $str ? $default : $str;
}

/**
 * 重定向函数
 *
 * @param string $location 重定向路径
 * @param boolean $isPermanently 是否为永久重定向
 * @return void
 */
function typechoRedirect($location, $isPermanently = true)
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
 * @return string
 */
function typechoGetPathInfo()
{
    if(!empty($_SERVER['PATH_INFO']) || NULL != getenv('PATH_INFO'))
	{
		$pathInfo = empty($_SERVER['PATH_INFO']) ? getenv('PATH_INFO') : $_SERVER['PATH_INFO'];
		if(0 === strpos($pathInfo,$_SERVER['SCRIPT_NAME']) && 0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
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
 * @return string
 */
function typechoGetClientIp()
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
 * @return array
 */
function typechoGetHttpResponse($response)
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

function typechoHttpSender($url,
                           $agent = NULL,
                           $getData = NULL,
                           $postData = NULL,
                           $fileData = NULL,
                           $timeOut = 5,
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
        $request .= "Host: " . $parsedUrl['host'] . (isset($parsedUrl['port']) ? ':'. $port : NULL) . "\r\n";
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
                    $content .= mgReadFile($val)."\r\n";
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
        
        $socket = @fsockopen($parsedUrl['host'], $port, $errno, $errstr,$timeOut);
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
        $response = typechoGetHttpResponse($data);

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
                return mgHttpSender($response['location'],$agent,$getData,$postData,$fileData,$timeOut,$locationTimes+1);
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
