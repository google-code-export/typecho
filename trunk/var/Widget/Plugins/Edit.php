<?php
/**
 * 插件管理
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 插件管理组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Plugins_Edit extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 激活插件
     * 
     * @access public
     * @return void
     */
    public function activate($pluginName)
    {
        /** 获取插件入口 */
        list($pluginFileName, $className) = Typecho_Plugin::portal($pluginName, __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__);
        
        /** 获取已激活插件 */
        $plugins = Typecho_Plugin::export();
        $activatedPlugins = $plugins['activated'];
        
        /** 判断实例化是否成功 */
        $info = Typecho_Plugin::parseInfo($pluginFileName);
        if (!$info['activate'] || isset($activatedPlugins[$pluginName])) {
            throw new Typecho_Widget_Exception(_t('无法激活插件'), 500);
        }
        
        /** 实例化插件 */
        require_once $pluginFileName;
        $plugin = $this->widget($className);
        
        try {
            $plugin->activate();
            Typecho_Plugin::activate($pluginName);
            $this->update(array('value' => serialize(Typecho_Plugin::export())),
            $this->db->sql()->where('name = ?', 'plugins'));
        } catch (Typecho_Plugin_Exception $e) {
            /** 截获异常 */
            $this->widget('Widget_Notice')->set($e->getMessage(), NULL, 'error');
            $this->response->goBack();
        }
        
        $form = new Typecho_Widget_Helper_Form();
        $plugin->config($form);
        $options = $form->getValues();
        if ($options) {
            $this->insert(array(
                'name'  =>  'plugin:' . $pluginName,
                'value' =>  serialize($options),
                'user'  =>  0
            ));
        }
        
        $this->widget('Widget_Notice')->set(_t('插件已经被激活'), NULL, 'success');
        $this->response->goBack();
    }
    
    /**
     * 禁用插件
     * 
     * @access public
     * @return void
     */
    public function deactivate($pluginName)
    {
        /** 获取插件入口 */
        list($pluginFileName, $className) = Typecho_Plugin::portal($pluginName, __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__);
        
        /** 获取已激活插件 */
        $plugins = Typecho_Plugin::export();
        $activatedPlugins = $plugins['activated'];
        
        /** 判断实例化是否成功 */
        $info = Typecho_Plugin::parseInfo($pluginFileName);
        if (!$info['deactivate'] || !isset($activatedPlugins[$pluginName])) {
            throw new Typecho_Widget_Exception(_t('无法禁用插件'), 500);
        }
        
        /** 实例化插件 */
        require_once $pluginFileName;
        $plugin = $this->widget($className);
        
        try {
            $plugin->deactivate();
            Typecho_Plugin::deactivate($pluginName);
            $this->update(array('value' => serialize(Typecho_Plugin::export())),
            $this->db->sql()->where('name = ?', 'plugins'));
        } catch (Typecho_Plugin_Exception $e) {
            /** 截获异常 */
            $this->widget('Widget_Notice')->set($e->getMessage(), NULL, 'error');
            $this->response->goBack();
        }
        
        $this->delete($this->db->sql()->where('name = ?', 'plugin:' . $pluginName));
        
        $this->widget('Widget_Notice')->set(_t('插件已经被禁用'), NULL, 'success');
        $this->response->goBack();
    }
    
    /**
     * 配置插件
     * 
     * @access public
     * @return void
     */
    public function config($pluginName)
    {
        $form = $this->widget('Widget_Plugins_Config')->config();
        
        /** 验证表单 */
        if ($form->validate()) {
            $this->response->goBack();
        }
        
        $settings = $form->getAllRequest();
        $this->update(array('value' => serialize($settings)),
        $this->db->sql()->where('name = ?', 'plugin:' . $pluginName));
        
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t("插件设置已经保存"), NULL, 'success');
        
        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('plugins.php', $this->options->adminUrl));
    }

    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->onRequest('activate')->activate($this->request->activate);
        $this->onRequest('deactivate')->deactivate($this->request->deactivate);
        $this->onRequest('config')->config($this->request->config);
        $this->response->redirect($this->options->adminUrl);
    }
}
