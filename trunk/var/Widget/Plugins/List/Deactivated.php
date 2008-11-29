<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 禁用插件列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Plugins_List_Deactivated extends Widget_Plugins_List
{
    /**
     * 准备函数
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {
        $this->parameter->activated = false;
    }
}
