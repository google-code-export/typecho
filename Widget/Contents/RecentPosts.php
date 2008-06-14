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

/**
 * 最新评论组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_RecentPosts extends Widget_Abstract_Contents
{
    /**
     * 入口函数
     * 
     * @access public
     * @param integer $pageSize 文章数量
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
        
        $this->pageSize = empty($pageSize) ? $this->options->postsListSize : $pageSize;
    
        $this->db->fetchAll($this->select()->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime)
        ->where('table.contents.`type` = ?', 'post')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
        ->limit($this->pageSize), array($this, 'push'));
    }
}
