<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义根目录 */
define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));

/** 设置包含路径 */
set_include_path(__TYPECHO_ROOT_DIR__);

/** 定义插件目录(相对路径) */
define('__TYPECHO_PLUGIN_DIR__', '/Plugin');

/** 定义模板目录(相对路径) */
define('__TYPECHO_THEME_DIR__', '/var/theme');

/** 附件存储目录(相对路径) */
define('__TYPECHO_ATTACHMENT_DIR__', '/var/attachment');

/** 定义调试开关 **/
define('__TYPECHO_DEBUG__', true);

/** 定义网页输出编码 **/
define('__TYPECHO_CHARSET__', 'UTF-8');

/** 定义gzip支持 **/
define('__TYPECHO_GZIP_ENABLE__', false);

/** 载入API支持 */
require_once 'Typecho/API.php';

/** 载入API支持 */
require_once 'Typecho/Config.php';

/** 定义数据库参数 */
Typecho_Config::set('Db', array(
    'host'          =>  'localhost',
    'port'          =>  '3306',
    'user'          =>  'root',
    'password'      =>  '',
    'database'      =>  'typecho',
    'prefix'        =>  'typecho_',
    'charset'       =>  'utf8',
    'adapter'       =>  'Mysql'
));

/** 定义语言项 */
Typecho_Config::set('I18n', NULL);

/** 自定义错误页面 */
if(!__TYPECHO_DEBUG__)
{
    Typecho_Config::set('Exception', array(
        '_403'          =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        '_404'          =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        '_500'          =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        '_501'          =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        '_503'          =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        '_error'        =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
    ));
}

/** 定义路由参数 */
Typecho_Config::set('Router', array(
    'index'             =>  array('[/]?', 'Widget_Archive', NULL, '/'),
    'post'              =>  array('/archives/([0-9]+)[/]?', 'Widget_Archive', array('cid'), '/archives/%s/'),
    'category'          =>  array('/category/([^/]+)[/]?', 'Widget_Archive', array('slug'), '/category/%s/'),
    'tag'               =>  array('/tag/([^/]+)[/]?', 'Widget_Archive', array('slug'), '/tag/%s/'),
    'index_page'        =>  array('/page/([0-9]+)[/]?', 'Widget_Archive', array('page'), '/page/%s/'),
    'category_page'     =>  array('/category/([^/]+)/([0-9]+)[/]?', 'Widget_Archive', array('slug', 'page'), '/category/%s/%s/'),
    'tag_page'          =>  array('/tag/([^/]+)/([0-9]+)[/]?', 'Widget_Archive', array('slug', 'page'), '/tag/%s/%s/'),
    'archive_year'      =>  array('/([0-9]{4})[/]?', 'Widget_Archive', array('year'), '/%s/'),
    'archive_month'     =>  array('/([0-9]{4})/([0-9]{1,2})[/]?', 'Widget_Archive', array('year', 'month'), '/%s/%s/'),
    'archive_day'       =>  array('/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})[/]?', 'Widget_Archive', array('year', 'month', 'day'), '/%s/%s/%s/'),
    'archive_year_page' =>  array('/([0-9]{4})/page/([0-9]+)[/]?', 'Widget_Archive', array('year', 'page'), '/%s/'),
    'archive_month_page'=>  array('/([0-9]{4})/([0-9]{1,2})/page/([0-9]+)[/]?', 'Widget_Archive', array('year', 'month', 'page'), '/%s/%s/'),
    'archive_day_page'  =>  array('/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/([0-9]+)[/]?', 'Widget_Archive', array('year', 'month', 'day', 'page'), '/%s/%s/%s/'),
    'feed'              =>  array('/feed(.*)', 'Widget_Feed', array('feed'), '/feed%s'),
    'feedback'          =>  array('(.*)/([_0-9a-zA-Z-]+)[/]?', 'Widget_Feedback', array('permalink', 'type'), '%s/%s'),
    'do'                =>  array('/([_0-9a-zA-Z-\/]+)\.do', 'Widget_Do', array('do'), '/%s.do'),
    'plugin'            =>  array('/([_0-9a-zA-Z-\/]+)\.plugin', 'Widget_Do', array('plugin'), '/%s.plugin'),
    'page'              =>  array('/([_0-9a-zA-Z-]+)[/]?', 'Widget_Archive', array('slug'), '/%s/'),
));

/** 注册自动加载函数 */
Typecho_API::registerAutoLoad();

/** 关闭魔术引号 */
Typecho_API::forceDisableMagicQuotesGPC();

/** 开始监视缓冲区 */
Typecho_API::obStart(__TYPECHO_GZIP_ENABLE__);

/** 设置默认时区 */
Typecho_API::setDefaultTimezone();

/** 设置输出类型 */
Typecho_API::setContentType();
