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
class Typecho_Common
{
    /** 默认不解析的标签列表 */
    const LOCKED_HTML_TAG = 'code|script';

    /**
     * 锁定的代码块
     *
     * @access private
     * @var array
     */
    private static $_lockedBlocks = array();

    /**
     * 锁定标签回调函数
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public static function __lockHTML(array $matches)
    {
        $guid = uniqid(time());
        self::$_lockedBlocks[$guid] = $matches[0];
        return $guid;
    }

    /**
     * 注册自动载入函数
     * 
     * @access public
     * @return void
     */
    public static function registerAutoLoad()
    {
        /** 设置自动载入函数 */
        function __autoLoad($className)
        {
            require_once dirname(__FILE__) . '/../' . str_replace('_', '/', $className) . '.php';
        }
    }
    
    /**
     * 强行关闭魔术引号功能
     * 
     * @access public
     * @return void
     */
    public static function forceDisableMagicQuotesGPC()
    {
        /** 兼容php6 */
        if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            $_GET = self::stripslashesDeep($_GET);
            $_POST = self::stripslashesDeep($_POST);
            $_COOKIE = self::stripslashesDeep($_COOKIE);

            reset($_GET);
            reset($_POST);
            reset($_COOKIE);
        }
    }
    
    /**
     * 如果时区不存在,设置一个默认时区
     * 此方法用于修正某些未设置时区的错误
     * 
     * @access public
     * @param string $timezone 时区名称
     * @return void
     */
    public static function setDefaultTimezone($timezone = 'UTC')
    {
        if(!ini_get("date.timezone") && function_exists("date_default_timezone_set"))
        {
            @date_default_timezone_set($timezone);
        }
    }

    /**
     * 递归去掉数组反斜线
     *
     * @access public
     * @param mixed $value
     * @return mixed
     */
    public static function stripslashesDeep($value)
    {
        return is_array($value) ? array_map(array('Typecho_API', 'stripslashesDeep'), $value) : stripslashes($value);
    }

    /**
     * 抽取多维数组的某个元素,组成一个新数组,使这个数组变成一个扁平数组
     * 使用方法:
     * <code>
     * <?php
     * $fruit = array(array('apple' => 2, 'banana' => 3), array('apple' => 10, 'banana' => 12));
     * $banana = Typecho_Common::arrayFlatten($fruit, 'banana');
     * print_r($banana);
     * //outputs: array(0 => 3, 1 => 12);
     * ?>
     * </code>
     *
     * @access public
     * @param array $value 被处理的数组
     * @param string $key 需要抽取的键值
     * @return array
     */
    public static function arrayFlatten(array $value, $key)
    {
        $result = array();

        if($value)
        {
            foreach($value as $inval)
            {
                if(is_array($inval) && isset($inval[$key]))
                {
                    $result[] = $inval[$key];
                }
                else
                {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * 自闭合html修复函数
     * 使用方法:
     * <code>
     * $input = '这是一段被截断的html文本<a href="#"';
     * echo Typecho_Common::fixHtml($input);
     * //output: 这是一段被截断的html文本
     * </code>
     *
     * @access public
     * @param string $string 需要修复处理的字符串
     * @return string
     */
    public static function fixHtml($string)
    {
        //关闭自闭合标签
        $startPos = strrpos($string, "<");
        $trimString = substr($string, $startPos);

        if(false === strpos($trimString, ">"))
        {
            $string = substr($string, 0, $startPos);
        }

        //非自闭合html标签列表
        preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
        preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);

        if(!empty($startTags[1]) && is_array($startTags[1]))
        {
            krsort($startTags[1]);
            $closeTagsIsArray = is_array($closeTags[1]);
            foreach($startTags[1] as $key => $tag)
            {
                $attrLength = strlen($startTags[2][$key]);
                if($attrLength > 0 && "/" == trim($startTags[2][$key][$attrLength - 1]))
                {
                    continue;
                }
                if(!empty($closeTags[1]) && $closeTagsIsArray)
                {
                    if(false !== ($index = array_search($tag, $closeTags[1])))
                    {
                        unset($closeTags[1][$index]);
                        continue;
                    }
                }
                $string .= "</{$tag}>";
            }
        }

        return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $string);
    }

    /**
     * 去掉字符串中的html标签
     * 使用方法:
     * <code>
     * $input = '<a href="http://test/test.php" title="example">hello</a>';
     * $output = Typecho_Common::stripTags($input, <a href="">);
     * echo $output;
     * //display: '<a href="http://test/test.php">hello</a>'
     * </code>
     *
     * @access public
     * @param string $string 需要处理的字符串
     * @param string $allowableTags 需要忽略的html标签
     * @return string
     */
    public static function stripTags($string, $allowableTags = NULL)
    {
        if(!empty($allowableTags) && preg_match_all("/\<([a-z]+)([^>]*)\>/is", $allowableTags, $tags))
        {
            $normalizeTags = '<' . implode('><', $tags[1]) . '>';
            $string = strip_tags($string, $normalizeTags);
            $attributes = array_map('trim', $tags[2]);
            
            $allowableAttributes = array();
            foreach($attributes as $key => $val)
            {
                $allowableAttributes[$tags[1][$key]] = array();
                if(preg_match_all("/([a-z]+)\s*\=/is", $val, $vals))
                {
                    foreach($vals[1] as $attribute)
                    {
                        $allowableAttributes[$tags[1][$key]][] = $attribute;
                    }
                }
            }
            
            foreach($tags[1] as $key => $val)
            {
                $match = "/\<{$val}(\s*[a-z]+\s*\=\s*[\"'][^\"']*[\"'])*\s*\>/is";
                
                if(preg_match_all($match, $string, $out))
                {
                    foreach($out[0] as $startTag)
                    {
                        if(preg_match_all("/([a-z]+)\s*\=\s*[\"'][^\"']*[\"']/is", $startTag, $attributesMatch))
                        {
                            $replace = $startTag;
                            foreach($attributesMatch[1] as $attribute)
                            {
                                if(!in_array($attribute, $allowableAttributes[$val]))
                                {
                                    $startTag = preg_replace("/\s*{$attribute}\s*=\s*[\"'][^\"']*[\"']/is", '', $startTag);
                                }
                            }
                            
                            $string = str_replace($replace, $startTag, $string);
                        }
                    }
                }
            }
            
            return $string;
        }
        else
        {
            return strip_tags($string);
        }
    }

    /**
     * 过滤用于搜索的字符串
     * 
     * @access public
     * @param string $query 搜索字符串
     * @return string
     */
    public static function filterSearchQuery($query)
    {
        return str_replace(array('%', '?', '*', '/'), '', $query);
    }

    /**
     * 宽字符串截字函数
     *
     * @access public
     * @param string $str 需要截取的字符串
     * @param integer $start 开始截取的位置
     * @param integer $length 需要截取的长度
     * @param string $trim 截取后的截断标示符
     * @return string
     */
    public static function subStr($str, $start, $length, $trim = "...")
    {
        if(function_exists('mb_get_info'))
        {
            $iLength = mb_strlen($str, __TYPECHO_CHARSET__);
            $str = mb_substr($str, $start, $length, __TYPECHO_CHARSET__);
            return ($length < $iLength - $start) ? $str . $trim : $str;
        }
        else
        {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            $str = join("", array_slice($info[0], $start, $length));
            return ($length < (sizeof($info[0]) - $start)) ? $str . $trim : $str;
        }
    }

    /**
     * 获取宽字符串长度函数
     *
     * @access public
     * @param string $str 需要获取长度的字符串
     * @return integer
     */
    public static function strLen($str)
    {
        if(function_exists('mb_get_info'))
        {
            return mb_strlen($str, __TYPECHO_CHARSET__);
        }
        else
        {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            return sizeof($info[0]);
        }
    }

    /**
     * 生成缩略名
     *
     * @access public
     * @param string $str 需要生成缩略名的字符串
     * @param string $default 默认的缩略名
     * @return string
     */
    public static function slugName($str, $default = NULL)
    {
        $str = str_replace(array("'", ":", "\\", "/"), "", $str);
        $str = str_replace(array("+", ",", " ", ".", "?", "=", "&", "!", "<", ">", "(", ")", "[", "]", "{", "}"), "-", $str);

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
            $str = substr($str, 0, - $cutOff);
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
            $str = substr($str, $cutOff);
        }

        return empty($str) ? (empty($default) ? time() : $default) : $str;
    }
    
    /**
     * 文本分段函数
     *
     * @param string $string
     * @return string
     */
    public static function cutParagraph($string)
    {
        /** 锁定自闭合标签 */
        $string = preg_replace_callback("/\<(" . self::LOCKED_HTML_TAG . ")[^\>]*\/\>/is", array('Typecho_API', '__lockHTML'), $string);
        
        /** 锁定开标签 */
        $string = preg_replace_callback("/\<(" . self::LOCKED_HTML_TAG . ")[^\>]*\>.*\<\/\w+\>/is", array('Typecho_API', '__lockHTML'), $string);

        /** 区分段落 */
        $string = preg_replace("/(\r\n|\n|\r)/", "\n", $string);
        $string = '<p>' . preg_replace("/\n{2,}/", "</p><p>", $string) . '</p>';
        $string = str_replace("\n", '<br />', $string);
        
        /** 去掉不需要的 */
        $string = preg_replace("/\<p\>\s*\<h([1-6])\>(.*)\<\/h\\1\>\s*\<\/p\>/is", "\n<h\\1>\\2</h\\1>\n", $string);
        $string = preg_replace("/\<p\>\s*\<(div|blockquote|pre|table|tr|th|td|li|ol|ul)\>(.*)\<\/\\1\>\s*\<\/p\>/is", "\n<\\1>\\2</\\1>\n", $string);
        $string = preg_replace("/\<\/(div|blockquote|pre|table|tr|th|td|li|ol|ul)\>\s*\<br\s?\/?\>\s*\<(div|blockquote|pre|table|tr|th|td|li|ol|ul)\>/is",
        "</\\1>\n<\\2>", $string);

        return str_replace(array_keys(self::$_lockedBlocks), array_values(self::$_lockedBlocks), $string);
    }
    
    /**
     * 生成随机字符串
     * 
     * @access public
     * @param integer $length 字符串长度
     * @return string
     */
    public static function randString($length)
    {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $result = '';
        for($i = 0; $i < $length; $i++)
        {
            $result .= $str[rand(0, 52)];
        }
        return $result;
    }

    /**
     * 动态获取网站根目录
     *
     * @access public
     * @return string
     */
    public static function getSiteRoot()
    {
        return substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')) . '/';
    }
    
    /**
     * 将路径转化为链接
     * 
     * @access public
     * @param string $path 路径
     * @param string $prefix 前缀
     * @return string
     */
    public static function pathToUrl($path, $prefix)
    {
        $path = (0 === strpos($path, './')) ? substr($path, 2) : $path;
        return rtrim($prefix, '/') . '/' . str_replace('//', '/', ltrim($path, '/'));
    }
}
