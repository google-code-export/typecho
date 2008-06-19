<?php

/**
 * 插件操作抽象组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
abstract class Typecho_Widget_Abstract_Plugin extends Typecho_Widget
{
    /**
     * 激活插件
     * 
     * @access public
     * @param string $name 插件名称
     * @return void
     */
    abstract public function activate($name);
    
    /**
     * 禁用插件
     * 
     * @access public
     * @param string $name 插件名称
     * @return void
     */
    abstract public function deactivate($name);
    
    /**
     * 获得已经激活的插件
     * 
     * @access public
     * @return void
     */
    abstract public function getActivated();
    
    /**
     * 列出所有插件
     * 
     * @access public
     * @return void
     */
    abstract public function listAll();
}
