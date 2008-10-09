<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入api支持 */
require_once 'Typecho/Request.php';

/**
 * 从属于控制器的服务器请求处理类
 *
 * @package Widget
 */
class Typecho_Widget_Request
{
    /**
     * 实例化的对象
     * 
     * @access private
     * @var Typecho_Controller_Request
     */
    private static $_instance;
    
    /**
     * 获取实例化对象
     * 
     * @access public
     * @return Typecho_Controller_Request
     */
    public static function getInstance()
    {
        if(empty(self::$_instance))
        {
            self::$_instance = new Typecho_Widget_Request();
        }
        
        return self::$_instance;
    }
    
    /**
     * 获取参数列表
     * 
     * @access public
     * @return array
     */
    public function from()
    {
        return call_user_func_array(array('Typecho_Request', 'getParametersFrom'), func_get_args());
    }

    /**
     * Request包的直接代理
     * 
     * @access public
     * @param string $method 方法名
     * @param array $args 参数列表
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array('Typecho_Request', $method), $args);
    }
    
    /**
     * 获取指定参数值
     * 
     * @access public
     * @param string $name 参数名
     * @return mixed
     */
    public function __get($name)
    {
        return Typecho_Request::getParameter($name);
    }
    
    /**
     * 设定指定参数
     * 
     * @access public
     * @param string $name 参数名
     * @param mixed $value 参数值
     * @return void
     */
    public function __set($name, $value)
    {
        Typecho_Request::setParameter($name, $value);
    }
}
