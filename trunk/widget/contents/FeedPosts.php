<?php

require_once 'Posts.php';

class FeedPosts extends Posts
{
    public function render()
    {
        $rows = $this->db->fetchAll($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created,
        table.contents.text, table.contents.comments_num, table.metas.slug AS category_slug, table.users.screen_name as author')
        ->join('table.metas', 'table.contents.meta = table.metas.mid')
        ->join('table.users', 'table.contents.author = table.users.uid')
        ->where('table.contents.type = ?', 'post')
        ->where('table.metas.type = ?', 'category')
        ->where('table.contents.allow_feed = ?', 'enable')
        ->where('table.contents.created < ?', $this->registry('Options')->gmt_time)
        ->group('table.contents.cid')
        ->order('table.contents.created', 'DESC')
        ->limit(10), array($this, 'push'));
    }
}
