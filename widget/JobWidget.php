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
 * @package DoWidget
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
        $this->registry('Access')->pass('system');
        TypechoRoute::handle('./widget/job', 'do');
        
        $args = empty($_POST['args']) ? array() : unserialize($_POST['args']);
        array_unshift($args, 'job.' . $_GET['do']);
        
        call_user_func_array('widget', $args);
    }
}
