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
abstract class Typecho_Plugin_Abstract
{
    /**
     * 获取插件信息方法
     * <code>
     * return array(
     * 'title'          =>  'Hello World',
     * 'author'         =>  'Typecho Team',
     * 'homepage'       =>  'http://www.typecho.org',
     * 'check'          =>  'http://www.typecho.org/check.php?{version}',
     * 'version'        =>  '1.0.0',
     * 'config'         =>  true,
     * 'description'    =>  'This is an example.'
     * );
     * </code>
     * 
     * @static
     * @access public
     * @return unknown
     */
    public static function information(){}
    
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate(){}
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @static
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){}
}
