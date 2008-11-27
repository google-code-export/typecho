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
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        /** 列出插件目录 */
        $pluginDirs = glob(__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__ . '/*');

        /** 获取已激活插件 */
        $plugins = Typecho_Plugin::export();
        $activatedPlugins = $plugins['activated'];
        
        foreach ($pluginDirs as $pluginDir) {
            if (is_dir($pluginDir)) {
                /** 获取插件名称 */
                $pluginName = basename($pluginDir);
            
                /** 获取插件主文件 */
                $pluginFileName = $pluginDir . '/Plugin.php';
            } else if (is_file($pluginDir)) {
                $pluginFileName = $pluginDir;
                $part = explode('.', $pluginDir);
                if (2 == count($part) && 'php' == $part[1]) {
                    $pluginName = $part[0];
                } else {
                    continue;
                }
            } else {
                continue;
            }
            
            if (file_exists($pluginFileName)) {
                $info = Typecho_Plugin::parseInfo($pluginFileName);
                $info['name'] = $pluginName;
                $info['activated'] = isset($activatedPlugins[$pluginName]);
                $this->push($info);
            }
        }
    }
}
