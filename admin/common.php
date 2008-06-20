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

/** 注册一个初始化插件 */
_p(__FILE__, 'Action')->init();

$options = Typecho_API::factory('Widget_Abstract_Options');
$access = Typecho_API::factory('Widget_Users_Current');
$notice = Typecho_API::factory('Widget_Notice');
