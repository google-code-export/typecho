<?php
/**
 * 插件帮手本身也是一个插件, 它将默认出现在所有的typecho发行版中.
 * 因此你可以放心使用它的功能, 以方便你的插件安装在用户的系统里.
 * 
 * @package Plugin Helper 
 * @author qining
 * @version 1.0.0
 * @link http://typecho.org
 */
class Helper_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
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
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 增加路由
     * 
     * @access public
     * @return boolean
     */
    public static function addRoute()
    {
        
    }
    
    /**
     * 移除路由
     * 
     * @access public
     * @return boolean
     */
    public static function removeRoute()
    {
        
    }
    
    public static function addMenu()
    {
        
    }
    
    public static function removeMenu()
    {
        
    }
}
