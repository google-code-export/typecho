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
 * @param string $fileName 文件名
 * @return TypechoPlugin
 * @throws TypechoPluginException
 */
function _p($fileName, $adapterName)
{
    return Typecho_Plugin::instance($fileName, ucfirst($adapterName));
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
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function init(array $plugins)
    {
        foreach($plugins as $pluginName => $pluginFileName)
        {
            if(file_exists($pluginFileName))
            {
                /** 载入插件主文件 */
                require_once $pluginFileName;
                
                /** 运行初始化方法 */
                call_user_func(array('Plugin_' . $pluginName, 'init'));
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
     * @param string $fileName 文件名
     * @return TypechoPlugin
     * @throws TypechoPluginException
     */
    public static function instance($fileName, $adapterName)
    {
        $realPath = realpath($fileName);
        if(empty(self::$_adapters[$realPath]))
        {
            require_once 'Typecho/Plugin/Adapter/' . $adapterName . '.php';
            $adapterName = 'Typecho_Plugin_Adapter_' . $adapterName;
            self::$_adapters[$realPath] = new $adapterName();
        }
        
        return self::$_adapters[$realPath];
    }
}
