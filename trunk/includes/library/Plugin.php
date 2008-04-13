<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

class TypechoPlugin
{
    /**
     * 当前所有钩子
     * 
     * @access private
     * @var array
     */
    private static $_hooks = array();
    
    /**
     * 当前所有过滤器
     * 
     * @access private
     * @var array
     */
    private static $_filters = array();
    
    /**
     * 激活插件
     * 
     * @param string $pluginName 插件名称
     * @param string $action 默认动作
     * @return mixed
     */
    static public function activate($pluginName, $action = 'activate')
    {
        if(file_exists($fileName = __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '.php'))
        {
            require_once $fileName;
        }
        else
        {
            require_once __TYPECHO_PLUGIN_DIR__ . '/' . $pluginName . '/' . $pluginName . '.php';
        }
        
        $functionName = array($pluginName, $action);
        if(is_callable($functionName))
        {
            return call_user_func($functionName);
        }
        
        return false;
    }
    
    /**
     * 禁用插件
     * 
     * @param string $pluginName 插件名称
     */
    static public function deactivate($pluginName)
    {
        self::activate($pluginName, 'deactivate');
    }
    
    /**
     * 载入插件
     * 
     * @param array $pluginsList 插件列表
     */
    static public function init(array $pluginsList)
    {
        foreach($pluginsList as $pluginName)
        {
            self::activate($pluginName, 'init');
        }
    }
    
    /**
     * 插件信息
     * 
     * @param array $pluginsList 插件列表
     */
    static public function info($pluginName)
    {
        return self::activate($pluginName, 'return');
    }
    
    /**
     * 返回标准化名称
     * 
     * @access public
     * @param string $fileName 文件名称
     * @param string $component 部件名称
     * @return string
     */
    static public function name($fileName, $component = NULL)
    {
        return urlencode(realpath($fileName)) . (empty($component) ? NULL : '->' . $component);
    }
    
    /**
     * 注册钩子
     * 
     * @access public
     * @param string $hookName 钩子名称
     * @param mixed $functionName 钩子函数名称
     * @return void
     */
    static public function registerHook($hookName, $functionName)
    {
        if(empty(self::$_hooks[$hookName]))
        {
            self::$_hooks[$hookName] = array();
        }
        
        self::$_hooks[$hookName][] = $functionName;
    }
    
    /**
     * 运行钩子
     * 
     * @access public
     * @param string $hookName 钩子名称
     * @return array
     */
    static public function callHook($hookName)
    {
        $args = func_get_args();
        array_shift($args);
        $result = array();
    
        if(!empty(self::$_hooks[$hookName]))
        {
            foreach(self::$_hooks[$hookName] as $functionName)
            {
                $result[$functionName] = call_user_func_array($functionName, $args);
            }
        }
        
        return $result;
    }
    
    /**
     * 注册过滤器
     * 
     * @access public
     * @param string $filterName 过滤器名称
     * @param mixed $functionName 过滤器函数名称
     * @return void
     */
    static public function registerFilter($filterName, $functionName)
    {
        if(empty(self::$_filters[$filterName]))
        {
            self::$_filters[$filterName] = array();
        }
        
        self::$_filters[$filterName][] = $functionName;
    }
    
    /**
     * 运行过滤器
     * 
     * @access public
     * @param string $filterName 过滤器名称
     * @param array $input 需要过滤的数组
     * @return array
     */
    static public function callFilter($filterName, &$input)
    {
        if(!empty(self::$_filters[$filterName]))
        {
            foreach(self::$_filters[$filterName] as $functionName)
            {
                $functionName($input);
            }
        }
    }
}
