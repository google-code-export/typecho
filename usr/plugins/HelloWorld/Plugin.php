<?php
/**
 * Hello World
 * 
 * @desc This is an example.
 * @author qining
 * @config yes
 * @version 1.0.0
 * @link http://www.typecho.org
 */
class HelloWorld_Plugin extends Typecho_Plugin_Abstract
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
        Typecho_Plugin::factory('admin/menu.php')->navBar = array('HelloWorld_Plugin', 'render');
    }
    
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
     * @access public
     * @return unknown
     */
    public static function information()
    {
        return array('title'        => 'Hello World',                                   //插件标题
                     'author'       => 'Typecho Team',                                  //插件作者
                     'homepage'     => 'http://www.typecho.org',                        //插件主页
                     'version'      => '1.0.0',                                         //插件版本
                     'check'        => 'http://www.typecho.org/check.php?{version}',    //插件版本检测
                     'config'       =>  true,                                           //插件配置
                     'description'  => 'This is an example.');                          //插件描述
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
        $name = new Typecho_Widget_Helper_Form_Text('word', 'Hello World', _t('说点什么'));
        $name->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($name);
    }
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        echo '<span style="border:1px solid #999;padding:2px;background:#E37400;color:#222">' . 
        Typecho_Plugin::getOption('HelloWorld', 'word') . '</span>';
    }
}
