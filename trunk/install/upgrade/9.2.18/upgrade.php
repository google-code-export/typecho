<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/** 升级编辑器接口 */
$this->db->query($this->db->update('table.options')
        ->rows(array('value' => 350))
        ->where('name = ?', 'editorSize'));
