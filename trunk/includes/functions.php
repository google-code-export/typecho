<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */


function typechoStripslashesDeep($value)
{
    return is_array($value) ? array_map('typechoStripslashesDeep', $value) : stripslashes($value);
}

/**
 * 生成缩略名
 * 
 * @param string $str 需要生成缩略名的字符串
 * @param string $default 默认的缩略名
 * @return string
 */
function typechoSlugName($str, $default)
{
    $str = str_replace(array("'",":","\\","/"),"",$str);
    $str = str_replace(array("+",","," ",".","?","=","&","!","<",">","(",")","[","]","{","}"),"-",$str);

    //cut string
    //from end
    $length = strlen($str);
    $i = $length;
    $cutOff = 0;

    while($i > 0)
    {
        $i--;
        if('-' == $str[$i])
        {
            $cutOff ++;
        }
        else
        {
            break;
        }
    }

    if($cutOff)
    {
        $str = substr($str,0,- $cutOff);
    }

    //from start
    $length = strlen($str);
    $i = 0;
    $cutOff = 0;

    while($i < $length)
    {
        if('-' == $str[$i])
        {
            $cutOff ++;
        }
        else
        {
            break;
        }
        $i++;
    }

    if($cutOff)
    {
        $str = substr($str,$cutOff);
    }

    $str = urlencode($str);
    return NULL == $str ? $default : $str;
}

/**
 * I18n function
 *
 * @param string $string
 * @return string
 */
function _t()
{
    if(func_num_args() <= 1)
    {
        return func_get_arg(0);
    }
    else
    {
        $args = func_get_args();
        $string = array_shift($args);
        return vsprintf($string, $args);
    }
}

/**
 * I18n function, translate and echo
 *
 * @param string $string
 * @return string
 */
function _e()
{
    $args = func_get_args();
    echo call_user_func_array('_t', $args);
}
