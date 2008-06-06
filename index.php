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

/** 系统启动 */
Typecho::start();

/** 载入插件 */
TypechoPlugin::init();

/** 注册一个初始化插件 */
TypechoPlugin::instance(__FILE__)->start();

/** 通过路由器载入页面 */
TypechoRoute::target(__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . Typecho::widget('Options')->theme);

/** 注册一个结束插件 */
TypechoPlugin::instance(__FILE__)->end();
