<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 57 2008-03-20 08:06:57Z magike.net $
 */

/**
 * 异步执行模块
 * 
 * @package Widget
 */
class JobWidget extends TypechoWidget
{
    /**
     * 入口函数,初始化路由器
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        widget('Access')->pass('system');

        $args = (NULL === TypechoRequest::getParameter('args')) ? array() : unserialize(TypechoRequest::getParameter('args'));
        array_unshift($args, 'job.' . TypechoRoute::getParameter('job'));
        
        call_user_func_array('widget', $args);
    }
}
