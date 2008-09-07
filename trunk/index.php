<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入配置支持 */
require_once 'config.inc.php';

/** 初始化插件 */
Typecho_Plugin::init(Typecho_API::factory('Widget_Options')->plugins,
__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__);

/** 开始路由分发 */
Typecho_Router::dispatch();
