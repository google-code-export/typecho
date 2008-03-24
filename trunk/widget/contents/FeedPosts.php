<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 载入父类
 * 
 */
require_once 'Posts.php';

/**
 * 输出文章聚合
 * 
 * @package FeedPosts
 */
class FeedPosts extends Posts
{
    /**
     * 重载父类入口函数
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $rows = $this->db->fetchAll($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.slug, table.contents.created,
        table.contents.text, table.contents.commentsNum, table.metas.slug AS category, table.users.screenName as author')
        ->join('table.metas', 'table.contents.meta = table.metas.mid', 'LEFT')
        ->join('table.users', 'table.contents.author = table.users.uid', 'LEFT')
        ->where('table.contents.type = ?', 'post')
        ->where('table.metas.type = ?', 'category')
        ->where('table.contents.password = NULL')
        ->where('table.contents.allowFeed = ?', 'enable')
        ->where('table.contents.created < ?', $this->registry('Options')->gmt_time)
        ->group('table.contents.cid')
        ->order('table.contents.created', 'DESC')
        ->limit(10), array($this, 'push'));
    }
}
