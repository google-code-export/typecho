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
class HelloWorld_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public function activate()
    {
        Typecho_Plugin::factory('admin/menu.php')->navBar = array('HelloWorld_Plugin', 'render');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public function deactivate(){}
    
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
    public function information()
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
    public function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
        $name = new Typecho_Widget_Helper_Form_Element_Text('word', NULL, 'Hello World', _t('说点什么'));
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
        echo Typecho_Widget::widget('Widget_Options')->plugin(self)->word;
    }
}
