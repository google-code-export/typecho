<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Route.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 异常基类 */
require_once 'Exception.php';

/** 配置管理 */
require_once 'Config.php';

/** 载入路由异常支持 */
require_once 'Route/RouteException.php';

/** 载入api支持 */
require_once 'Typecho.php';

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
     * 解析路径
     * 
     * @access public
     * @param mixed $route 路由表
     * @param string $pathInfo 全路径
     * @param string $current 当前键值
     * @param array $matches 匹配值
     * @return array
     */
    public static function match($route, $pathInfo, &$current, &$matches)
    {
        foreach($route as $key => $val)
        {
            if(preg_match('|^' . $val[0] . '$|', $pathInfo, $matches))
            {
                $current = $key;
                return $val;
            }
        }
        
        return false;
    }

    /**
     * 路由指向函数,返回根据pathinfo和路由表配置的目的文件名
     *
     * @param string $path 目的文件所在目录
     * @return string
     * @throws TypechoRouteException
     */
    public static function target($path)
    {
        /** 判断是否定义配置 */
        TypechoConfig::need('Route');
        
        /** 获取路由配置 */
        $route = TypechoConfig::get('Route');
        
        /** 获取PATHINFO */
        $pathInfo = Typecho::getPathInfo();

        /** 遍历路由 */
        if(false !== ($val = self::match($route, $pathInfo, $key, $matches)))
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
                throw new TypechoRouteException(_t('目录错误 %s', $pathInfo), TypechoException::NOTFOUND);
            }

            if(!empty($address))
            {
                Typecho::redirect($address, true);
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
                    Typecho::widget($widget);
                }
            }

            if(!empty($file))
            {
                require $path . '/' . $file;
            }

            return;
        }

        throw new TypechoRouteException(_t('没有找到 %s', $pathInfo), TypechoException::NOTFOUND);
    }

    /**
     * 获取路径解析值
     *
     * @access public
     * @param string $key
     * @return mixed
     */
    public static function getParameter($key)
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
        $route = TypechoConfig::get('Route')->$name;

        if($value)
        {
            //交换数组键值
            $pattern = array();
            foreach($route[2] as $row)
            {
                $pattern[$row] = isset($value[$row]) ? $value[$row] : '{' . $row . '}';
            }

            return Typecho::pathToUrl(vsprintf($route[3], $pattern), $prefix);
        }
        else
        {
            return Typecho::pathToUrl($route[3], $prefix);
        }
    }
}
