<?php
/** ���������ļ� **/
require_once '../includes/config.php';

/** �����Ŀ¼ **/
define('__TYPECHO_ADMIN_DIR__', __TYPECHO_ROOT_DIR__ . '/admin');

/** ����Ĭ����� **/
widget('Options')->to($options);
widget('Access')->to($access);
widget('Menu')->to($menu);
