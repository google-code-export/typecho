<?php
/** 载入配置文件 */
require_once '../config.inc.php';

/** 注册一个初始化插件 */
_p('admin/header.php', 'Action')->begin();

$options = Typecho_Widget::widget('Widget_Options');
$user = Typecho_Widget::widget('Widget_User');
$notice = Typecho_Widget::widget('Widget_Notice');
