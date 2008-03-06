<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */
 
/** 载入路由异常支持 **/
require_once 'Route/RouteException.php';

/**
 * Typecho组件基类
 * 
 * @package Route
 */
class TypechoRoute
{
    private static $_get = array();
    private static $_value = array();

    /**
     * 路由指向函数,返回根据pathinfo和路由表配置的目的文件名
     * 
     * @param string $path 目的文件所在目录
     * @return string
     * @throws TypechoRouteException
     */
    public static function target($path)
    {
        global $route;
        
        $pathInfo = typechoGetPathInfo();
        foreach($route as $key => $val)
        {
            list($format, $file) = $val;
            $format = preg_replace_callback('/\[([_a-zA-Z0-9-]+)\]/is', array('TypechoRoute', 'callback'), $format);
            $values = sscanf($pathInfo, $format);
            
            if(NULL !== $values && NULL !== implode(NULL, $values) 
            && file_exists($fileName = $path . '/' . $file))
            {
                foreach($values as $inkey => $inval)
                {
                    $_GET[self::$_get[$inkey]] = $inval;
                }
                
                return $fileName;
            }
        }
        
        throw new TypechoRouteException(_t('没有找到'), __TYPECHO_EXCEPTION_404__);
    }
    
    private static function callback($matches)
    {
        self::$_get[] = $matches[1];
        return '%s';
    }
    
    /**
     * 路由指向函数,返回根据GET配置的目的文件名
     * 
     * @param string $path 目的文件所在目录
     * @param string $get 获取目的文件的GET值
     * @param string $default 当目的不存在时默认的文件
     * @return string
     */
    public static function handle($path, $get = 'mod', $default = 'index')
    {
        if(!empty($_GET[$get]) && preg_match('|^[_a-zA-Z-]+$|', $_GET[$get]) 
        && file_exists($fileName = $path . '/' . $_GET[$get] . '.php'))
        {
            return $fileName;
        }
        else
        {
            return $path . '/' . $default . '.php';
        }
    }
    
    /**
     * 路由反解析函数
     * 
     * @param string $name 路由配置表名称
     * @param string $value 路由填充值
     * @param string $prefix 最终合成路径的前缀
     * @return string
     */
    public static function parse($name, $value, $prefix = NULL)
    {
        global $route;
        
        self::$_value = $value;
        return $prefix . preg_replace_callback('/\[([_a-zA-Z0-9-]+)\]/is', array('TypechoRoute', 'callback'), $route[$name][0]);
    }
    
    private static function parseCallback($matches)
    {
        return self::$_value[$matches[1]];
    }
}
