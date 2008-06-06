<?php
/**
 * Typecho Blog Platform
 *
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
        if(Typecho::widget('Access')->hasLogin())
        {
            Typecho::widget('Access')->logout();
        }
        
        Typecho::redirect(Typecho::widget('Options')->index);
    }
}
