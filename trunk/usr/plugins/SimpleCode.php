<?php
/**
 * 解析内容源代码中的code串
 * 
 * @package Simple Code 
 * @author qining
 * @version 1.0.0
 * @link http://typecho.org
 */
class SimpleCode implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->filter = array('SimpleCode', 'parse');
    }
    
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
     * 解析
     * 
     * @access public
     * @param unknown $matches
     * @return unknown
     */
    public static function parseCallback($matches)
    {
        return highlight_string(trim($matches[2]), true);
    }
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function parse($value, $widget, $lastResult)
    {
        $value = empty($lastResult) ? $value : $lastResult;
        if ($widget instanceof Widget_Archive) {
            $value['text'] = preg_replace_callback("/<code(\s*.*)>(.*)<\/code>/is", array('SimpleCode', 'parseCallback'), $value['text']);
        }
        
        return $value;
    }
}
