<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入异常支持 */
require_once 'Typehco/Plugin/Exception.php';

/** 载入接口支持 */
require_once 'Typehco/Plugin/Interface.php';

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
    /** 钩子类型 */
    const HOOK = 0;
    
    /** 过滤器类型 */
    const FILTER = 1;

    /**
     * 插件根目录
     * 
     * @access private
     * @var string
     */
    private static $_rootPath = '/Plugins/';

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
     * 设置插件根目录
     * 
     * @access public
     * @param string $rootPath 插件根目录
     * @return void
     */
    public static function setRoot($rootPath)
    {
        self::$_rootPath = $rootPath;
    }

    /**
     * 插件初始化
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function init()
    {
        /** 初始化插件列表 */
        $plugins = array();
    
        /** 载入插件列表 */
        require self::$_rootPath . '/plugins.php';

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
     * 获取插件列表
     * 
     * @access public
     * @return array
     */
    public static function listAll()
    {
        /** 初始化插件列表 */
        $plugins = array();
        $result = array();
    
        /** 载入插件列表 */
        require self::$_rootPath . '/plugins.php';
        
        /** 获取所有插件 */
        $pluginFiles = glob(self::$_rootPath . '/*/*.php');
        
        foreach($pluginFiles as $pluginFile)
        {
            /** 获得类名 */
            $className = substr(basename($pluginFile), 0, -4);
            
            /** 载入插件 */
            require_once $pluginFile;
            
            /** 获取插件信息 */
            $info = call_user_func(array($className . 'Plugin', 'information'));
            $info['name'] = $className;
            $info['activated'] = in_array($className, $plugins);
            $info['status'] = $info['activated'] ? _t('已激活') : _t('已禁用');
            $info['check'] = !empty($info['check']) ? str_replace('{version}', $info['version'], $info['check']) : $info['homepage'];
            $result[] = $info;
        }
        
        return $result;
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
        /** 初始化插件列表 */
        $plugins = array();
    
        /** 载入插件列表 */
        require self::$_rootPath . '/plugins.php';
        
        if(!in_array($pluginName, $plugins))
        {
            require_once self::$_rootPath . '/' . $pluginName . '/' . $pluginName . '.php';
        
            /** 激活插件 */
            call_user_func(array($pluginName . 'Plugin', 'activate'));
            
            /** 写入插件表 */
            $plugins[] = $pluginName;
            
            file_put_contents(self::$_rootPath . '/plugins.php', '<?php $plugins = ' . var_export($plugins, true) . ';');
        }
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
        /** 初始化插件列表 */
        $plugins = array();
    
        /** 载入插件列表 */
        require self::$_rootPath . '/plugins.php';
        
        if(in_array($pluginName, $plugins))
        {
            require_once self::$_rootPath . '/' . $pluginName . '/' . $pluginName . '.php';
            
            /** 禁用插件 */
            call_user_func(array($pluginName . 'Plugin', 'deactivate'));
            
            /** 写入插件表 */
            unset($plugins[array_search($pluginName, $plugins)]);
            
            file_put_contents(self::$_rootPath . '/plugins.php', '<?php $plugins = ' . var_export($plugins, true) . ';');
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
                self::$_instance[$realPath] = new Typecho_Plugin();
            }
            
            return self::$_instance[$realPath];
        }
        
        throw new Typecho_Plugin_Exception(_t('插件目标文件不存在 %s', $fileName), Typecho_Exception::RUNTIME);
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
            throw new Typecho_Plugin_Exception(_t('回调函数不合法 %s', var_export($callback, true)), Typecho_Exception::RUNTIME);
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
