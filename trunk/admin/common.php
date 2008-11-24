<?php
/** 载入配置文件 */
require_once dirname(__FILE__) . '/../config.inc.php';

/** 定义路由参数 */
Typecho_Router::setRoutes(Typecho_Widget::widget('Widget_Options')->routingTable);

/** 初始化插件 */
Typecho_Plugin::init(Typecho_Widget::widget('Widget_Options')->plugins,
array(Typecho_Widget::widget('Widget_Options'), 'getPluginOption'));

/** 注册一个初始化插件 */
Typecho_Plugin::factory('admin/header.php')->begin();

$options = Typecho_Widget::widget('Widget_Options');
$user = Typecho_Widget::widget('Widget_User');
$notice = Typecho_Widget::widget('Widget_Notice');
$menu = Typecho_Widget::widget('Widget_Menu');
