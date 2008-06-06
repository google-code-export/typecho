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

/** 系统启动 */
Typecho::start();

/** 载入插件 */
TypechoPlugin::init();

/** 注册一个初始化插件 */
TypechoPlugin::instance(__FILE__)->admin();
