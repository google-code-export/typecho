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
class Widget_Do extends Typecho_Widget
{
    /**
     * 入口函数,初始化路由器
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 验证路由地址 **/
        $widget = trim($this->request->widget, '/');
        $objectName = str_replace('/', '_', $widget);
        $widgetName = 'plugin' == Typecho_Router::$current ? $objectName : 'Widget_' . $objectName;
        $fileName = ('plugin' == Typecho_Router::$current ? $widget : 'Widget/' . $widget) . '.php';

        if (Typecho_Common::isAvailableClass($widgetName)) {
            require_once $fileName;
            
            if (class_exists($widgetName)) {
                $reflectionWidget =  new ReflectionClass($widgetName);
                if ($reflectionWidget->implementsInterface('Widget_Interface_Do')) {
                    $this->widget($widgetName)->action();
                    return;
                }
            }
        }

        throw new Typecho_Widget_Exception(_t('请求的路径不存在'), 404);
    }
}
