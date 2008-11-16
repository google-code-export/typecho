<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 插件列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Plugins_List extends Typecho_Widget
{
    public function __construct()
    {
        /** 列出插件目录 */
        $pluginDirs = glob(__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/*');

        /** 获取已激活插件 */
        $activatedPlugins = Typecho_API::factory('Widget_Options')->plugins;
        
        foreach ($pluginDirs as $pluginDir) {
            /** 获取插件名称 */
            $pluginName = basename($pluginDir);
        
            /** 获取插件主文件 */
            $pluginFileName = $pluginDir . '/Plugin.php';
            
            if (file_exists($pluginFileName)) {
                require_once $pluginFileName;
                
                /** 获取插件信息 */
                if (is_callable(array($pluginName . '_Plugin', 'information'))) {
                    $information = call_user_func(array($pluginName . '_Plugin', 'information'));
                    $information['name'] = $pluginName;
                    $information['check'] = isset($information['check']) ? 
                    str_replace('{version}', $information['version'], $information['check']) : $information['homepage'];
                    $information['activated'] = in_array($pluginName, $activatedPlugins);
                    $this->push($information);
                }
            }
        }
    }
}
