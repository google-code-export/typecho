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

/** 开始路由分发 */
Typecho_Router::dispatch();
