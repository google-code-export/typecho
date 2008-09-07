<?php
/**
 * 登出动作
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 登出组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Logout extends Typecho_Widget implements Widget_Interface_Action_Widget
{
    /**
     * 开始用户登出
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_API::factory('Widget_Users_Current')->logout();
        Typecho_API::redirect(Typecho_API::factory('Widget_Options')->index);
    }
}
