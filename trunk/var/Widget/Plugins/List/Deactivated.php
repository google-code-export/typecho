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
     * 构造函数
     * 
     * @access public
     * @param mixed $params 传递的参数
     * @return void
     */
    public function __construct($params = NULL)
    {
        parent::__construct($params);
        $this->parameter->activated = false;
    }
}
