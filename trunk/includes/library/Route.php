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
            list($format, $file, $values) = $val;
            
            if(preg_match('|^' . $format . '$|', $pathInfo, $matches))
            {
                if(1 < count($matches[0]))
                {
                    unset($matches[0]);
                    $_GET = array_merge($_GET, array_combine($values, $matches));
                    reset($_GET);
                }
                return $path . '/' . $file;
            }
        }
        
        throw new TypechoRouteException(_t('没有找到 %s', $pathInfo), __TYPECHO_EXCEPTION_404__);
    }
    
    /**
     * 路由指向函数,返回根据GET配置的目的文件名
     * 
     * @param string $path 目的文件所在目录
     * @param string $get 获取目的文件的GET值
     * @param string $default 当目的不存在时默认的文件
     * @param array  $deny 禁止访问的handle
     * @return string
     */
    public static function handle($path, $get = 'mod', $default = NULL, array $deny = array())
    {
        if(!empty($_GET[$get]) && preg_match('|^[_a-zA-Z-]+$|', $_GET[$get]) 
        && !in_array($_GET[$get], $deny)
        && file_exists($fileName = $path . '/' . $_GET[$get] . '.php'))
        {
            return $fileName;
        }
        else
        {
            if(!empty($default))
            {
                return $path . '/' . $default . '.php';
            }
            
            throw new TypechoRouteException(_t('禁止访问'), __TYPECHO_EXCEPTION_403__);
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
    public static function parse($name, array $value = NULL, $prefix = NULL)
    {
        global $route;
        
        if($value)
        {
            //交换数组键值
            $pattern = array();
            foreach($route[$name][2] as $row)
            {
                $pattern[$row] = $value[$row];
            }
            
            return $prefix . vsprintf($route[$name][3], $pattern);
        }
        else
        {
            return $prefix . $route[$name][3];
        }
    }
}
