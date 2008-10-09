<?php
/**
 * widget对象帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * widget对象帮手,用于处理空对象方法
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Null
{
    /**
     * 所有方法请求直接返回
     * 
     * @access public
     * @param string $name 方法名
     * @param array $args 参数列表
     * @return void
     */
    public function __call($name, $args)
    {
        return;
    }
}
