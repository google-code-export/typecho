<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入api支持 */
require_once 'Typecho/Response.php';

/**
 * 从属于控制器的服务器回执处理类
 *
 * @package Widget
 */
class Typecho_Widget_Response
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
        if (empty(self::$_instance)) {
            self::$_instance = new Typecho_Widget_Response();
        }
        
        return self::$_instance;
    }
    
    /**
     * Response包的直接代理
     * 
     * @access public
     * @param string $method 方法名
     * @param array $args 参数列表
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array('Typecho_Response', $method), $args);
    }
}
