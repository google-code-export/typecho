<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Exception.php 106 2008-04-11 02:23:54Z magike.net $
 */

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
     * 默认异常页面
     * 
     * @access private
     * @var array
     */
    private static $_handles = array();
    
    /**
     * 是否现实异常
     * 
     * @access private
     * @var boolean
     */
    private static $_display;

    /**
     * 异常基类构造函数,重载以增加$code的默认参数
     *
     * @param mixed $message 异常消息
     * @param integer $code 异常代码
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        if (empty($message) && function_exists('error_get_last')) {
            /** 默认信息为上一条错误 */
            $error = error_get_last();
            $message = $error['message'];
        }
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

        foreach ($rows as $row) {
            if (0 == $i) {
                $items = explode(' ', $row);
                array_shift($items);
                $message = implode(' ', $items);

                $str .= '<tr><td width=5% style="background:#777;color:#FFF">&nbsp</td><td style="background:#777;color:#FFF">' . $row . '</td></tr>';
            } else if (1 == $i) {
                $str .= '<tr><td style="background:#FFFFAA"><strong>Trace</strong></td><td align="center" style="background:#FFFFAA">Message</td></tr>';
            } else {
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
        if (self::$_display) {
            echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title>Exception</title>
</head><body>
            <h1 style="font-family:verdana,Helvetica,sans-serif;font-size:12px;background:#AA0000;padding:10px;color:#FFF">'
            . $this->code . ' : ' . $this->message . '</h1>';
            return self::parse(parent::__toString()) . '</body></html>';
        } else if (isset(self::$_handles[$this->code]) && is_file(self::$_handles[$this->code])) {
            require_once self::$_handles[$this->code];
            return;
        } else {
            //TODO 显示需求
        }
    }
    
    /**
     * 设置是否显示异常
     * 
     * @access public
     * @param boolean $display 是否显示异常
     * @return void
     */
    public static function setDisplay($display = true)
    {
        self::$_display = $display;
    }
    
    /**
     * 设置默认异常处理页面
     * 
     * @access public
     * @param array $handles 配置信息
     * @return void
     */
    public static function set404($handle)
    {
        //目前仅开放404接口
        self::$_handles[self::NOTFOUND] = $handle;
    }
}

/** 设置异常截获函数 */
set_exception_handler('exceptionHandler');

/**
 * 异常截获函数
 *
 * @param string $exception
 * @return void
 */
function exceptionHandler($exception)
{
    @ob_clean();
    
    switch ($exception->getCode()) {
        case Typecho_Exception::FORBIDDEN:
            header('HTTP/1.1 403 Forbidden');
            break;
            
        case Typecho_Exception::NOTFOUND:
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            break;
            
        case Typecho_Exception::RUNTIME:
            header('HTTP/1.1 500 Internal Server Error');
            break;
            
        case Typecho_Exception::UNVAILABLE:
            header('HTTP/1.1 503 Service Unvailable');
            break;
            
        default:
            break;
    }
    
    if ($exception instanceof Typecho_Exception) {
        /** 显示调用__toString,修正PHP 5.2之前的bug */
        die($exception->__toString());
    } else {
        die(Typecho_Exception::parse($exception->__toString()));
    }
}
