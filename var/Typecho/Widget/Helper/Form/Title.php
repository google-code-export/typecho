<?php
/**
 * 文字输入表单项帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Widget_Helper_Layout */
require_once 'Typecho/Widget/Helper/Layout.php';

/**
 * 文字输入表单项帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Title extends Typecho_Widget_Helper_Layout
{

    public function __construct($title, $description = NULL)
    {
        /** 创建html元素,并设置class */
        parent::__construct('ul', array('class' => 'typecho-option typecho-option-clear'));
        $container = new Typecho_Widget_Helper_Layout('li', array('class' => 'typecho-option-title'));
        $this->addItem($container);
        
        $h3 = new Typecho_Widget_Helper_Layout('h3');
        $h3->html($title);
        $container->addItem($h3);
        
        if (!empty($description)) {
            $cite = new Typecho_Widget_Helper_Layout('p');
            $cite->html($description);
            $container->addItem($cite);
        }
    }
}
