<?php
/**
 * 插件接口
 * 
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 插件接口
 * 
 * @package Plugin
 * @abstract
 */
interface Typecho_Plugin_Interface
{    
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public function activate();
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public function deactivate();
    
    /**
     * 获取插件配置面板
     * 
     * @static
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public function config(Typecho_Widget_Helper_Form $form);
}
