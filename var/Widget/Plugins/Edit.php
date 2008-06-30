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
        $pluginFileName = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '/' . $pluginName . '.php';
        
        /** 获取已激活插件 */
        $activatedPlugins = Typecho_API::factory('Widget_Abstract_Options')->plugins;
        
        if(file_exists($pluginFileName))
        {
            require_once $pluginFileName;
            
            /** 获取插件信息 */
            if(is_callable(array('Plugin_' . $pluginName, 'activate')) && 
            !in_array($pluginName, $activatedPlugins))
            {
                call_user_func(array('Plugin_' . $pluginName, 'activate'));
                $activatedPlugins[] = $pluginName;
                Typecho_API::factory('Widget_Abstract_Options')->update(array('value' => serialize($activatedPlugins)),
                Typecho_Db::get()->sql()->where('`name` = ?', 'plugins'));
                
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
        $pluginFileName = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '/' . $pluginName . '.php';
        
        /** 获取已激活插件 */
        $activatedPlugins = Typecho_API::factory('Widget_Abstract_Options')->plugins;
        
        if(file_exists($pluginFileName))
        {
            require_once $pluginFileName;
            
            /** 获取插件信息 */
            if(is_callable(array('Plugin_' . $pluginName, 'deactivate')) && 
            in_array($pluginName, $activatedPlugins))
            {
                call_user_func(array('Plugin_' . $pluginName, 'deactivate'));
                unset($activatedPlugins[array_search($pluginName, $activatedPlugins)]);
                Typecho_API::factory('Widget_Abstract_Options')->update(array('value' => serialize($activatedPlugins)),
                Typecho_Db::get()->sql()->where('`name` = ?', 'plugins'));
                
                /** 提示信息 */
                Typecho_API::factory('Widget_Notice')->set(_t("插件已经被禁用"), NULL, 'success');
                
                /** 转向原页 */
                Typecho_API::goBack();
            }
        }
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
        Typecho_API::redirect(Typecho_API::factory('Widget_Abstract_Options')->adminUrl);
    }
}
