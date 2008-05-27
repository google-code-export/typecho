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

/** 定义绝对根目录 */
if(!defined('__DIR__'))
{
    /** 兼容PHP 5.3 */
    define('__DIR__', dirname(__FILE__));
}

/** 载入配置文件 */
require_once file_exists(__DIR__ . '/../config.admin.php') ?
__DIR__ . '/../config.admin.php' : __DIR__ . '/../config.inc.php';

/** 系统启动 */
Typecho::start(__TYPECHO_CHARSET__);

/** 载入插件 */
TypechoPlugin::init(Typecho::widget('Options')->plugins('admin'));
