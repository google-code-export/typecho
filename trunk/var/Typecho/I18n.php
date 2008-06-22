<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: I18n.php 106 2008-04-11 02:23:54Z magike.net $
 */

/** 配置管理 */
require_once 'Typecho/Config.php';

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
        return Typecho_I18n::translate($string);
    }
    else
    {
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
 * 国际化字符翻译
 *
 * @package I18n
 */
class Typecho_I18n
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
        if(Typecho_Config::get('I18n'))
        {
            if(!self::$_loaded)
            {
                /** GetText支持 */
                require_once 'Typecho/I18n/GetText.php';
                Typecho_I18n_GetText::init(Typecho_Config::get('I18n'));
                self::$_loaded = true;
            }

            return isset(Typecho_I18n_GetText::$strings[$string]) ? Typecho_I18n_GetText::$strings[$string] : $string;
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
                'Sun'       => _t('星期日'),
                'Mon'       => _t('星期一'),
                'Tue'       => _t('星期二'),
                'Wed'       => _t('星期三'),
                'Thu'       => _t('星期四'),
                'Fri'       => _t('星期五'),
                'Sat'       => _t('星期六'),
                
                /** 时间 */
                'am'        => _t('上午'),
                'pm'        => _t('下午')
            );
            
            $localDateDestCache = array_values($map);
            $localDateSourceCache = array_keys($map);
        }
        
        $between = $now - $from;
        
        /** 如果是一天 */
        if($between < 86400 && idate('d', $from) == idate('d', $now))
        {
            /** 如果是一小时 */
            if($between < 3600 && idate('H', $from) == idate('H', $now))
            {                
                /** 如果是一分钟 */
                if($between < 60 && idate('i', $from) == idate('i', $now))
                {
                    return _t('%d秒前', idate('s', $now) - idate('s', $from));
                }
                
                return _t('%d分钟前', idate('i', $now) - idate('i', $from));
            }
            
            return _t('%d小时前', idate('H', $now) - idate('H', $from));
        }
        
        /** 如果是昨天 */
        if($between < 172800 && (idate('z', $from) + 1 == idate('z', $now) || idate('z', $from) > 2 + idate('z', $now)))
        {
            return str_replace($localDateSourceCache, $localDateDestCache, date(_t('昨天a g:i'), $from));
        }
        
        /** 如果是一个星期 */
        if($between < 604800 && idate('W', $from) == idate('W', $now))
        {
            return str_replace($localDateSourceCache, $localDateDestCache, date('D', $from));
        }
        
        /** 如果是 */
        if($between < 31622400 && idate('Y', $from) == idate('Y', $now))
        {
            return str_replace($localDateSourceCache, $localDateDestCache, date(_t('n月j日'), $from));
        }
        
        return date(_t('Y年m月d日'), $from);
    }
}
