<?php

/**
 * 标记格式转换
 *
 * @category typecho
 * @package Text
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Text
{
    /**
     * 将标记格式转换为html
     * 
     * @access public
     * @param string $text 输入文本
     * @return string
     */
    public static function encode($text)
    {
        /** Typecho_Text_Encode */
        require_once 'Typecho/Text/Encode.php';
        
        $encoder = new Typecho_Text_Encode($text);
        return $encoder->__toString();
    }
    
    /**
     * 将html转换为标记格式
     * 
     * @access public
     * @param string $html 输入的html
     * @return string
     */
    public static function decode($html)
    {
        /** Typecho_Text_Decode */
        require_once 'Typecho/Text/Decode.php';
        
        $decoder = new Typecho_Text_Decode($html);
        return $decoder->__toString();
    }
}
