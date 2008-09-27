<?php
/**
 * 后台头部
 * 
 * @category typecho
 * @package admin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入配置文件 */
require_once '../config.inc.php';

/** 初始化插件 */
Typecho_Plugin::init(Typecho_API::factory('Widget_Options')->plugins,
__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__);

/** 注册一个初始化插件 */
_p('admin/common.php', 'Action')->init();

$options = Typecho_API::factory('Widget_Options');
$access = Typecho_API::factory('Widget_Users_Current');
$notice = Typecho_API::factory('Widget_Notice');
