<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: I18n.php 106 2008-04-11 02:23:54Z magike.net $
 */

/**
 * I18n function
 *
 * @param string $string 需要翻译的文字
 * @return string
 */
function _t($string)
{
    if (func_num_args() <= 1) {
        return Typecho_I18n::translate($string);
    } else {
        $args = func_get_args();
        array_shift($args);
        return vsprintf(Typecho_I18n::translate($string), $args);
    }
}

/**
 * I18n function, translate and echo
 *
 * @param string $string 需要翻译并输出的文字
 * @return void
 */
function _e()
{
    $args = func_get_args();
    echo call_user_func_array('_t', $args);
}

/**
 * 针对复数形式的翻译函数
 * 
 * @param string $single 单数形式的翻译
 * @param string $plural 复数形式的翻译
 * @param integer $number 数字
 * @return string
 */
function _n($single, $plural, $number)
{
    return Typecho_I18n::ngettext($single, $plural, $number);
}

/**
 * 国际化字符翻译
 *
 * @package I18n
 */
class Typecho_I18n
{
    /**
     * 是否已经载入的标志位
     * 
     * @access private
     * @var boolean
     */
    private static $_loaded = false;
    
    /**
     * 语言文件
     *
     * @access private
     * @var string
     */
    private static $_lang = NULL;
    
    /**
     * 初始化语言文件
     * 
     * @access private
     * @return boolean
     */
    private static function init()
    {
        /** GetText支持 */
        require_once 'Typecho/I18n/GetText.php';
        self::$_loaded = new Typecho_I18n_GetText(self::$_lang);
        return true;
    }

    /**
     * 翻译文字
     * 
     * @access public
     * @param string $string 待翻译的文字
     * @return string
     */
    public static function translate($string)
    {
        self::$_lang && empty(self::$_loaded) && self::init();
        return self::$_lang ? self::$_loaded->translate($string) : $string;
    }
    
    /**
     * 针对复数形式的翻译函数
     * 
     * @param string $single 单数形式的翻译
     * @param string $plural 复数形式的翻译
     * @param integer $number 数字
     * @return string
     */
    public static function ngettext($single, $plural, $number)
    {
        self::$_lang && empty(self::$_loaded) && self::init();
        return self::$_lang ? self::$_loaded->ngettext($single, $plural, $number) : ($number > 1 ? $plural : $single);
    }
    
    /**
     * 词义化时间
     * 
     * @access public
     * @param string $from 起始时间
     * @param string $now 终止时间
     * @return string
     */
    public static function dateWord($from, $now)
    {
        $between = $now - $from;
        
        /** 如果是一天 */
        if ($between < 86400 && idate('d', $from) == idate('d', $now)) {
            /** 如果是一小时 */
            if ($between < 3600 && idate('H', $from) == idate('H', $now)) {
                /** 如果是一分钟 */
                if ($between < 60 && idate('i', $from) == idate('i', $now)) {
                    $second = idate('s', $now) - idate('s', $from);
                    return sprintf(_n('%d秒前', '%d秒前', $second), $second);
                }
                
                $min = idate('i', $now) - idate('i', $from);
                return sprintf(_n('%d分钟前', '%d分钟前', $min), $min);
            }
            
            $hour = idate('H', $now) - idate('H', $from);
            return sprintf(_n('%d小时前', '%d小时前', $hour), $hour);
        }
        
        /** 如果是昨天 */
        if ($between < 172800 && (idate('z', $from) + 1 == idate('z', $now) || idate('z', $from) > 2 + idate('z', $now))) {
            return date(_t('昨天 H:i'), $from);
        }
        
        /** 如果是一个星期 */
        if ($between < 604800 && idate('W', $from) == idate('W', $now)) {
            $day = intval($between / (3600 * 24));
            return sprintf(_n('%d天前', '%d天前', $day), $day);
        }
        
        /** 如果是 */
        if ($between < 31622400 && idate('Y', $from) == idate('Y', $now)) {
            return date(_t('n月j日'), $from);
        }
        
        return date(_t('Y年m月d日'), $from);
    }
    
    /**
     * 设置语言项
     * 
     * @access public
     * @param string $lang 配置信息
     * @return void
     */
    public static function setLang($lang)
    {
        self::$_lang = $lang;
    }
}
