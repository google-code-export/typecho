<?php

/** Typecho_Widget_Helper_Layout */
require_once 'Typecho/Widget/Helper/Layout.php';

/**
 * HTML文档头帮手
 * 
 * @category typecho
 * @package Typecho_Widget_Helper_Header
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Decorator_Header extends Typecho_Widget_Helper_Layout
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
