<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

if (is_writeable(__TYPECHO_ROOT_DIR__ . '/config.inc.php')) {
    $handle = fopen(__TYPECHO_ROOT_DIR__ . '/config.inc.php', 'ab');
    fwrite($handle, '
/** 初始化时区 */
Typecho_Date::setTimezoneOffset($options->timezone);
');
    fclose($handle);
} else {
    throw new Typecho_Exception(_t('config.inc.php 文件无法写入, 请将它的权限设置为可写'));
}
