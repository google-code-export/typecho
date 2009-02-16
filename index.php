<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 如果配置文件不存在,启动安装进程 */
if (!is_file('./config.inc.php') && is_file('./install.php')) {
    header('Location: install.php');
}

/** 载入配置支持 */
require_once 'config.inc.php';

/** 注册一个初始化插件 */
Typecho_Plugin::factory('index.php')->begin();

/** 开始路由分发 */
Typecho_Router::dispatch();

/** 注册一个结束插件 */
Typecho_Plugin::factory('index.php')->end();
