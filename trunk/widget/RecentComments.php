<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */
 
 /** 载入评论基类支持 **/
require_once 'Abstract/Comments.php';
 
/**
 * 最近评论组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class RecentCommentsWidget extends CommentsWidget
{    
    /**
     * 入口函数
     *
     * @access public
     * @param integer $pageSize 评论数量
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $this->selectSql->where('table.comments.`status` = ?', 'approved');
        $this->pageSize = empty($pageSize) ? $this->options->commentsListSize : $pageSize;
        
        $this->db->fetchAll($this->selectSql->limit($this->pageSize)
        ->group('table.comments.`coid`')
        ->order('table.comments.`created`', TypechoDb::SORT_DESC), array($this, 'push'));
    }
}
