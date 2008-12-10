<?php
/**
 * R&uuml;stet Syntax-Highlighting f&uuml;r den Theme- und Plugin-Editor nach. Unterst&uuml;tzt php, css, html und js. F&uuml;r weitere Infos besuch die <a href="http://www.naden.de/blog/wordpress-code-editor" target="_blank">Plugin Homepage</a>.
 * 
 * @package Typecho Code Editor 
 * @author Naden Badalgogtapeh
 * @version 1.1 / 30.05.2007
 * @link http://www.naden.de/blog
 */
class CodeEditor_Plugin implements Typecho_Plugin_Interface
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
        if(false !== strpos(Typecho_Request::getAgent(), 'KHTML')) {
            throw new Typecho_Plugin_Exception(_t('对不起, 您使用的Webkit核心浏览器无法正常使用此插件'));
        }
        Typecho_Plugin::factory('admin/theme-editor.php')->form = array('CodeEditor_Plugin', 'render');
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
    public function config(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        $options = Typecho_Widget::widget('Widget_Options');
        print( "\n<!-- Code Editor Plugin v1.1 http://www.naden.de/blog/wordpress-code-editor -->\n" );
        /// die Variable brauchen wir, falls Wordpress in einem Unterverzeichnis installiert ist
        printf( '<script type="text/javascript">var ce_url = \'%s\';</script>', Typecho_Common::url('CodeEditor', $options->pluginUrl) );
        printf( '<script type="text/javascript" src="%s"></script>',
        Typecho_Common::url('CodeEditor/code-editor.js', $options->pluginUrl));	 
        print( "\n<!-- // Code Editor Plugin -->\n" );
    }
}
