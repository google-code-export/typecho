<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

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
        $rows = explode("\n",$exceptionString);
        $str = '<table width="100%" cellspacing="1" cellpadding="5" border="0" style="background:#777;font-size:10pt;font-family:verdana,Helvetica,sans-serif">';
        $i = 0;
        
        foreach($rows as $row)
        {
            if(0 == $i)
            {
                $items = explode(' ',$row);
                array_shift($items);
                $message = implode(' ',$items);
                
                $str .= '<tr><td width=5% style="background:#777;color:#FFF">&nbsp</td><td style="background:#777;color:#FFF">'.$row.'</td></tr>';
            }
            else if(1 == $i)
            {
                $str .= '<tr><td style="background:#FFFFAA"><strong>Trace</strong></td><td align="center" style="background:#FFFFAA">Message</td></tr>';
            }
            else
            {
                $items = explode(' ',$row);
                $num = $items[0];
                array_shift($items);
                $message = implode(' ',$items);
                
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
            <h1 style="font-family:verdana,Helvetica,sans-serif;font-size:20px;background:#AA0000;padding:10px;color:#FFF">'
            . $this->message . '</h1>';
            return self::parse(parent::__toString()).'</body></html>';
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
    header('content-type: text/html;charset=UTF-8');
    if($exception instanceof TypechoException)
    {
        die((string) $exception);
    }
    else
    {
        die(TypechoException::parse((string) $exception));
    }
}
