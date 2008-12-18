<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** Typecho_Widget_Helper_PageNavigator */
require_once 'Typecho/Widget/Helper/PageNavigator.php';

/**
 * 经典分页样式
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_PageNavigator_Classic extends Typecho_Widget_Helper_PageNavigator
{
    /**
     * 输出经典样式的分页
     *
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @return void
     */
    public function render($prevWord = 'PREV', $nextWord = 'NEXT')
    {
        if ($this->_total < 1) {
            return;
        }
    
        //输出下一页
        if ($this->_currentPage < $this->_totalPage) {
            echo '<a class="next" href="' , str_replace($this->_pageHolder, $this->_currentPage + 1, $this->_pageTemplate) , '">'
            , $nextWord , '</a>';
        }

        //输出上一页
        if ($this->_currentPage > 1) {
            echo '<a class="prev" href="' , str_replace($this->_pageHolder, $this->_currentPage - 1, $this->_pageTemplate) , '">'
            , $prevWord , '</a>';
        }
    }
}
