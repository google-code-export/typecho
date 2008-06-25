<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 最近评论组件,只显示评论
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Recent_Comment extends Widget_Abstract_Comments
{
    /**
     * 入口函数
     *
     * @access public
     * @param integer $pageSize 评论数量
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
        $pageSize = empty($pageSize) ? $this->options->commentsListSize : $pageSize;
        
        $this->db->fetchAll($this->select()->limit($pageSize)
        ->where('table.comments.`type` = ?', 'comment')
        ->where('table.comments.`status` = ?', 'approved')
        ->group('table.comments.`coid`')
        ->order('table.comments.`created`', Typecho_Db::SORT_DESC), array($this, 'push'));
    }
}
