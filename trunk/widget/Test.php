<?php

class Test extends TypechoWidget
{
    public function render()
    {
        $db = TypechoDb::get();
        
        $db->fetchAll($db->sql()
        ->select('table.posts')
        ->order('post_id', 'DESC')
        ->limit(5), array($this, 'push'));
    }
}
