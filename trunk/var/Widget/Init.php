<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 初始化模块
 *
 * @package Widget
 */
class Widget_Init extends Typecho_Widget
{
    /**
     * 入口函数,初始化路由器
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 对变量赋值 */
        $options = $this->widget('Widget_Options');
        
        /** 初始化charset */
        Typecho_Common::$config['charset'] = $options->charset;
        
        /** 设置路径 */
        Typecho_Router::setPathInfo($this->request->getPathInfo());
        
        /** 初始化路由器 */
        Typecho_Router::setRoutes($options->routingTable);
        
        /** 初始化插件 */
        Typecho_Plugin::init($options->plugins);
        
        /** 初始化回执 */
        $this->response->setCharset($options->charset);
        $this->response->setContentType($options->contentType);

        /** 默认时区 */
        if (!ini_get("date.timezone") && function_exists("date_default_timezone_set")) {
            @date_default_timezone_set('UTC');
        }
        
        /** 初始化时区 */
        Typecho_Date::setTimezoneOffset($options->timezone);
        
        /** 监听缓冲区 */
        ob_start();
    }
}
