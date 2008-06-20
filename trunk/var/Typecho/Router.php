<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Route.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 配置管理 */
require_once 'Typecho/Config.php';

/** 载入api支持 */
require_once 'Typecho/API.php';

/** 载入request支持 */
require_once 'Typecho/Request.php';

/** 载入request支持 */
require_once 'Typecho/Widget.php';

/** 载入路由解析支持 */
require_once 'Typecho/Router/Parser.php';

/** 载入路由异常支持 */
require_once 'Typecho/Router/Exception.php';

/**
 * Typecho组件基类
 *
 * @package Route
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
     * 路径解析值列表
     *
     * @access private
     * @var array
     */
    private static $_parameters = array();
    
    /**
     * 已经解析完毕的路由配置
     * 
     * @access private
     * @var mixed
     */
    private static $_routes = false;
    
    /**
     * 解析路由
     * 
     * @access private
     * @return void
     */
    private static function _parseRoute()
    {
        if(false === self::$_routes)
        {
            /** 判断是否定义配置 */
            Typecho_Config::need('Router');
            
            /** 获取路由配置 */
            $config = Typecho_Config::get('Router');
            
            /** 解析路由配置 */
            $parser = new Typecho_Router_Parser($config);
            self::$_routes = $parser->parse($config);
        }
    }

    /**
     * 解析路径
     * 
     * @access public
     * @param string $pathInfo 全路径
     * @return mixed
     */
    public static function match($pathInfo)
    {
        self::_parseRoute();
    
        foreach(self::$_routes as $key => $route)
        {
            if(preg_match($route['regx'], $pathInfo, $matches))
            {
                self::$current = $key;
                
                if(!empty($route['params']))
                {
                    unset($matches[0]);
                    $_REQUEST = array_merge($_REQUEST, array_combine($route['params'], $matches));
                    reset($_REQUEST);
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
        /** 获取PATHINFO */
        $pathInfo = Typecho_Request::getPathInfo();

        /** 遍历路由 */
        if(false !== ($route = self::match($pathInfo)))
        {
            Typecho_API::factory($route['widget'])->{$route['action']}();
            return;
        }

        throw new Typecho_Router_Exception("Path '{$pathInfo}' not found", Typecho_Exception::NOTFOUND);
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
        self::_parseRoute();
        $route = self::$_routes[$name];
    
        if($value)
        {            
            //交换数组键值
            $pattern = array();
            foreach($route['params'] as $row)
            {
                $pattern[$row] = isset($value[$row]) ? $value[$row] : '{' . $row . '}';
            }

            return Typecho_API::pathToUrl(vsprintf($route['format'], $pattern), $prefix);
        }
        else
        {
            return Typecho_API::pathToUrl($route['url'], $prefix);
        }
    }
}
