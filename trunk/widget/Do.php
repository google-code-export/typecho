<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
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
        widget('do.' . str_replace('.', '', TypechoRoute::getParameter('do')));
    }
}
