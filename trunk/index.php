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
require_once 'config.inc.php';

/** 系统启动 */
Typecho::start();

/** 载入插件 */
TypechoPlugin::init(Typecho::widget('Options')->plugins('index'));

/** 载入页面 */
TypechoRoute::target(Typecho::widget('Options')->templateDirectory . '/' . Typecho::widget('Options')->template);
