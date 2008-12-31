<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/** Typecho_Widget_Helper_Layout */
require_once 'Typecho/Widget/Helper/Layout.php';

/**
 * HTML文档头帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Layout_Header extends Typecho_Widget_Helper_Layout
{
    /**
     * 重载构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct(NULL, NULL);
    }
    
    /**
     * 重载输出函数
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->html();
    }
}
