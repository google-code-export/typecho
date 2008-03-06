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
define('__TYPECHO_EXCEPTION_DIR__', dirname(__FILE__) . '/../../var/error/');

/**
 * Typecho异常基类
 * 主要重载异常打印函数
 *
 */
class TypechoException extends Exception
{
    /**
     * 异常基类构造函数,重载以增加$code的默认参数
     * 
     * @param string $message 异常消息
     * @param integer $code 异常代码
     * @return void
     */
    public function __construct($message, $code = 0)
    {
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
        $str = '<table width="100%" cellspacing="1" cellpadding="5" border="0" style="background:#777;font-size:10pt;font-family:verdana,Helvetica,sans-serif">';
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
                $str .= '<tr><td style="background:#FFFFAA"><strong>' . _t('回溯') . '</strong></td><td align="center" style="background:#FFFFAA">' . _t('消息') . '</td></tr>';
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
<title>' . _t('系统截获异常') . '</title>
</head><body>
            <h1 style="font-family:verdana,Helvetica,sans-serif;font-size:20px;background:#AA0000;padding:10px;color:#FFF">'
            . $this->message . '</h1>';
            return self::parse(parent::__toString()) . '</body></html>';
    }
}

/**
 * 设置异常截获函数
 * 
 */
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
            case __TYPECHO_EXCEPTION_404__:
            case __TYPECHO_EXCEPTION_500__:
            case __TYPECHO_EXCEPTION_503__:
            {
                require_once __TYPECHO_EXCEPTION_DIR__ . $exception->getCode() . '.php';
                break;
            }
            default:
            {
                require_once __TYPECHO_EXCEPTION_DIR__ . '500.php';
                break;
            }
        }
        
        die();
    }
}
