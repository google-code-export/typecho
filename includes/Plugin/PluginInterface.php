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
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */
interface TypechoPluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws TypechoPluginException
     */
    public static function activate();
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws TypechoPluginException
     */
    public static function deactivate();
    
    /**
     * 插件初始化方法
     * 
     * @access public
     * @return void
     */
    public static function init();
    
    /**
     * 获取插件信息方法
     * <code>
     * return array(
     * 'name'           =>  'Hello World',
     * 'author'         =>  'Typecho Team',
     * 'homepage'       =>  'http://www.typecho.org',
     * 'version'        =>  '1.0.0',
     * 'description'    =>  'This is an example.'
     * );
     * </code>
     * 
     * @access public
     * @return unknown
     */
    public static function information();
}
