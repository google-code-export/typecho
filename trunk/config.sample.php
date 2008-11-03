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

/** 定义插件目录(相对路径) */
define('__TYPECHO_PLUGIN_DIR__', '/usr/plugins');

/** 定义模板目录(相对路径) */
define('__TYPECHO_THEME_DIR__', '/usr/themes');

/** 附件存储目录(相对路径) */
define('__TYPECHO_ATTACHMENT_DIR__', '/usr/attachment');

/** 后台路径(相对路径) */
define('__TYPECHO_ADMIN_DIR__', '/admin/');

/** 定义调试开关 **/
define('__TYPECHO_DEBUG__', true);

/** 定义网页输出编码 **/
define('__TYPECHO_CHARSET__', 'UTF-8');

/** 定义gzip支持 **/
define('__TYPECHO_GZIP_ENABLE__', false);

/** 设置包含路径 */
@set_include_path(get_include_path() . PATH_SEPARATOR . 
__TYPECHO_ROOT_DIR__ . '/var' . PATH_SEPARATOR . 
__TYPECHO_ROOT_DIR__ . __TYPECHO_PLUGIN_DIR__);

/** 载入API支持 */
require_once 'Typecho/Common.php';

/** 载入Response支持 */
require_once 'Typecho/Response.php';

/** 载入配置支持 */
require_once 'Typecho/Config.php';

/** 载入异常支持 */
require_once 'Typecho/Exception.php';

/** 载入插件支持 */
require_once 'Typecho/Plugin.php';

/** 载入国际化支持 */
require_once 'Typecho/I18n.php';

/** 载入数据库支持 */
require_once 'Typecho/Db.php';

/** 载入路由器支持 */
require_once 'Typecho/Router.php';

/** 定义数据库参数 */
$db = new Typecho_Db('Mysql', 'typecho_');
$db->addServer(array(
    'host'          =>  'localhost',
    'port'          =>  '3306',
    'user'          =>  'root',
    'password'      =>  '',
    'database'      =>  'typecho',
    'charset'       =>  'utf8'
), Typecho_Db::READ | Typecho_Db::WRITE);
Typecho_Db::set($db);

/** 自定义错误页面 */
if(!__TYPECHO_DEBUG__)
{
    Typecho_Exception::setHandles(array(
        0              =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        403            =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        404            =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        500            =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        501            =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php',
        503            =>  __TYPECHO_ROOT_DIR__ . '/admin/error.php'
    ));
}

/** 定义路由参数 */
Typecho_Router::setRoutes(array(
    'index'             =>  array('url' => '/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'post'              =>  array('url' => '/archives/[cid:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'category'          =>  array('url' => '/category/[slug]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'tag'               =>  array('url' => '/tag/[slug]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'search'            =>  array('url' => '/search/[keywords]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'index_page'        =>  array('url' => '/page/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'category_page'     =>  array('url' => '/category/[slug]/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'tag_page'          =>  array('url' => '/tag/[slug]/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'search_page'       =>  array('url' => '/search/[keywords]/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'archive_year'      =>  array('url' => '/[year:digital:4]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'archive_month'     =>  array('url' => '/[year:digital:4]/[month:digital:2]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'archive_day'       =>  array('url' => '/[year:digital:4]/[month:digital:2]/[day:digital:2]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'archive_year_page' =>  array('url' => '/[year:digital:4]/page/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'archive_month_page'=>  array('url' => '/[year:digital:4]/[month:digital:2]/page/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'archive_day_page'  =>  array('url' => '/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/', 'widget' => 'Widget_Archive', 'action' => 'render'),
    'feed'              =>  array('url' => '/feed[feed:string:0]', 'widget' => 'Widget_Feed', 'action' => 'render'),
    'feedback'          =>  array('url' => '[permalink:string]/[type:alpha]', 'widget' => 'Widget_Feedback', 'action' => 'action'),
    'do'                =>  array('url' => '/[widget:alphaslash].do', 'widget' => 'Widget_Do', 'action' => 'action'),
    'plugin'            =>  array('url' => '/[plugin:alphaslash].plugin', 'widget' => 'Widget_Do', 'action' => 'action'),
    'page'              =>  array('url' => '/[slug].html', 'widget' => 'Widget_Archive', 'action' => 'render'),
));

/** 注册自动加载函数 */
Typecho_Common::registerAutoLoad();

/** 关闭魔术引号 */
Typecho_Common::forceDisableMagicQuotesGPC();

/** 设置默认时区 */
Typecho_Common::setDefaultTimezone();

/** 开始监视缓冲区 */
Typecho_Response::obStart(__TYPECHO_GZIP_ENABLE__);

/** 设置编码类型 */
Typecho_Response::setDefaultCharset(__TYPECHO_CHARSET__);

/** 设置输出类型 */
Typecho_Response::setContentType('text/html');

/** 初始化插件 */
Typecho_Plugin::init(Typecho_Widget::widget('Widget_Options')->plugins);

/** 设置插件参数获取代理 */
Typecho_Plugin::setOptionCallback(array(Typecho_Widget::widget('Widget_Options'), 'getPluginOption'));
