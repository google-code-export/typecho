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

/** 注册一个初始化插件 */
_p('index.php', 'Action')->begin();

/** 开始路由分发 */
Typecho_Router::dispatch();

/** 注册一个结束插件 */
_p('index.php', 'Action')->end();
