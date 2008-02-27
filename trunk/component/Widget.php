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
 * 控件控制
 *
 * @package Component
 */
class WidgetComponent extends TypechoComponent
{
    public function __call($name, $args)
    {
        $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $className = $name . 'WidgetComponent';
        
        if(!class_exists($className))
        {
            require 'Widget/' . $name . 'Widget.php';
        }
        
        $widget = new $className();
        call_user_func_array(array($widget, 'render'), $args);
    }
}
