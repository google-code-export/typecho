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
 * @package Widget
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
        TypechoRoute::handle('./widget/do', 'do');
        widget('do.' . TypechoRoute::getParameter('do'));
    }
}
