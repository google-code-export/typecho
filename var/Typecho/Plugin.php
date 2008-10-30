<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

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
     * 所有激活的插件
     * 
     * @access private
     * @var array
     */
    private static $_plugins = array();
    
    /**
     * 已经加载的文件
     * 
     * @access private
     * @var array
     */
    private static $_required = array();
    
    /**
     * 实例化的插件对象
     * 
     * @access private
     * @var array
     */
    private static $_instances;
    
    /**
     * 临时存储变量
     * 
     * @access private
     * @var array
     */
    private static $_tmp = array();
    
    /**
     * 唯一句柄
     * 
     * @access private
     * @var string
     */
    private $_handle;
    
    /**
     * 组件
     * 
     * @access private
     * @var string
     */
    private $_component;
    
    /**
     * 插件初始化
     * 
     * @access public
     * @param string $handle 插件
     * @return void
     */
    public function __construct($handle)
    {
        /** 初始化变量 */
        $this->_handle = $handle;
    }

    /**
     * 插件初始化
     * 
     * @access public
     * @param array $plugins 插件列表
     * @return void
     */
    public static function init(array $plugins)
    {
        $plugins['activated'] = array_key_exists('activated', $plugins) ? $plugins['activated'] : array();
        $plugins['handles'] = array_key_exists('handles', $plugins) ? $plugins['handles'] : array();
        $plugins['files'] = array_key_exists('files', $plugins) ? $plugins['files'] : array();
        
        /** 初始化变量 */
        self::$_plugins = $plugins;
    }
    
    /**
     * 获取实例化插件对象
     * 
     * @access public
     * @return Typecho_Plugin
     */
    public static function factory($handle)
    {
        return isset(self::$_instances[$handle]) ? self::$_instances[$handle] : (self::$_instances[$handle] = new Typecho_Plugin($handle));
    }
    
    /**
     * 激活插件
     * 
     * @access public
     * @param string $pluginName 插件名称
     * @return void
     */
    public static function activate($pluginName)
    {
        self::$_plugins['activated'][$pluginName] = self::$_tmp;
        self::$_tmp = array();
    }
    
    /**
     * 禁用插件
     * 
     * @access public
     * @param string $pluginName 插件名称
     * @return void
     */
    public static function deactivate($pluginName)
    {
        /** 去掉所有相关文件 */
        foreach(self::$_plugins['activated'][$pluginName]['files'] as $handle => $files)
        {
            self::$_plugins['files'][$handle] = array_diff(self::$_plugins['files'][$handle], $files);
        }
        
        /** 去掉所有相关回调函数 */
        foreach(self::$_plugins['activated'][$pluginName]['handles'] as $handle => $handles)
        {
            self::$_plugins['handles'][$handle] = array_diff(self::$_plugins['handles'][$handle], $handles);
        }
        
        /** 禁用当前插件 */
        unset(self::$_plugins['activated'][$pluginName]);
    }
    
    /**
     * 导出当前插件设置
     * 
     * @access public
     * @return array
     */
    public static function export()
    {
        return self::$_plugins;
    }
    
    /**
     * 需要预先包含的文件
     * 
     * @access public
     * @param string $file 文件名称(相对路径)
     * @return void
     */
    public function need($file)
    {
        $handle = $this->_handle . ':' . $this->_component;
        self::$_plugins['files'][$handle][] = $file;
        self::$_tmp['files'][$handle][] = $file;
    }
    
    /**
     * 设置回调函数
     * 
     * @access public
     * @param string $handle 句柄
     * @param mixed $value 回调函数
     * @return void
     */
    public function __set($handle, $value)
    {
        $handle = $this->_handle . ':' . $handle;
        self::$_plugins['handles'][$handle][] = $value;
        self::$_tmp['handles'][$handle][] = $value;
    }
    
    /**
     * 通过魔术函数设置当前组件位置
     * 
     * @access public
     * @param string $component 当前组件
     * @return void
     */
    public function __get($component)
    {
        $this->_component = $component;
    }
    
    /**
     * 回调处理函数
     * 
     * @access public
     * @param string $handle 句柄
     * @param string $args 参数
     * @return mixed
     */
    public function __call($handle, $args)
    {
        $handle = $this->_handle . ':' . $handle;
        $last = count($args);
        
        if(isset($this->_required[$handle]) && isset(self::$_plugins['files'][$handle]))
        {
            $this->_required[$handle] = true;
            foreach(self::$_plugins['files'][$handle] as $file)
            {
                require_once $file;
            }
        }
    
        if(isset(self::$_plugins['handles'][$handle]))
        {
            $args[$last] = NULL;
            foreach(self::$_plugins['handles'][$handle] as $callback)
            {
                $args[$last] = call_user_func_array($callback, $args);
            }
        }
        
        return $args[$last];
    }
}
