<?php
/** 载入配置文件 **/
require_once '../includes/config.php';

/** 定义根目录 **/
define('__TYPECHO_ADMIN_DIR__', __TYPECHO_ROOT_DIR__ . '/admin');

/** 运行默认组件 **/
widget('Options')->to($options);
widget('Access')->to($access);
widget('Menu')->to($menu);
