<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */
 
 /** 载入文章基类支持 **/
require_once 'Abstract/Comments.php';
 
/**
 * 评论归档组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class ArchiveCommentsWidget extends CommentsWidget
{    
    /**
     * 入口函数
     *
     * @access public
     * @param string $mode 显示的评论类型
     * @param integer $pageSize 分页评论数量
     * @return void
     */
    public function render($mode = NULL, $pageSize = 0)
    {
        $this->selectSql->where('table.comments.`status` = ?', 'approved');
        
        if(NULL !== TypechoRoute::getParameter('cid'))
        {
            $this->selectSql->where('table.contents.`cid` = ?', TypechoRoute::getParameter('cid'));
        }

        if(NULL !== TypechoRoute::getParameter('slug'))
        {
            $this->selectSql->where('table.contents.`slug` = ?', TypechoRoute::getParameter('slug'));
        }
        
        if(!empty($mode))
        {
            $this->selectSql->where('table.comments.`mode` = ?', $mode);
        }
        
        $this->countSql = clone $this->selectSql;
        
        if($pageSize > 0)
        {
            $this->pageSize = $pageSize;
            $this->selectSql->page($this->currentPage, $this->pageSize);
        }
        
        $this->db->fetchAll($this->selectSql->group('table.comments.`coid`')
        ->order('table.comments.`created`', TypechoDb::SORT_DESC), array($this, 'push'));
    }
}
