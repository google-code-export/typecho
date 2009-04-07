<?php
/**
 * 小巧的mp3播放器,在编辑器代码模式下使用<strong>&lt;mp3&gt;http://...&lt;/mp3&gt;</strong>的格式来添加一个音乐播放器
 * 
 * @package Dewplayer
 * @author qining
 * @version 1.0.0
 * @link http://typecho.org
 */
class FlashMp3Player_Plugin implements Typecho_Plugin_Interface
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
        /** 以下三个为编辑器接口 */
        Typecho_Plugin::factory('Widget_Ajax')->toVisualEditor = array('FlashMp3Player_Plugin', 'toVisualEditor');
        Typecho_Plugin::factory('Widget_Ajax')->toCodeEditor = array('FlashMp3Player_Plugin', 'toCodeEditor');
        
        //离线浏览器都是所见即所得模式
        Typecho_Plugin::factory('Widget_XmlRpc')->fromOfflineEditor = array('FlashMp3Player_Plugin', 'toCodeEditor');
        
        /** 前端输出处理接口 */
        Typecho_Plugin::factory('Widget_Abstract_Contents')->filter = array('FlashMp3Player_Plugin', 'parse');
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
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 将伪可视化代码转化为可视化代码
     * 
     * @access public
     * @param string $content 需要处理的内容
     * @return string
     */
    public static function toVisualEditor($content)
    {
        $swfUrl = Typecho_Common::url('FlashMp3Player/swf/dewplayer.swf', Helper::options()->pluginUrl);
        return preg_replace("/<(mp3)>(.*?)<\/\\1>/is", 
        "<object class=\"typecho-plugin\" type=\"application/x-shockwave-flash\" data=\"{$swfUrl}?mp3=\\2\" width=\"200\" height=\"20\">
<param name=\"movie\" value=\"{$swfUrl}?mp3=\\2\" />
</object>",
        $content);
    }
    
    /**
     * 将可视化代码转化为伪可视化代码
     * 
     * @access public
     * @param string $content 需要处理的内容
     * @return string
     */
    public static function toCodeEditor($content)
    {
        $swfUrl = preg_quote(Typecho_Common::url('FlashMp3Player/swf/dewplayer.swf', Helper::options()->pluginUrl), "/");
        return preg_replace("/<object.*data=\"{$swfUrl}\?mp3\=([^\"]+)\".*>(.*?)<\/object>/is", "<mp3>\\1</mp3>", $content);
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
            $swfUrl = Typecho_Common::url('FlashMp3Player/swf/dewplayer.swf', Helper::options()->pluginUrl);
            $value['text'] = preg_replace("/<(mp3)>(.*?)<\/\\1>/is", 
            "<object class=\"typecho-plugin\" type=\"application/x-shockwave-flash\" data=\"{$swfUrl}?mp3=\\2\" width=\"200\" height=\"20\">
<param name=\"movie\" value=\"{$swfUrl}?mp3=\\2\" />
</object>",
            $value['text']);
        }
        
        return $value;
    }
}
