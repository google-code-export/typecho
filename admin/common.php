<?php
/** 载入配置文件 */
require_once dirname(__FILE__) . '/../config.inc.php';

/** 注册一个初始化插件 */
Typecho_Plugin::factory('admin/common.php')->begin();

Typecho_Widget::widget('Widget_User')->to($user);
Typecho_Widget::widget('Widget_Notice')->to($notice);
Typecho_Widget::widget('Widget_Menu')->to($menu);

/** 检测版本是否升级 */
$currentMenu = $menu->getCurrentMenu();
list($soft, $currentVersion) = explode(' ', $options->generator);
if ($user->pass('administrator', true) && '/admin/upgrade.php' != $currentMenu[2]) {
    if (!isset(Typecho_Common::$config['version']) || version_compare(str_replace('/', '.', Typecho_Common::$config['version']),
    str_replace('/', '.', $currentVersion), '>')) {
        Typecho_Response::redirect(Typecho_Common::url('upgrade.php', $options->adminUrl));
    }
}
