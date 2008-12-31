<?php
/** 载入配置文件 */
require_once dirname(__FILE__) . '/../config.inc.php';

/** 注册一个初始化插件 */
Typecho_Plugin::factory('admin/common.php')->begin();

Typecho_Widget::widget('Widget_User')->to($user);
Typecho_Widget::widget('Widget_Notice')->to($notice);
Typecho_Widget::widget('Widget_Menu')->to($menu);
