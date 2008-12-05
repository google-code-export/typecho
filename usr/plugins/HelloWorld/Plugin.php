<?php
/**
 * Hello World
 * 
 * @package HelloWorld 
 * @author qining
 * @version 1.0.0
 * @link http://typecho.org
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
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
		$nowHelloWorld = Typecho_Widget::widget('Widget_Options')->plugin('HelloWorld')->word;
        $name = new Typecho_Widget_Helper_Form_Element_Text('word', NULL, $nowHelloWorld, _t('说点什么'));
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
        echo '<span class="message success">' . Typecho_Widget::widget('Widget_Options')->plugin('HelloWorld')->word . '</span>';
    }
}
