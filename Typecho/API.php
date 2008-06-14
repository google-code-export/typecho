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
class Typecho_API
{
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
        $_GET = self::stripslashesDeep($_GET);
        $_POST = self::stripslashesDeep($_POST);
        $_COOKIE = self::stripslashesDeep($_COOKIE);

        reset($_GET);
        reset($_POST);
        reset($_COOKIE);
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
        if($gzipAble && empty($_SERVER['HTTP_ACCEPT_ENCODING'])
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
     * 如果时区不存在,设置一个默认时区
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
     * 在http头部请求中声明类型和字符集
     * 
     * @access public
     * @param string $contentType 文档类型
     * @param string $charset 字符集
     * @return void
     */
    public static function setContentType($contentType = 'text/html', $charset = 'UTF-8')
    {
        header('content-Type: ' . $contentType . ';charset= ' . $charset);
    }
    
    /**
     * 工厂方法,将类静态化放置到列表中
     * 
     * @access public
     * @param string $className
     * @return object
     */
    public static function factory($className)
    {
        static $classStack;
        
        if(!isset($classStack[$className]))
        {
            require_once dirname(__FILE__) . '/../' . str_replace('_', '/', $className) . '.php';
            $classStack[$className] = new $className;
        }
        
        return $classStack[$className];
    }
    
    /**
     * 控制头部输出和内容
     * 
     * @access public
     * @param string $tag html标签
     * @param string $attribute 属性名称以及其值
     * @param string $close 是否自闭合
     * @return void
     */
    public static function header($tag = NULL, array $attribute = NULL, $close = true)
    {
        static $headers;
        
        if(empty($tag))
        {
            foreach($headers as $val)
            {
                foreach($val as $inval)
                {
                    echo $inval . "\n";
                }
            }
        }
        else
        {
            $string = "<{$tag}";
            if($attribute)
            {
                foreach($attribute as $key => $val)
                {
                    $string .= " {$key}=\"{$val}\"";
                }
            }
            $string .= $close ? " />" : "></{$tag}>";
            $headers[$tag][] = $string;
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
        return is_array($value) ? array_map(array('Typecho', 'stripslashesDeep'), $value) : stripslashes($value);
    }

    /**
     * 抽取多维数组的某个元素,组成一个新数组,使这个数组变成一个扁平数组
     * 使用方法:
     * <code>
     * <?php
     * $fruit = array(array('apple' => 2, 'banana' => 3), array('apple' => 10, 'banana' => 12));
     * $banana = Typecho::arrayFlatten($fruit, 'banana');
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
                if($attrLength > 0 && "/" == $startTags[2][$key][$attrLength - 1])
                {
                    continue;
                }
                if(!empty($closeTags[1]) && $closeTagsIsArray)
                {
                    if(in_array($tag, $closeTags[1]))
                    {
                        continue;
                    }
                }
                $string .= "</{$tag}>";
            }
        }

        return $string;
    }

    /**
     * 去掉字符串中的html标签
     * 使用方法:
     * <code>
     * $input = '<a href="http://test/test.php" title="example">hello</a>';
     * $output = Typecho::stripTags($input, <a href="">);
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
        return str_replace(array('%', '?', '*'), '', $query);
    }
    
    /**
     * 判断一个变量是否可以作为字符串
     * 
     * @access public
     * @param string $value 被判断的变量
     * @return boolean
     */
    public static function stringAble($value)
    {
        return !is_array($value) && !is_object($value);
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
    public static function slugName($str, $default)
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

        return NULL == $str ? $default : $str;
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
        $prefixLength = strLen($prefix) - 1;
        $prefix = ($prefixLength == strrpos($prefix, '/')) ? $prefix : $prefix . '/';
        $path = (0 === strpos($path, './')) ? substr($path, 2) : $path;
        $path = (0 === strpos($path, '/')) ? substr($path, 1) : $path;
        return $prefix . str_replace('//', '/', $path);
    }
}
