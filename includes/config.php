<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入函数库支持 **/
require_once 'functions.php';

//关闭魔术引号功能
if(get_magic_quotes_gpc())
{
    $_GET = typechoStripslashesDeep($_GET);
    $_POST = typechoStripslashesDeep($_POST);
    $_COOKIE = typechoStripslashesDeep($_COOKIE);

    reset($_GET);
    reset($_POST);
    reset($_COOKIE);
}

//开始监视输出区
ob_start();

//设置默认时区
if(!ini_get("date.timezone") && function_exists("date_default_timezone_set"))
{
    @date_default_timezone_set('UTC');
}

/** 定义调试开关 **/
define('__TYPECHO_DEBUG__', true);

/** 定义库目录 **/
define('__TYPECHO_LIB_DIR__', dirname(__FILE__) . '/library');

/** 载入异常支持 **/
require_once 'library/Exception.php';

/** 载入组件支持 **/
require_once 'library/Widget.php';

/** 载入数据库配置 **/
require_once './config/Db.php';

/** 载入数据库支持 **/
require_once 'library/Db.php';

/** 载入路由配置 **/
require_once './config/Route.php';

/** 载入路由支持 **/
require_once 'library/Route.php';

/** 载入国际化配置 **/
require_once './config/I18n.php';

/** 载入国际化支持 **/
require_once 'library/I18n.php';
