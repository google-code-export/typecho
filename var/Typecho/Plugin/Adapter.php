<?php
/**
 * 插件适配器
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 插件适配器抽象类
 * 
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
abstract class Typecho_Plugin_Adapter
{
    /**
     * 回调函数列表
     * 
     * @access protected
     * @var array
     */
    protected $callback;
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->callback = array();
    }

    /**
     * 回调处理函数
     * 
     * @access public
     * @param string $component 元件名称
     * @param string $args 参数
     * @return mixed
     */
    abstract public function __call($component, $args);
    
    /**
     * 注册一个回调函数
     * 
     * @access public
     * @param string $component 元件名称
     * @param string $callback 回调函数
     * @return void
     */
    public function __set($component, $callback)
    {
        $this->callback[$component][] = $callback;
    }
}
