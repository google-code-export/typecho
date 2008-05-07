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
}
