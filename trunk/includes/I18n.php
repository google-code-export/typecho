<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: I18n.php 106 2008-04-11 02:23:54Z magike.net $
 */

/** GetText支持 */
require_once 'I18n/GetText.php';

/**
 * I18n function
 *
 * @param string $string 需要翻译的文字
 * @return string
 */
function _t($string)
{
    if(func_num_args() <= 1)
    {
        return TypechoI18n::translate($string);
    }
    else
    {
        $args = func_get_args();
        array_shift($args);
        return vsprintf(TypechoI18n::translate($string), $args);
    }
}

/**
 * I18n function, translate and echo
 *
 * @param string $string 需要翻译并输出的文字
 * @return string
 */
function _e()
{
    $args = func_get_args();
    echo call_user_func_array('_t', $args);
}

/**
 * 国际化字符翻译
 *
 * @package I18n
 */
class TypechoI18n
{
    private $_loaded = false;

    /**
     * 翻译文字
     * 
     * @access public
     * @param string $string 待翻译的文字
     * @return string
     */
    public static function translate($string)
    {
        if(__TYPECHO_I18N_LANGUAGE__)
        {
            if(!self::$_loaded)
            {
                TypechoGetText::init(__TYPECHO_I18N_LANGUAGE__);
                self::$_loaded = true;
            }

            return isset(TypechoGetText::$strings[$string]) ? TypechoGetText::$strings[$string] : $string;
        }
        else
        {
            return $string;
        }
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
        static $localDateDestCache, $localDateSourceCache;
        
        if(empty($localDateDestCache) || empty($localDateSourceCache))
        {
            $map = array(
                /** 星期 */
                'Sun'       => _t('周日'),
                'Mon'       => _t('周一'),
                'Tue'       => _t('周二'),
                'Wed'       => _t('周三'),
                'Thu'       => _t('周四'),
                'Fri'       => _t('周五'),
                'Sat'       => _t('周六'),
                
                /** 月份 */
                'Jan'       => _t('一月'),
                'Feb'       => _t('二月'),
                'Mar'       => _t('三月'),
                'Apr'       => _t('四月'),
                'May'       => _t('五月'),
                'Jun'       => _t('六月'),
                'Jul'       => _t('七月'),
                'Aug'       => _t('八月'),
                'Sep'       => _t('九月'),
                'Oct'       => _t('十月'),
                'Nov'       => _t('十一月'),
                'Dec'       => _t('十二月'),
                
                /** 日期 */
                '1st'       => _t('一日'),
                '2nd'       => _t('二日'),
                '3rd'       => _t('三日'),
                '4th'       => _t('四日'),
                '5th'       => _t('五日'),
                '6th'       => _t('六日'),
                '7th'       => _t('七日'),
                '8th'       => _t('八日'),
                '9th'       => _t('九日'),
                '10th'      => _t('十日'),
                '11st'      => _t('十一日'),
                '12nd'      => _t('十二日'),
                '13rd'      => _t('十三日'),
                '14th'      => _t('十四日'),
                '15th'      => _t('十五日'),
                '16th'      => _t('十六日'),
                '17th'      => _t('十七日'),
                '18th'      => _t('十八日'),
                '19th'      => _t('十九日'),
                '20th'      => _t('二十日'),
                '21st'      => _t('二十一日'),
                '22nd'      => _t('二十二日'),
                '23rd'      => _t('二十三日'),
                '24th'      => _t('二十四日'),
                '25th'      => _t('二十五日'),
                '26th'      => _t('二十六日'),
                '27th'      => _t('二十七日'),
                '28th'      => _t('二十八日'),
                '29th'      => _t('二十九日'),
                '30th'      => _t('三十日'),
                '31st'      => _t('三十一日'),
                
                /** 时间 */
                'am'        => _t('上午'),
                'pm'        => _t('下午')
            );
            
            $localDateDestCache = array_values($map);
            $localDateSourceCache = array_keys($map);
        }
        
        $between = $now - $from;
        
        /** 如果是一天 */
        if($between < 86400 && date('d', $from) == date('d', $now))
        {
            return str_replace($localDateSourceCache, $localDateDestCache, date(_t('a g:i'), $from));
        }
        
        /** 如果是一个星期 */
        if($between < 604800 && date('W', $from) == date('W', $now))
        {
            return str_replace($localDateSourceCache, $localDateDestCache, date('D', $from));
        }
        
        /** 如果是 */
        if($between < 31622400 && date('Y', $from) == date('Y', $now))
        {
            return str_replace($localDateSourceCache, $localDateDestCache, date('M jS', $from));
        }
        
        return date(_t('Y年m月d日'), $from);
    }
}
