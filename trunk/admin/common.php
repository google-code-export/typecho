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
if ($user->pass('administrator', true) && !empty($currentMenu)) {
    $mustUpgrade = (!isset(Typecho_Common::$config['version']) || version_compare(str_replace('/', '.', Typecho_Common::$config['version']),
    str_replace('/', '.', $currentVersion), '>'));

    if ($mustUpgrade && '/admin/upgrade.php' != $currentMenu[2]) {
        Typecho_Response::redirect(Typecho_Common::url('upgrade.php', $options->adminUrl));
    } else if (!$mustUpgrade && '/admin/upgrade.php' == $currentMenu[2]) {
        Typecho_Response::redirect(Typecho_Common::url('index.php', $options->adminUrl));
    }
}
