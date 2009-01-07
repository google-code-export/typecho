<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/** 转换评论 */
$i = 1;

while (true) {
    $result = $this->db->query($this->db->select('coid', 'text')->from('table.comments')
    ->order('coid', Typecho_Db::SORT_ASC)->page($i, 100));
    $j = 0;
    
    while ($row = $this->db->fetchRow($result)) {
        $text = nl2br($row['text']);

        $this->db->query($this->db->update('table.comments')
        ->rows(array('text' => $text))
        ->where('coid = ?', $row['coid']));
        
        $j ++;
        unset($text);
        unset($row);
    }
    
    if ($j < 100) {
        break;
    }
    
    $i ++;
    unset($result);
}

/** 转换内容 */
$i = 1;

while (true) {
    $result = $this->db->query($this->db->select('cid', 'text')->from('table.contents')
    ->order('cid', Typecho_Db::SORT_ASC)->page($i, 100));
    $j = 0;
    
    while ($row = $this->db->fetchRow($result)) {
        $text = preg_replace(
        array("/\s*<p>/is", "/\s*<\/p>\s*/is", "/\s*<br\s*\/>\s*/is",
        "/\s*<(div|blockquote|pre|table|ol|ul)>/is", "/<\/(div|blockquote|pre|table|ol|ul)>\s*/is"),
        array('', "\n\n", "\n", "\n\n<\\1>", "</\\1>\n\n"), 
        $row['text']);

        $this->db->query($this->db->update('table.contents')
        ->rows(array('text' => $text))
        ->where('cid = ?', $row['cid']));
        
        $j ++;
        unset($text);
        unset($row);
    }
    
    if ($j < 100) {
        break;
    }
    
    $i ++;
    unset($result);
}
