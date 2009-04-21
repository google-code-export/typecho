<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/** 增加自定义主页 */
$this->db->query($this->db->insert('table.options')
        ->rows(array('name' => 'customHomePage', 'value' => 0)));
        
/** 增加文件上传散列函数 */
$this->db->query($this->db->insert('table.options')
        ->rows(array('name' => 'uploadHandle', 'value' => 'a:2:{i:0;s:13:"Widget_Upload";i:1;s:12:"uploadHandle";}')));
        
/** 增加文件展现散列函数 */
$this->db->query($this->db->insert('table.options')
        ->rows(array('name' => 'attachmentHandle', 'value' => 'a:2:{i:0;s:13:"Widget_Upload";i:1;s:16:"attachmentHandle";}')));
        
/** 增加文件扩展名 */
$this->db->query($this->db->insert('table.options')
        ->rows(array('name' => 'attachmentTypes', 'value' => '*.jpg;*.gif;*.png;*.zip;*.tar.gz')));
        
/** 增加路由 */
$routingTable = $this->options->routingTable;
if (isset($routingTable[0])) {
    unset($routingTable[0]);
}

$pre = array_slice($routingTable, 0, 2);
$next = array_slice($routingTable, 2);

$routingTable = array_merge($pre, array('attachment' => 
  array (
    'url' => '/attachment/[cid:digital]/',
    'widget' => 'Widget_Archive',
    'action' => 'render',
  )), $next);

$this->db->query($this->db->update('table.options')
        ->rows(array('value' => serialize($routingTable)))
        ->where('name = ?', 'routingTable'));
