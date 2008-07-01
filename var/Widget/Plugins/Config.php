<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 插件配置组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Plugins_Config extends Typecho_Widget
{
    /**
     * 配置面板表单
     * 
     * @access public
     * @var Typecho_Widget_Helper_Form
     */
    public $form;

    /**
     * 绑定动作
     * 
     * @access public
     * @return unknown
     */
    public function __construct()
    {
        Typecho_Request::bindParameter(array('do' => 'config'), array($this, 'configPlugin'));
    }

    /**
     * 配置插件
     * 
     * @access public
     * @return void
     */
    public function configPlugin()
    {
        $pluginName = Typecho_Request::getParameter('plugin');
        $pluginFileName = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '/' . $pluginName . '.php';
        
        /** 获取已激活插件 */
        $activatedPlugins = Typecho_API::factory('Widget_Abstract_Options')->plugins;
        
        if(file_exists($pluginFileName))
        {
            require_once $pluginFileName;
            
            /** 获取插件信息 */
            if(is_callable(array('Plugin_' . $pluginName, 'config')) && 
            in_array($pluginName, $activatedPlugins))
            {
                try
                {
                    $options = Typecho_API::factory('Widget_Abstract_Options');
                    
                    /** 初始化表单 */
                    $this->form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Plugins/Edit.do?do=config&plugin=' .
                    $pluginName, $options->index),
                    Typecho_Widget_Helper_Form::POST_METHOD);
                    
                    /** 增加一个标题 */
                    $information = call_user_func(array('Plugin_' . $pluginName, 'information'));
                    $title = new Typecho_Widget_Helper_Layout('h4');
                    $this->form->addItem($title->html(_t('配置%s', $information['title']))
                    ->setAttribute('id', 'edit'));
                    
                    /** 配置插件面板 */
                    call_user_func(array('Plugin_' . $pluginName, 'config'), $this->form);
                    
                    /** 对面板赋值 */
                    $inputs = $this->form->getInputs();
                    foreach($inputs as $name => $input)
                    {
                        $input->value($options->plugin($pluginName)->{$name});
                    }
                    
                    /** 空格 */
                    $this->form->addItem(new Typecho_Widget_Helper_Layout('hr', array('class' => 'space')));
                    
                    /** 提交按钮 */
                    $submit = new Typecho_Widget_Helper_Form_Submit();
                    $submit->button->setAttribute('class', 'submit');
                    $this->form->addItem($submit->value(_t('保存配置'))->setAttribute('class', 'table_nav'));
                }
                catch(Typecho_Plugin_Exception $e)
                {
                    /** 截获异常 */
                    Typecho_API::factory('Widget_Notice')->set($e->getMessage(), NULL, 'error');
                    Typecho_API::goBack();
                }
            }
        }
    }
}
