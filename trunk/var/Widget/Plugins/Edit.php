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
class Widget_Plugins_Edit extends Typecho_Widget implements Widget_Interface_DoWidget
{
    /**
     * 激活插件
     * 
     * @access public
     * @return void
     */
    public function activatePlugin()
    {
        $pluginName = Typecho_Request::getParameter('plugin');
        $pluginFileName = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '/Plugin.php';
        
        /** 获取已激活插件 */
        $activatedPlugins = Typecho_API::factory('Widget_Abstract_Options')->plugins;
        
        if(file_exists($pluginFileName))
        {
            require_once $pluginFileName;
            
            /** 获取插件信息 */
            if(is_callable(array($pluginName . '_Plugin', 'activate')) && 
            !in_array($pluginName, $activatedPlugins))
            {
                try
                {
                    call_user_func(array($pluginName . '_Plugin', 'activate'));
                }
                catch(Typecho_Plugin_Exception $e)
                {
                    /** 截获异常 */
                    Typecho_API::factory('Widget_Notice')->set($e->getMessage(), NULL, 'error');
                    Typecho_API::goBack();
                }
                
                $activatedPlugins[] = $pluginName;
                Typecho_API::factory('Widget_Abstract_Options')->update(array('value' => serialize($activatedPlugins)),
                Typecho_Db::get()->sql()->where('`name` = ?', 'plugins'));
                
                /** 获取插件信息 */
                if(is_callable(array($pluginName . '_Plugin', 'config')))
                {
                    try
                    {
                        $options = Typecho_API::factory('Widget_Abstract_Options');
                        
                        /** 初始化表单 */
                        $this->form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Plugins/Edit.do', $options->index),
                        Typecho_Widget_Helper_Form::POST_METHOD);
                        
                        /** 配置插件面板 */
                        call_user_func(array($pluginName . '_Plugin', 'config'), $this->form);
                        Typecho_API::factory('Widget_Abstract_Options')->insert(array('value' => serialize($this->form->getValues()),
                        'name' => 'plugin:' . $pluginName));
                    }
                    catch(Typecho_Plugin_Exception $e)
                    {
                        /** 截获异常 */
                        Typecho_API::factory('Widget_Notice')->set($e->getMessage(), NULL, 'error');
                        Typecho_API::goBack();
                    }
                }
                
                /** 提示信息 */
                Typecho_API::factory('Widget_Notice')->set(_t("插件已经被激活"), NULL, 'success');
                
                /** 转向原页 */
                Typecho_API::goBack();
            }
        }
    }
    
    /**
     * 禁用插件
     * 
     * @access public
     * @return void
     */
    public function deactivatePlugin()
    {
        $pluginName = Typecho_Request::getParameter('plugin');
        $pluginFileName = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '/Plugin.php';
        
        /** 获取已激活插件 */
        $activatedPlugins = Typecho_API::factory('Widget_Abstract_Options')->plugins;
        
        if(file_exists($pluginFileName))
        {
            require_once $pluginFileName;
            
            /** 获取插件信息 */
            if(is_callable(array($pluginName . '_Plugin', 'deactivate')) && 
            in_array($pluginName, $activatedPlugins))
            {
                try
                {
                    call_user_func(array($pluginName . '_Plugin', 'deactivate'));
                }
                catch(Typecho_Plugin_Exception $e)
                {
                    /** 截获异常 */
                    Typecho_API::factory('Widget_Notice')->set($e->getMessage(), NULL, 'error');
                    Typecho_API::goBack();
                }
            
                unset($activatedPlugins[array_search($pluginName, $activatedPlugins)]);
                Typecho_API::factory('Widget_Abstract_Options')->update(array('value' => serialize($activatedPlugins)),
                Typecho_Db::get()->sql()->where('`name` = ?', 'plugins'));
                
                Typecho_API::factory('Widget_Abstract_Options')->delete(Typecho_Db::get()->sql()->where('`name` = ?', 'plugin:' . $pluginName));
                
                /** 提示信息 */
                Typecho_API::factory('Widget_Notice')->set(_t("插件已经被禁用"), NULL, 'success');
                
                /** 转向原页 */
                Typecho_API::goBack();
            }
        }
    }
    
    /**
     * 配置插件
     * 
     * @access public
     * @return void
     */
    public function configPlugin()
    {
        $form = Typecho_API::factory('Widget_Plugins_Config')->form;
        
        /** 验证表单 */
        try
        {
            $form->validate();
        }
        catch(Typecho_Widget_Exception $e)
        {
            Typecho_API::goBack('#edit');
        }
        
        $pluginName = Typecho_Request::getParameter('plugin');
        $settings = $form->getParameters();
        Typecho_API::factory('Widget_Abstract_Options')->update(array('value' => serialize($settings),
        'name' => 'plugin:' . $pluginName), Typecho_Db::get()->sql()->where('`name` = ?', 'plugin:' . $pluginName));
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("插件配置已经保存"), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('plugin.php', Typecho_API::factory('Widget_Abstract_Options')->adminUrl));
    }

    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_API::factory('Widget_Users_Current')->pass('administrator');
        Typecho_Request::bindParameter(array('do' => 'activate'), array($this, 'activatePlugin'));
        Typecho_Request::bindParameter(array('do' => 'deactivate'), array($this, 'deactivatePlugin'));
        Typecho_Request::bindParameter(array('do' => 'config'), array($this, 'configPlugin'));
        Typecho_API::redirect(Typecho_API::factory('Widget_Abstract_Options')->adminUrl);
    }
}
