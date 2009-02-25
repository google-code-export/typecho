<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/** 升级编辑器接口 */
$this->db->query($this->db->insert('table.options')
        ->rows(array('name' => 'useRichEditor', 'value' => 1)));
