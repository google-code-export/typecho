<?php
/**
 * 后台头部
 * 
 * @author qining
 * @category typecho
 * @package admin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入配置支持 */
require_once '../config.php';

/** 系统启动 */
Typecho::start(__TYPECHO_CHARSET__);

/** 载入插件 */
TypechoPlugin::init(widget('Options')->plugins('admin'));
