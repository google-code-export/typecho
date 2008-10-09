<?php
/**
 * API方法,Typecho命名空间
 *
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * Typecho公用方法
 *
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Response
{
    /**
     * 解析ajax回执的内部函数
     * 
     * @access private
     * @param mixed $message 格式化数据
     * @return string
     */
    private static function _parseXml($message)
    {
        /** 对于数组型则继续递归 */
        if(is_array($message))
        {
            $result = '';
            
            foreach($message as $key => $val)
            {
                $tagName = is_int($key) ? 'item' : $key;
                $result .= '<' . $tagName . '>' . self::_parseAjaxResponse($val) . '</' . $tagName . '>';
            }
            
            return $result;
        }
        else
        {
            return '<![CDATA[' . $message . ']]>';
        }
    }
    
    /**
     * 打开输出缓冲区
     * 
     * @access public
     * @param boolean $gzipAble 是否打开gzip压缩
     * @return void
     */
    public static function obStart($gzipAble = false)
    {
        //开始监视输出区
        if($gzipAble && !empty($_SERVER['HTTP_ACCEPT_ENCODING'])
           && false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
        {
            ob_start("ob_gzhandler");
        }
        else
        {
            ob_start();
        }
    }
    
    /**
     * 在http头部请求中声明类型和字符集
     * 
     * @access public
     * @param string $contentType 文档类型
     * @param string $charset 字符集
     * @return void
     */
    public static function setContentType($contentType = 'text/html', $charset = 'UTF-8')
    {
        header('content-Type: ' . $contentType . ';charset= ' . $charset, true);
    }
    
    /**
     * 设置http头
     * 
     * @access public
     * @param string $name 名称 
     * @param string $value 对应值
     * @param boolean $replace 是否替换重复
     * @return void
     */
    public static function setHeader($name, $value, $replace = false)
    {
        header($name . ': ' . $value, $replace);
    }
    
    /**
     * 抛出ajax的回执信息
     * 
     * @access public
     * @param string $message 消息体
     * @param string $charset 信息编码
     * @return void
     */
    public static function throwXml($message, $charset = 'UTF-8')
    {
        /** 设置http头信息 */
        self::setContentType('text/xml', $charset);
        
        /** 构建消息体 */
        echo '<?xml version="1.0" encoding="' . $charset . '"?>',
        '<response>',
        self::_parseXml($message),
        '</response>';
        
        /** 终止后续输出 */
        exit;
    }

    /**
     * 重定向函数
     *
     * @access public
     * @param string $location 重定向路径
     * @param boolean $isPermanently 是否为永久重定向
     * @return void
     */
    public static function redirect($location, $isPermanently = false)
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
     * 返回来路
     *
     * @access protected
     * @param string $anchor 锚点地址
     * @return void
     */
    public static function goBack($anchor = NULL)
    {
        //判断来源
        if(!empty($_SERVER['HTTP_REFERER']))
        {
            self::redirect($_SERVER['HTTP_REFERER'] . $anchor, false);
        }
    }
}
