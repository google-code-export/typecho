<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 国际化语言 */
require_once 'Typecho/I18n.php';

/** 载入异常支持 */
require_once 'Typecho/Plugin/Exception.php';

/** 载入接口支持 */
require_once 'Typecho/Plugin/Interface.php';

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
     * @param Typecho_Widget_Abstract_Plugin $pluginWidget 插件管理组件
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function init($pluginWidget)
    {
        /** 初始化插件管理组件 */
        self::$_pluginWidget = $pluginWidget;
        
        /** 初始化插件列表 */
        $plugins = $pluginWidget->getActivated();

        foreach($plugins as $plugin)
        {
            if(file_exists($pluginFileName = self::$_rootPath . '/' . $plugin . '/' . $plugin . '.php'))
            {
                /** 载入插件主文件 */
                require_once $pluginFileName;
                
                /** 运行初始化方法 */
                call_user_func(array($plugin . 'Plugin', 'init'));
            }
            else
            {
                /** 如果不存在则抛出异常 */
                throw new Typecho_Plugin_Exception(_t('插件文件不存在 %s', $pluginFileName), Typecho_Exception::RUNTIME);
            }
        }
    }
    
    /**
     * 生成动作适配器
     * 
     * @access public
     * @param string $fileName 文件名
     * @return void
     */
    public static function action($fileName)
    {
        return self::instance($fileName, 'Action');
    }
    
    /**
     * 生成过滤器适配器
     * 
     * @access public
     * @param string $fileName 文件名
     * @return void
     */
    public static function filter($fileName)
    {
        return self::instance($fileName, 'Filter');
    }
    
    /**
     * 生成布局适配器
     * 
     * @access public
     * @param string $fileName 文件名
     * @return void
     */
    public static function layout($fileName)
    {
        return self::instance($fileName, 'Layout');
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
        if(file_exists($fileName))
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
        
        throw new Typecho_Plugin_Exception(_t('插件目标文件不存在 %s', $fileName), Typecho_Exception::RUNTIME);
    }
}
