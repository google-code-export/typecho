<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 组件所属钩子类
 *
 * @package Widget
 */
class TypechoWidgetHook
{
    /**
     * 当前所有钩子
     * 
     * @access private
     * @var array
     */
    private static $_hooks = array();
    
    /**
     * 注册钩子
     * 
     * @access public
     * @param string $hookName 组件名称
     * @param string $functionName 钩子函数名称
     * @return void
     */
    static public function register($hookName, $functionName)
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
     * @param string $hookName 组件名称
     * @return array
     */
    static public function call($hookName)
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
     * 返回标准化钩子名称
     * 
     * @access public
     * @param string $fileName 钩子文件名称
     * @param string $component 钩子部件名称
     * @return string
     */
    static public function name($fileName, $component = NULL)
    {
        return urlencode(realpath($fileName)) . (empty($component) ? NULL : '->' . $component);
    }
}
