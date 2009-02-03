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
        $text = preg_replace("/\s*<br\s*\/>\s*/i", "\n", $row['text']);

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
