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
 * 用户登出操作
 *
 * @category Widget
 */
class LogoutWidget extends TypechoWidget
{
    /**
     * 执行用户登出操作
     * 
     * @return void
     */
    public function render()
    {
        if(widget('Access')->hasLogin())
        {
            widget('Access')->logout();
        }
        
        Typecho::redirect(widget('Options')->index);
    }
}
