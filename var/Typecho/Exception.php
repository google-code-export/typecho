<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Exception.php 106 2008-04-11 02:23:54Z magike.net $
 */

/** 配置管理 */
require_once 'Typecho/Config.php';

/**
 * Typecho异常基类
 * 主要重载异常打印函数
 *
 * @package Exception
 */
class Typecho_Exception extends Exception
{
    /** 权限异常 */
    const FORBIDDEN = 403;
    
    /** 记录不存在异常 */
    const NOTFOUND = 404;
    
    /** 程序运行异常 */
    const RUNTIME = 500;
    
    /** 服务器不可用 */
    const UNVAILABLE = 503;

    /**
     * 内部消息对象,支持数组类型
     *
     * @access private
     * @var array
     */
    private $_messages;

    /**
     * 异常基类构造函数,重载以增加$code的默认参数
     *
     * @param mixed $message 异常消息
     * @param integer $code 异常代码
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        $this->_messages = is_array($message) ? $message : array($message);
        $message = is_array($message) ? implode(',', $message) : $message;
        parent::__construct($message, $code);
    }

    /**
     * 解析异常字符串
     *
     * @param string $exceptionString 异常字符串
     * @return string
     */
    public static function parse($exceptionString)
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
     * 重载获取消息方法,支持多列输出
     *
     * @access public
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
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
            . $this->code . ' : ' . $this->message . '</h1>';
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

    if(!Typecho_Config::get('Exception'))
    {    
        if($exception instanceof Typecho_Exception)
        {
            /** 显示调用__toString,修正PHP 5.2之前的bug */
            die($exception->__toString());
        }
        else
        {
            die(Typecho_Exception::parse($exception->__toString()));
        }
    }
    else
    {
        switch($exception->getCode())
        {
            case Typecho_Exception::FORBIDDEN:
                header('HTTP/1.1 403 Forbidden');
                $handle = '_403';
                break;
            case Typecho_Exception::NOTFOUND:
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                $handle = '_404';
                break;
            case Typecho_Exception::RUNTIME:
                header('HTTP/1.1 500 Internal Server Error');
                $handle = '_500';
                break;
            case Typecho_Exception::UNVAILABLE:
                header('HTTP/1.1 503 Service Unvailable');
                $handle = '_503';
                break;
            default:
                $handle = '_error';
                break;
        }

        require_once Typecho_Config::get('Exception')->{$handle};
        die();
    }
}

/** 兼容PHP5.2以前的错误级别,E_RECOVERABLE_ERROR为PHP5.2的新增错误类型 */
if(!defined('E_RECOVERABLE_ERROR'))
{
    define('E_RECOVERABLE_ERROR', 4096);
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

    if(!Typecho_Config::get('Exception'))
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
