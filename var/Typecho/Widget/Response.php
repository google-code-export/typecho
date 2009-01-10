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
     * 是否激活
     * 
     * @access private
     * @var boolean
     */
    private $_enabled = true;

    /**
     * 禁用
     * 
     * @access public
     * @return void
     */
    public function disable()
    {
        $this->_enabled = false;
    }
    
    /**
     * 激活
     * 
     * @access public
     * @return void
     */
    public function enable()
    {
        $this->_enabled = true;
    }

    /**
     * 使用代理模式处理闭包
     * 
     * @access public
     * @param unknown $name
     * @param unknown $args
     * @return unknown
     */
    public function __call($name, $args)
    {
        if ($this->_enabled) {
            return call_user_func_array(array('Typecho_Response', $name), $args);
        }
    }
}
