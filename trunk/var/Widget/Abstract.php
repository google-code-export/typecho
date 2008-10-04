<?php
/**
 * 纯数据抽象组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 纯数据抽象组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
abstract class Widget_Abstract extends Typecho_Widget
{
    /**
     * 获取数据对象
     * 
     * @access public
     * @return void
     */
    protected static function db()
    {
        return Typecho_Db::get();
    }
    
    /**
     * 获取选项对象
     * 
     * @access public
     * @return void
     */
    protected static function options()
    {
        return Typecho_API::factory('Widget_Options');
    }
    
    /**
     * 获取用户对象
     * 
     * @access public
     * @return void
     */
    protected static function user()
    {
        return Typecho_API::factory('Widget_Users_Current');
    }
}

