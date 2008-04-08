<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义异常代码 **/
define('__TYPECHO_EXCEPTION_403__', 403);     //403权限错误
define('__TYPECHO_EXCEPTION_404__', 404);     //404页面不存在
define('__TYPECHO_EXCEPTION_500__', 500);     //500服务器内部错误,用于标记未知的系统错误
define('__TYPECHO_EXCEPTION_503__', 503);     //503服务器不可用,用于标记数据连接错误

/** 定义异常截获页面地址 **/
define('__TYPECHO_EXCEPTION_DIR__', __TYPECHO_ROOT_DIR__ . '/var/error');

/**
 * Typecho异常基类
 * 主要重载异常打印函数
 * 
 * @package Exception
 */
class TypechoException extends Exception
{
    /**
     * 异常基类构造函数,重载以增加$code的默认参数
     * 
     * @param mixed $message 异常消息
     * @param integer $code 异常代码
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        $message = is_array($message) ? implode(',', $message) : $message;
        parent::__construct($message, $code);
    }
    
    /**
     * 解析异常字符串
     * 
     * @param string $exceptionString 异常字符串
     * @return string
     */
    static public function parse($exceptionString)
    {
        $exceptionString = trim($exceptionString);
        $rows = explode("\n", $exceptionString);
        $str = '<table width="100%" cellspacing="1" cellpadding="5" border="0" style="background:#777;font-size:8pt;font-family:verdana,Helvetica,sans-serif">';
        $i = 0;
        
        foreach($rows as $row)
        {
            if(0 == $i)
            {
                $items = explode(' ', $row);
                array_shift($items);
                $message = implode(' ', $items);
                
                $str .= '<tr><td width=5% style="background:#777;color:#FFF">&nbsp</td><td style="background:#777;color:#FFF">' . $row . '</td></tr>';
            }
            else if(1 == $i)
            {
                $str .= '<tr><td style="background:#FFFFAA"><strong>Trace</strong></td><td align="center" style="background:#FFFFAA">Message</td></tr>';
            }
            else
            {
                $items = explode(' ', $row);
                $num = $items[0];
                array_shift($items);
                $message = implode(' ', $items);
                
                $str .= "<tr><td style=\"background:#FFF\"><strong>{$num}</strong></td><td style=\"background:#FFF\">{$message}</td></tr>";
            }
            $i ++;
        }
        
        $str .= '</table>';
        
        return $str;
    }
    
    /**
     * 打印异常错误
     * 
     * @return void
     */
    public function __toString()
    {
            echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title>Exception</title>
</head><body>
            <h1 style="font-family:verdana,Helvetica,sans-serif;font-size:12px;background:#AA0000;padding:10px;color:#FFF">'
            . $this->message . '</h1>';
            return self::parse(parent::__toString()) . errorHandler() . '</body></html>';
    }
}

/**
 * 设置异常截获函数
 * 
 */
set_exception_handler('exceptionHandler');

/**
 * 设置错误截获函数
 * 
 */
set_error_handler('errorHandler');

/**
 * 异常截获函数
 * 
 * @param string $exception
 * @return void
 */
function exceptionHandler($exception)
{
    @ob_clean();
    
    if(__TYPECHO_DEBUG__)
    {
        if($exception instanceof TypechoException)
        {
            die((string) $exception);
        }
        else
        {
            die(TypechoException::parse((string) $exception));
        }
    }
    else
    {
        switch($exception->getCode())
        {
            case __TYPECHO_EXCEPTION_403__:
            {
                header('HTTP/1.1 403 Forbidden');
                require_once __TYPECHO_EXCEPTION_DIR__ . '/403.php';
                break;
            }
            case __TYPECHO_EXCEPTION_404__:
            {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                require_once __TYPECHO_EXCEPTION_DIR__ . '/404.php';
                break;
            }
            case __TYPECHO_EXCEPTION_500__:
            {
                header('HTTP/1.1 500 Internal Server Error');
                require_once __TYPECHO_EXCEPTION_DIR__ . '/500.php';
                break;
            }
            case __TYPECHO_EXCEPTION_503__:
            {
                header('HTTP/1.1 503 Service Unvailable');
                require_once __TYPECHO_EXCEPTION_DIR__ . '/503.php';
                break;
            }
            default:
            {
                require_once __TYPECHO_EXCEPTION_DIR__ . '/error.php';
                break;
            }
        }
        
        die();
    }
}

/**
 * 错误截获函数
 * 
 * @param integer $errno 错误代码
 * @param string $errstr 错误描述
 * @param string $errfile 错误文件
 * @param integer $errline 错误代码行
 * @return void
 */
function errorHandler($errno = NULL, $errstr = NULL, $errfile = NULL, $errline = NULL)
{
    static $errors;
    
    if(empty($errors))
    {
        $errors = array();
    }

    if(__TYPECHO_DEBUG__)
    {
        $errorWord = array (
            E_ERROR              => 'Error',
            E_WARNING            => 'Warning',
            E_PARSE              => 'Parsing Error',
            E_NOTICE             => 'Notice',
            E_CORE_ERROR         => 'Core Error',
            E_CORE_WARNING       => 'Core Warning',
            E_COMPILE_ERROR      => 'Compile Error',
            E_COMPILE_WARNING    => 'Compile Warning',
            E_USER_ERROR         => 'User Error',
            E_USER_WARNING       => 'User Warning',
            E_USER_NOTICE        => 'User Notice',
            E_STRICT             => 'Runtime Notice',
            E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
            );
        
        if(empty($errno))
        {
            if(!empty($errors))
            {
                $str = '<table width="100%" cellspacing="1" cellpadding="5" border="0" style="background:#777;font-size:8pt;font-family:verdana,Helvetica,sans-serif;margin-bottom:20px;">';
                $str .= '<tr><td style="background:#777;color:#FFF" align="center" colspan="4">System caught error</td></tr>';
                $str .= '<tr><td align="center" style="background:#FFFFAA">Error</td>
                <td align="center" style="background:#FFFFAA">File</td>
                <td align="center" style="background:#FFFFAA">Line</td>
                <td align="center" style="background:#FFFFAA">Message</td>
                </tr>';
                
                foreach($errors as $error)
                {
                    list($errorWord, $errno, $errfile, $errline, $errstr) = $error;
                    $str .= '<tr><td style="background:#FFF">' . $errorWord . '</td>
                    <td style="background:#FFF">' . $errfile . '</td>
                    <td style="background:#FFF">' . $errline . '</td>
                    <td style="background:#FFF">' . $errstr . '</td>
                    </tr>';
                }
                
                $str .= '</table>';
                
                echo $str;
            }
        }
        else
        {
            if(array_key_exists($errno, $errorWord))
            {
                $errorWord = $errorWord[$errno];
            }
            else
            {
                $errorWord = 'Unkown Error';
            }
            
            $errors[] = array($errorWord, $errno, $errfile, $errline, $errstr);
            echo $errorWord . "[$errno]: [file:$errfile][line:$errline] $errstr<br />\n";
        }
    }
}
