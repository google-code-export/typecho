<?php
/**
 * 根据作者取出最新文章
 * 
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入父类 */
require_once 'Posts.php';

class AuthorsRecentPostWidget extends PostsWidget
{
    public function render($author, $limit = 5)
    {
        $rows = $this->db->fetchAll($this->db->sql()
        ->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, table.metas.`slug` AS `category`, table.users.`screenName` AS `author`')
        ->join('table.metas', 'table.contents.`meta` = table.metas.`mid`', 'LEFT')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', 'LEFT')
        ->where('table.contents.`type` = ?', 'post')
        ->where('table.metas.`type` = ?', 'category')
        ->where('table.contents.`author` = ?', $author)
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->limit($limit), array($this, 'push'));
    }
}
