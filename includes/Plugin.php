<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 异常基类 */
require_once 'Exception.php';

/** 载入异常支持 */
require_once 'Plugin/PluginException.php';

/** 载入插件列表 */
require_once __TYPECHO_PLUGIN_DIR__ . '/plugins.php';

/**
 * 插件处理类
 *
 * @author qining
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class TypechoPlugin
{
    /** 钩子类型 */
    const HOOK = 0;
    
    /** 过滤器类型 */
    const FILTER = 1;

    /**
     * 实例化对象列表
     * 
     * @access private
     * @var array
     */
    private static $_instance = array();
    
    /**
     * 回调函数列表
     * 
     * @access private
     * @var array
     */
    private $_callback = array();

    /**
     * 插件初始化
     * 
     * @access public
     * @return void
     * @throws TypechoPluginException
     */
    public static function init()
    {
        /** 检测配置是否存在 */
        TypechoConfig::need('Plugin');
        
        /** 初始化插件 */
        $plugins = TypechoConfig::get('Plugin');
        foreach($plugins as $plugin)
        {
            if(file_exists($pluginFileName = __TYPECHO_PLUGIN_DIR__ . '/' . $plugin . '/' . $plugin . '.php'))
            {
                /** 载入插件主文件 */
                require_once $pluginFileName;
            }
            else
            {
                /** 如果不存在则抛出异常 */
                throw new TypechoPluginException(_t('插件文件不存在 %s', $pluginFileName), TypechoException::RUNTIME);
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
    public static function instance($fileName)
    {
        if(file_exists($fileName))
        {
            $realPath = realpath($fileName);
            if(empty(self::$_instance[$realPath]))
            {
                self::$_instance[$realPath] = new TypechoPlugin();
            }
            
            return self::$_instance[$realPath];
        }
        
        throw new TypechoPluginException(_t('插件目标文件不存在 %s', $fileName), TypechoException::RUNTIME);
    }
    
    /**
     * 注册一个回调函数
     * 
     * @access public
     * @param integer $type 插件类型
     * @param string $method 方法名
     * @param mixed $callback 回调函数
     * @return void
     * @throws TypechoPluginException
     */
    public function register($type, $method, $callback)
    {
        if(is_callable($callback))
        {
            $this->_callback[$type][$method][] = $callback;
        }
        else
        {
            throw new TypechoPluginException(_t('回调函数不合法 %s', var_export($callback, true)), TypechoException::RUNTIME);
        }
    }
    
    /**
     * 魔术函数,钩住回调函数
     * 
     * @access public
     * @param string $method 方法名
     * @param array $args 参数
     * @return void
     */
    public function __call($method, array $args)
    {
        if(isset($this->_callback[self::HOOK][$method]))
        {
            foreach($this->_callback[self::HOOK][$method] as $callback)
            {
                call_user_func_array($callback, $args);
            }
        }
    }
    
    /**
     * 过滤器函数
     * 
     * @access public
     * @param string $method 方法名
     * @param array $value 需要过滤的数组
     * @return void
     */
    public function filter($method, array &$value)
    {
        if(isset($this->_callback[self::FILTER][$method]))
        {
            foreach($this->_callback[self::FILTER][$method] as $callback)
            {
                $value = call_user_func($callback, $value);
            }
        }
    }
}
