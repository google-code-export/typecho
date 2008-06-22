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
 * 盒状分页样式
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_PageNavigator_Box extends Typecho_Widget_Helper_PageNavigator
{
    /**
     * 输出盒装样式分页栏
     *
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return unknown
     */
    public function render($prevWord = 'PREV', $nextWord = 'NEXT', $splitPage = 3, $splitWord = '...')
    {
        if($this->_total < 1)
        {
            return;
        }
    
        $from = max(1, $this->_currentPage - $splitPage);
        $to = min($this->_totalPage, $this->_currentPage + $splitPage);

        //输出上一页
        if($this->_currentPage > 1)
        {
            echo '<a class="prev" href="' . str_replace('{page}', $this->_currentPage - 1, $this->_pageTemplate) . '">'
            .$prevWord . '</a>';
        }

        //输出第一页
        if($from > 1)
        {
            echo '<a href="' . str_replace('{page}', 1, $this->_pageTemplate) . '">1</a>';
            //输出省略号
            echo '<span>' . $splitWord . '</span>';
        }

        //输出中间页
        for($i = $from; $i <= $to; $i ++)
        {
            if($i != $this->_currentPage)
            {
                echo '<a href="' . str_replace('{page}', $i, $this->_pageTemplate) . '">'
                . $i . '</a>';
            }
            else
            {
                //当前页
                echo '<span class="current">' . $i . '</span>';
            }
        }

        //输出最后页
        if($to < $this->_totalPage)
        {
            echo '<span>' . $splitWord . '</span>';
            echo '<a href="' . str_replace('{page}', $this->_totalPage, $this->_pageTemplate) . '">'
            . $this->_totalPage . '</a>';
        }

        //输出下一页
        if($this->_currentPage < $this->_totalPage)
        {
            echo '<a class="next" href="' . str_replace('{page}', $this->_currentPage + 1, $this->_pageTemplate) . '">'
            . $nextWord . '</a>';
        }
    }
}
