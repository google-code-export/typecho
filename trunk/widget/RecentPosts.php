<?php
/**
 * 最新文章
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入内容基类 */
require_once 'Abstract/Contents.php';

/**
 * 最新评论组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class RecentPostsWidget extends ContentsWidget
{
    /**
     * 入口函数
     * 
     * @access public
     * @param integer $pageSize 文章数量
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $this->pageSize = empty($pageSize) ? $this->options->postsListSize : $pageSize;
    
        $this->db->fetchAll($this->selectSql->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime)
        ->where('table.contents.`type` = ?', 'post')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->limit($this->pageSize), array($this, 'push'));
    }
}
