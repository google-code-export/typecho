<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * Typecho_Plugin::instance别名
 * 
 * @access public
 * @param string $fileName 句柄
 * @return TypechoPlugin
 * @throws TypechoPluginException
 */
function _p($handle, $adapterName)
{
    return Typecho_Plugin::factory($handle, ucfirst($adapterName));
}

/**
 * 插件处理类
 *
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Plugin
{
    /**
     * 适配器列表
     * 
     * @access private
     * @var array
     */
    private static $_adapters = array();

    /**
     * 插件初始化
     * 
     * @access public
     * @param array $plugins 插件列表
     * @param string $rootPath 插件根目录
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function init(array $plugins, $rootPath)
    {
        foreach($plugins as $pluginName)
        {
            $pluginFileName = $rootPath . '/' . $pluginName . '/Plugin.php';
            if(file_exists($pluginFileName))
            {
                /** 载入插件主文件 */
                require_once $pluginFileName;
                
                /** 运行初始化方法 */
                call_user_func(array($pluginName . '_Plugin', 'init'));
            }
            else
            {
                /** 载入异常支持 */
                require_once 'Typecho/Plugin/Exception.php';
                /** 如果不存在则抛出异常 */
                throw new Typecho_Plugin_Exception("Plugin '{$pluginFileName}' not found", Typecho_Exception::RUNTIME);
            }
        }
    }

    /**
     * 根据唯一的文件名初始化一个plugin实例
     * 
     * @access public
     * @param string $handle 句柄
     * @return TypechoPlugin
     * @throws TypechoPluginException
     */
    public static function factory($handle, $adapterName)
    {
        $handle = trim($handle, '/ ');
        if(empty(self::$_adapters[$handle]))
        {
            require_once 'Typecho/Plugin/Adapter/' . $adapterName . '.php';
            $adapterName = 'Typecho_Plugin_Adapter_' . $adapterName;
            self::$_adapters[$handle] = new $adapterName();
        }
        
        return self::$_adapters[$handle];
    }
}
