<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

define('__TYPECHO_WIDGET_DIR__', './widget');

/**
 * 实例化控件函数
 *
 * @param string $widget 控件名称
 * @return TypechoWidget
 */
function widget()
{
    static $widgetList;
    
    $widget = func_get_arg(0);
    $className = array_pop(explode('.', $widget));
    
    if(!($object = isset($widgetList[$widget]) ? $widgetList[$widget] : NULL))
    {
        if(!class_exists($className))
        {
            require(__TYPECHO_WIDGET_DIR__ . '/' . str_replace('.', '/', $widget) . '.php');
        }
        $object = new $className();
        $widgetList[$widget] = &$object;
        
        $args = func_get_args();
        array_shift($args);
    
        call_user_func_array(array(&$object, 'run'), $args);
    }
    
    return $object;
}

abstract class TypechoWidget
{
    abstract public function run();
}
