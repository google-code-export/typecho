<?php
class HelloWorldPlugin implements TypechoPluginInterface
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
        TypechoPlugin::instance(__TYPECHO_ROOT_DIR__ . '/admin/menu.php')
        ->register(TypechoPlugin::HOOK, 'navBar', array('HelloWorldPlugin', 'test'));
    }
    
    public static function information()
    {
        return array('title'        => 'Hello World',
                     'author'       => 'Typecho Team',
                     'homepage'     => 'http://www.typecho.org',
                     'version'      => '1.0.0',
                     'description'  => 'This is an example.');
    }
    
    public static function test()
    {
        echo '<span style="border:1px solid #999;padding:2px;background:#E37400;color:#222">Hello World</span>';
    }
}
