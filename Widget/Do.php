<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/**
 * 执行模块
 *
 * @package Widget
 */
class Widget_Do extends Typecho_Widget implements Typecho_Widget_Interface_Action
{
    /**
     * 入口函数,初始化路由器
     *
     * @access public
     * @return void
     */
    public function action()
    {
        /** 验证路由地址 **/
        $prefix = 'plugin' == Typecho_Router::$current ? 'Plugin' : 'Widget';
        $widgetName = $prefix . '_' . str_replace('/', '_', Typecho_Router::getParameter('do'));
        $fileName = __TYPECHO_ROOT_DIR__ . '/' . $prefix . '/' . Typecho_Router::getParameter('do') . '.php';
        
        if(file_exists($fileName))
        {
            require_once $fileName;
            
            if(class_exists($widgetName))
            {
                $reflectionWidget =  new ReflectionClass($widgetName);
                if($reflectionWidget->implementsInterface('Widget_Interface_Do' . $prefix))
                {
                    Typecho_API::factory($widgetName)->render();
                    return;
                }
            }
        }

        throw new Typecho_Widget_Exception(_t('动作%s不存在', $widgetName), Typecho_Exception::NOTFOUND);
    }
}
