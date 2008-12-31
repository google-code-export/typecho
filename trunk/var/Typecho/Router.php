<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Route.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 载入api支持 */
require_once 'Typecho/Common.php';

/**
 * Typecho组件基类
 *
 * TODO 增加cache缓存
 * @package Router
 */
class Typecho_Router
{
    /**
     * 当前路由名称
     *
     * @access public
     * @var string
     */
    public static $current;

    /**
     * 已经解析完毕的路由表配置
     * 
     * @access private
     * @var mixed
     */
    private static $_routingTable = array();
    
    /**
     * 解析路径
     * 
     * @access public
     * @param string $pathInfo 全路径
     * @return mixed
     */
    public static function match($pathInfo)
    {
        foreach (self::$_routingTable as $key => $route) {
            if (preg_match($route['regx'], $pathInfo, $matches)) {
                self::$current = $key;
                
                if (!empty($route['params'])) {
                    /** Typecho_Ruquest */
                    require_once 'Typecho/Request.php';
                    
                    unset($matches[0]);
                    $params = array_combine($route['params'], $matches);
                    
                    foreach ($params as $key => $val) {
                        Typecho_Request::setParameter($key, $val);
                    }
                }
                
                return $route;
            }
        }
        
        return false;
    }

    /**
     * 路由分发函数
     *
     * @param string $path 目的文件所在目录
     * @return void
     * @throws Typecho_Route_Exception
     */
    public static function dispatch()
    {
        /** 载入request支持 */
        require_once 'Typecho/Request.php';
        
        /** 获取PATHINFO */
        $pathInfo = Typecho_Request::getPathInfo();

        /** 遍历路由 */
        if (false !== ($route = self::match($pathInfo))) {
            /** Typecho_Widget */
            require_once 'Typecho/Widget.php';
            
            $widget = Typecho_Widget::widget($route['widget']);
            if (isset($route['action'])) {
                $widget->{$route['action']}();
            }
            return;
        }

        /** 载入路由异常支持 */
        require_once 'Typecho/Router/Exception.php';
        throw new Typecho_Router_Exception("Path '{$pathInfo}' not found", 404);
    }

    /**
     * 路由反解析函数
     *
     * @param string $name 路由配置表名称
     * @param string $value 路由填充值
     * @param string $prefix 最终合成路径的前缀
     * @return string
     */
    public static function url($name, array $value = NULL, $prefix = NULL)
    {
        $route = self::$_routingTable[$name];
       
        //交换数组键值
        $pattern = array();
        foreach ($route['params'] as $row) {
            $pattern[$row] = isset($value[$row]) ? $value[$row] : '{' . $row . '}';
        }

        return Typecho_Common::url(vsprintf($route['format'], $pattern), $prefix);
    }
    
    /**
     * 设置数据库默认配置
     * 
     * @access public
     * @param mixed $routes 配置信息
     * @return void
     */
    public static function setRoutes($routes)
    {
        /** 载入路由解析支持 */
        require_once 'Typecho/Router/Parser.php';

        /** 解析路由配置 */
        $parser = new Typecho_Router_Parser($routes);
        self::$_routingTable = $parser->parse($routes);
    }

    /**
     * 获取路由信息 
     * 
     * @param string $routeName 路由名称 
     * @static
     * @access public
     * @return void
     */
    public static function get($routeName)
    {
        return isset(self::$_routingTable[$routeName]) ? self::$_routingTable[$routeName] : NULL;
    }
}
