<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入配置支持 */
require 'config.php';

/** 载入项目配置文件 */
require 'includes/config.php';

/** 载入插件 */
TypechoPlugin::init(widget('Options')->plugins('index'));

/** 载入页面 */
TypechoRoute::target('./var/template/' . widget('Options')->template);
