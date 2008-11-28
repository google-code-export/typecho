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
class Widget_Plugins_Config extends Widget_Abstract_Options
{
    /**
     * 插件文件路径
     * 
     * @access private
     * @var string
     */
    private $_pluginFileName;
    
    /**
     * 插件类
     * 
     * @access private
     * @var string
     */
    private $_className;

    /**
     * 获取插件信息
     * 
     * @access public
     * @var array
     */
    public $info;

    /**
     * 绑定动作
     * 
     * @access public
     * @return unknown
     */
    public function init()
    {
        $this->user->pass('administrator');
        if (!isset($this->request->config)) {
            $this->throwExceptionResponseByCode(_t('插件不存在'), 404);
        }
        
        /** 获取插件入口 */
        list($this->_pluginFileName, $this->_className) = Typecho_Plugin::portal($this->request->config,
        __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__);
        $this->info = Typecho_Plugin::parseInfo($this->_pluginFileName);
    }
    
    /**
     * 获取菜单标题
     * 
     * @access public
     * @return string
     */
    public function getMenuTitle()
    {
        return _t('设置插件 "%s"', $this->info['title']);
    }

    /**
     * 配置插件
     * 
     * @access public
     * @return void
     */
    public function config()
    {
        /** 获取插件名称 */
        $pluginName = $this->request->config;
        
        /** 获取已激活插件 */
        $plugins = Typecho_Plugin::export();
        $activatedPlugins = $plugins['activated'];
        
        /** 判断实例化是否成功 */
        
        if (!$this->info['config'] || !isset($activatedPlugins[$pluginName])) {
            $this->throwExceptionResponseByCode(_t('无法配置插件'), 500);
        }
        
        /** 实例化插件 */
        require_once $this->_pluginFileName;
        $plugin = $this->widget($this->_className);
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('Plugins/Edit.do?config=' . $pluginName,
        $this->options->index), Typecho_Widget_Helper_Form::POST_METHOD);
        $plugin->config($form);
        
        $options = $this->options->plugin($pluginName);
        
        if (!empty($options) && is_array($options)) {
            foreach ($options as $key => $val) {
                $form->getInput($key)->value($val);
            }
        }
        
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Submit(NULL, NULL, _t('保存设置')));
        return $form;
    }
}
