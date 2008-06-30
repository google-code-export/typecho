<?php
class Plugin_HelloWorld implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        return;
    }
    
    public static function deactivate()
    {
        return;
    }
    
    public static function init()
    {
        /** 注册一个Layout插件 */
        _p(__TYPECHO_ROOT_DIR__ . '/admin/menu.php', 'Layout')->navBar = array('Plugin_HelloWorld', 'test');
    }
    
    public static function information()
    {
        return array('title'        => 'Hello World',
                     'author'       => 'Typecho Team',
                     'homepage'     => 'http://www.typecho.org',
                     'version'      => '1.0.0',
                     'check'        => 'http://www.typecho.org/check.php?{version}',
                     'config'       =>  false,
                     'description'  => 'This is an example.');
    }
    
    public static function test()
    {
        echo '<span style="border:1px solid #999;padding:2px;background:#E37400;color:#222">Hello World</span>';
    }
    
    public static function config()
    {
        return NULL;
    }
}
