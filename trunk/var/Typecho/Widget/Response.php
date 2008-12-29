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
