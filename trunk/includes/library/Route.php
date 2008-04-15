<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Route.php 107 2008-04-11 07:14:43Z magike.net $
 */
 
/** 载入路由异常支持 */
require_once 'Route/RouteException.php';

/**
 * Typecho组件基类
 * 
 * @package Route
 */
class TypechoRoute
{
    /**
     * 当前路由名称
     * 
     * @access public
     * @var string
     */
    public static $current;
    
    /**
     * 路径解析值列表
     * 
     * @access private
     * @var array
     */
    private static $_parameters = array();

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
            if(preg_match('|^' . $val[0] . '$|', $pathInfo, $matches))
            {
                self::$current = $key;
                $count = count($val);
                
                if(5 == $count)
                {
                    list($pattern, $file, $values, $format, $widgets) = $val;
                }
                else if(4 == $count)
                {
                    list($pattern, $widgets, $values, $format) = $val;
                }
                else if(2 == $count)
                {
                    list($pattern, $address) = $val;
                }
                else
                {
                    throw new TypechoRouteException(_t('目录错误 %s', $pathInfo), 404);
                }
                
                if(!empty($address))
                {
                    typechoRedirect($address);
                }
                
                if(1 < count($matches) && !empty($values))
                {
                    unset($matches[0]);
                    self::$_parameters = array_combine($values, $matches);
                }
                
                if(!empty($widgets))
                {
                    foreach($widgets as $widget)
                    {
                        widget($widget);
                    }
                }
                
                if(!empty($file))
                {
                    require $path . '/' . $file;
                }
                
                exit;
            }
        }
        
        throw new TypechoRouteException(_t('没有找到 %s', $pathInfo), 404);
    }
    
    /**
     * 获取路径解析值
     * 
     * @access public
     * @param string $key
     * @return mixed
     */
    static public function getParameter($key)
    {
        return empty(self::$_parameters[$key]) ? NULL : self::$_parameters[$key];
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
    
    /**
     * 动态设置路由
     * 
     * @access public
     * @param string $key 路由名称
     * @param array $value 路由解析值
     * @return void
     */
    public static function set($key, array $value)
    {
        global $route;
        $route[$key] = $value;
    }
    
    /**
     * 获取路由值
     * 
     * @access public
     * @param string $key 路由名称
     * @return string
     */
    public static function get($key)
    {
        global $route;
        return $route[$key];
    }
}
