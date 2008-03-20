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
 * 执行模块
 * 
 * @package DoWidget
 */
class DoWidget extends TypechoWidget
{
    /**
     * 入口函数,初始化路由器
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        if(!empty($_GET['isJob']) && 1 == $_GET['isJob'])
        {
            $this->registry('Access')->pass('system');
            TypechoRoute::handle('./widget/job', 'do');
            
            $args = empty($_POST['args']) ? array() : unserialize($_POST['args']);
            array_unshift($args, 'job.' . $_GET['do']);
            
            call_user_func_array('widget', $args);
        }
        else
        {
            TypechoRoute::handle('./widget/do', 'do');
            widget('do.' . $_GET['do']);
        }
    }
}
