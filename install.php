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

/** 后台路径(相对路径) */
define('__TYPECHO_ADMIN_DIR__', '/admin/');

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

/** 程序初始化 */
Typecho_Common::init(array(
    'autoLoad'          =>  true,
    'exception'         =>  'Widget_ExceptionHandle',
    'gpc'               =>  true,
    'timezone'          =>  'UTC',
    'session'           =>  false,
    'gzip'              =>  false,
    'charset'           =>  'UTF-8',
    'contentType'       =>  'text/html'
));

define('__TYPECHO_INSTALL_VERSION__', 'Typecho 0.3/8.12.30');

/**
 * 获取传递参数
 * 
 * @param string $name 参数名称
 * @param string $default 默认值
 * @return string
 */
function _v($name, $default = '')
{
    echo isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

/**
 * 判断是否兼容某个环境(perform)
 * 
 * @param string $adapter 适配器
 * @return boolean
 */
function _p($adapter)
{
    switch ($adapter) {
        case 'Mysql':
            return function_exists('mysql_connect');
        case 'Pdo_Mysql':
            return extension_loaded('pdo') && in_array('mysql', PDO::getAvailableDrivers());
        case 'SQLite':
            return function_exists('sqlite_open');
        case 'Pdo_SQLite':
            return extension_loaded('pdo') && in_array('sqlite', PDO::getAvailableDrivers());
        case 'Pgsql':
            return function_exists('pg_connect');
        case 'Pdo_Pgsql':
            return extension_loaded('pdo') && in_array('pgsql', PDO::getAvailableDrivers());
        default:
            return false;
    }
}

/**
 * 获取url地址
 * 
 * @return string
 */
function _u()
{
    $url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];	
    if (isset($_SERVER["QUERY_STRING"])) {
        $url = str_replace("?" . $_SERVER["QUERY_STRING"], "", $url);
    }
    
    return dirname($url);
}

$options = new stdClass();
$options->generator = __TYPECHO_INSTALL_VERSION__;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo Typecho_Common::$config['charset']; ?>" />
	<title><?php _e('Typecho安装程序'); ?></title>
    <link rel="stylesheet" type="text/css" href="admin/css/reset.source.css" />
    <link rel="stylesheet" type="text/css" href="admin/css/grid.source.css" />
    <link rel="stylesheet" type="text/css" href="admin/css/typecho.source.css" />
</head>
<body>
<div class="typecho-install-patch">
    <ol class="path">
        <li<?php if (!isset($_GET['finish']) && !isset($_GET['config'])) : ?> class="current"<?php endif; ?>><?php _e('欢迎使用'); ?></li>
        <li<?php if (isset($_GET['config'])) : ?> class="current"<?php endif; ?>><?php _e('输入您的配置'); ?></li>
        <li<?php if (isset($_GET['finish'])) : ?> class="current"<?php endif; ?>><?php _e('安装成功'); ?></li>
    </ol>
</div>
<div class="main">
    <div class="body body-950">
        <div class="container">
            <div class="column-14 start-06 typecho-install">
            <?php if (isset($_GET['finish'])) : ?>
                <h1 class="typecho-install-title"><?php _e('安装成功!'); ?></h1>
                <div class="typecho-install-body">
                    <div class="session">
                    <p><?php _e('您的用户名是'); ?>:<em><?php echo Typecho_Request::getParameter('user'); ?></em></p>
                    <p><?php _e('您的密码是'); ?>:<em>12345</em></p>
                    </div>

                    <div class="session">
                    <p><?php _e('您可以将下面两个链接保存到您的收藏夹'); ?>:</p>
                    <ul>
                        <li><a href="<?php echo _u(); ?>/admin/index.php"><?php _e('点击这里访问您的控制面板'); ?></a></li>
                        <li><a href="<?php echo _u(); ?>/index.php"><?php _e('点击这里查看您的 Blog'); ?></a></li>
                    </ul>
                    </div>

                    <p><?php _e('希望你能尽情享用 Typecho 带来的乐趣!'); ?></p>
                </div>
            <?php  elseif (isset($_GET['config'])) : ?>
            <?php
                    $adapter = Typecho_Request::getParameter('dbAdapter', 'Mysql');
                    $type = explode('_', $adapter);
                    $type = array_pop($type);
            ?>
                <form method="post" action="?config" name="config">
                    <h1 class="typecho-install-title"><?php _e('确认您的配置'); ?></h1>
                    <div class="typecho-install-body">
                        <h2><?php _e('数据库配置'); ?></h2>
                        <?php
                            if ('config' == Typecho_Request::getParameter('action')) {
                                $success = true;
                                
                                if (NULL == Typecho_Request::getParameter('userUrl')) {
                                    $success = false;
                                    echo '<p class="message error">' . _t('请填写您的网站地址') . '</p>';
                                } else if (NULL == Typecho_Request::getParameter('userName')) {
                                    $success = false;
                                    echo '<p class="message error">' . _t('请填写您的用户名') . '</p>';
                                } else if (NULL == Typecho_Request::getParameter('userMail')) {
                                    $success = false;
                                    echo '<p class="message error">' . _t('请填写您的邮箱地址') . '</p>';
                                } else if (32 < strlen(Typecho_Request::getParameter('userName'))) {
                                    $success = false;
                                    echo '<p class="message error">' . _t('用户名长度超过限制, 请不要超过32个字符') . '</p>';
                                } else if (200 < strlen(Typecho_Request::getParameter('userMail'))) {
                                    $success = false;
                                    echo '<p class="message error">' . _t('邮箱长度超过限制, 请不要超过200个字符') . '</p>';
                                }
                                
                                
                                if($success)
                                {
                                    $installDb = new Typecho_Db ($adapter, Typecho_Request::getParameter('dbPrefix'));
                                    $_dbConfig = Typecho_Request::getParametersFrom('dbHost', 'dbUser', 'dbPassword', 'dbCharset', 'dbPort', 'dbPrefix', 'dbDatabase', 'dbFile', 'dbDsn');

                                    $dbConfig = array();
                                    foreach ($_dbConfig as $key => $val) {
                                        $dbConfig[strtolower (substr($key, 2))] = $val;
                                    }

                                    $installDb->addServer($dbConfig, Typecho_Db::READ | Typecho_Db::WRITE);
                                    
                                    /** 检测数据库配置 */
                                    try {
                                        $installDb->query('SELECT 1=1');
                                    } catch (Typecho_Db_Exception $e) {
                                        $success = false;
                                        echo '<p class="message error">' . _t('安装程序捕捉到以下错误: "%s". 程序被终止, 请检查您的配置信息.',$e->getMessage()) . '</p>';
                                    }
                                }
                                
                                if($success)
                                {
                                    /** 初始化配置文件 */
                                    $lines = array_slice(file(__FILE__), 0, 61);
                                    $lines[] = "
/** 定义数据库参数 */
\$db = new Typecho_Db('{$adapter}', '" . Typecho_Request::getParameter('dbPrefix') . "');
\$db->addServer(" . var_export($dbConfig, true) . ", Typecho_Db::READ | Typecho_Db::WRITE);
Typecho_Db::set(\$db);

/** 初始化全局参数 */
Typecho_Widget::widget('Widget_Options')->to(\$options);

/** 定义路由参数 */
Typecho_Router::setRoutes(\$options->routingTable);

/** 初始化插件 */
Typecho_Plugin::init(\$options->plugins);
";

                                    file_put_contents('./config.inc.php', implode('', $lines));
                                
                                    try {
                                        /** 初始化数据库结构 */
                                        $scripts = file_get_contents ('./install/' . $type . '.sql');
                                        $scripts = str_replace('typecho_', $dbConfig['prefix'], $scripts);
                                        
                                        if (isset($dbConfig['charset'])) {
                                            $scripts = str_replace('%charset%', $dbConfig['charset'], $scripts);
                                        }
                                        
                                        $scripts = explode(';', $scripts);
                                        foreach ($scripts as $script) {
                                            $script = trim($script);
                                            if ($script) {
                                                $installDb->query($script, Typecho_Db::WRITE);
                                            }
                                        }
                                        
                                        /** 全局变量 */
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'theme', 'user' => 0, 'value' => 'default')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'timezone', 'user' => 0, 'value' => _t('28800'))));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'charset', 'user' => 0, 'value' => 'UTF-8')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'generator', 'user' => 0, 'value' => __TYPECHO_INSTALL_VERSION__)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'title', 'user' => 0, 'value' => 'Hello World')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'description', 'user' => 0, 'value' => 'Just So So ...')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'keywords', 'user' => 0, 'value' => 'typecho,php,blog')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'rewrite', 'user' => 0, 'value' => 0)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsRequireMail', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsRequireURL', 'user' => 0, 'value' => 0)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsRequireModeration', 'user' => 0, 'value' => 0)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'plugins', 'user' => 0, 'value' => 'a:0:{}')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentDateFormat', 'user' => 0, 'value' => 'Y-m-d H:i:s')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'siteUrl', 'user' => 0, 'value' => Typecho_Request::getParameter('userUrl'))));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'defaultCategory', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'allowRegister', 'user' => 0, 'value' => 0)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'defaultAllowComment', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'defaultAllowPing', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'defaultAllowFeed', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'pageSize', 'user' => 0, 'value' => 5)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'postsListSize', 'user' => 0, 'value' => 10)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsListSize', 'user' => 0, 'value' => 10)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsHTMLTagAllowed', 'user' => 0, 'value' => NULL)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'postDateFormat', 'user' => 0, 'value' => 'Y-m-d')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'feedFullText', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'editorSize', 'user' => 0, 'value' => 16)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'autoSave', 'user' => 0, 'value' => 0)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsPostTimeout', 'user' => 0, 'value' => 0)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsUrlNofollow', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'commentsShowUrl', 'user' => 0, 'value' => 1)));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'routingTable', 'user' => 0, 'value' => 'a:19:{s:5:"index";a:3:{s:3:"url";s:1:"/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:4:"post";a:3:{s:3:"url";s:24:"/archives/[cid:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:8:"category";a:3:{s:3:"url";s:17:"/category/[slug]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:3:"tag";a:3:{s:3:"url";s:12:"/tag/[slug]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:6:"search";a:3:{s:3:"url";s:19:"/search/[keywords]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:10:"index_page";a:3:{s:3:"url";s:21:"/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:13:"category_page";a:3:{s:3:"url";s:32:"/category/[slug]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:8:"tag_page";a:3:{s:3:"url";s:27:"/tag/[slug]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:11:"search_page";a:3:{s:3:"url";s:34:"/search/[keywords]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:12:"archive_year";a:3:{s:3:"url";s:18:"/[year:digital:4]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:13:"archive_month";a:3:{s:3:"url";s:36:"/[year:digital:4]/[month:digital:2]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:11:"archive_day";a:3:{s:3:"url";s:52:"/[year:digital:4]/[month:digital:2]/[day:digital:2]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:17:"archive_year_page";a:3:{s:3:"url";s:38:"/[year:digital:4]/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:18:"archive_month_page";a:3:{s:3:"url";s:56:"/[year:digital:4]/[month:digital:2]/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:16:"archive_day_page";a:3:{s:3:"url";s:72:"/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:4:"feed";a:3:{s:3:"url";s:20:"/feed[feed:string:0]";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:4:"feed";}s:8:"feedback";a:3:{s:3:"url";s:31:"[permalink:string]/[type:alpha]";s:6:"widget";s:15:"Widget_Feedback";s:6:"action";s:6:"action";}s:2:"do";a:3:{s:3:"url";s:23:"/[widget:alphaslash].do";s:6:"widget";s:9:"Widget_Do";s:6:"action";s:6:"action";}s:4:"page";a:3:{s:3:"url";s:12:"/[slug].html";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}}')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'actionTable', 'user' => 0, 'value' => 'a:0:{}')));
                                        $installDb->query($installDb->insert('table.options')->rows(array('name' => 'panelTable', 'user' => 0, 'value' => 'a:0:{}')));
                                        
                                        /** 初始分类 */
                                        $installDb->query($installDb->insert('table.metas')->rows(array('name' => _t('默认分类'), 'slug' => 'default', 'type' => 'category', 'description' => _t('只是一个默认分类'),
                                        'count' => 1, 'order' => 1)));
                                        
                                        /** 初始关系 */
                                        $installDb->query($installDb->insert('table.relationships')->rows(array('cid' => 1, 'mid' => 1)));
                                        
                                        /** 初始内容 */
                                        $installDb->query($installDb->insert('table.contents')->rows(array('title' => _t('欢迎使用Typecho'), 'slug' => 'start', 'created' => (time() - idate('Z')), 'modified' => (time() - idate('Z')),
                                        'text' => '<p>' . _t('如果您看到这篇文章,表示您的blog已经安装成功.') . '</p>', 'authorId' => 1, 'type' => 'post', 'status' => 'publish', 'commentsNum' => 1, 'allowComment' => 1,
                                        'allowPing' => 1, 'allowFeed' => 1)));
                                        
                                        $installDb->query($installDb->insert('table.contents')->rows(array('title' => _t('关于'), 'slug' => 'start-page', 'created' => (time() - idate('Z')), 'modified' => (time() - idate('Z')),
                                        'text' => '<p>' . _t('本页面由Typecho创建, 这只是个测试页面.') . '</p>', 'authorId' => 1, 'order' => 0, 'type' => 'page', 'status' => 'publish', 'commentsNum' => 0, 'allowComment' => 1,
                                        'allowPing' => 1, 'allowFeed' => 1)));
                                        
                                        /** 初始评论 */
                                        $installDb->query($installDb->insert('table.comments')->rows(array('cid' => 1, 'created' => (time() - idate('Z')), 'author' => 'Typecho', 'ownerId' => 1, 'url' => 'http://typecho.org',
                                        'ip' => '127.0.0.1', 'agent' => __TYPECHO_INSTALL_VERSION__, 'text' => '欢迎加入Typecho大家族', 'type' => 'comment', 'status' => 'approved', 'parent' => 0)));
                                        
                                        /** 初始用户 */
                                        $installDb->query($installDb->insert('table.users')->rows(array('name' => Typecho_Request::getParameter('userName'), 'password' => Typecho_Common::hash('12345'), 'mail' => Typecho_Request::getParameter('userMail'), 
                                        'url' => 'http://www.typecho.org', 'screenName' => Typecho_Request::getParameter('userName'), 'group' => 'administrator', 'created' => (time() - idate('Z')))));
                                        
                                        Typecho_Response::redirect('install.php?finish&user=' . Typecho_Request::getParameter('userName'));
                                    } catch (Typecho_Db_Exception $e) {
                                        echo '<p class="message error">' . _t('安装程序捕捉到以下错误: "%s". 程序被终止, 请检查您的配置信息.',$e->getMessage()) . '</p>';
                                    }
                                }
                            }
                        ?>
                        <ul class="typecho-option">
                            <li>
                            <label class="typecho-label"><?php _e('数据库适配器'); ?></label>
                            <select name="dbAdapter">
                                <?php if (_p('Mysql')): ?><option value="Mysql"<?php if('Mysql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Mysql原生函数适配器') ?></option><?php endif; ?>
                                <?php if (_p('SQLite')): ?><option value="SQLite"<?php if('SQLite' == $adapter): ?> selected=true<?php endif; ?>><?php _e('SQLite原生函数适配器(SQLite 2.x)') ?></option><?php endif; ?>
                                <?php if (_p('Pgsql')): ?><option value="Pgsql"<?php if('Pgsql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pgsql原生函数适配器') ?></option><?php endif; ?>
                                <?php if (_p('Pdo_Mysql')): ?><option value="Pdo_Mysql"<?php if('Pdo_Mysql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pdo驱动Mysql适配器') ?></option><?php endif; ?>
                                <?php if (_p('Pdo_SQLite')): ?><option value="Pdo_SQLite"<?php if('Pdo_SQLite' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pdo驱动SQLite适配器(SQLite 3.x)') ?></option><?php endif; ?>
                                <?php if (_p('Pdo_Pgsql')): ?><option value="Pdo_Pgsql"<?php if('Pdo_Pgsql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pdo驱动PostgreSql适配器') ?></option><?php endif; ?>
                            </select>
                            <p class="desption"><?php _e('请根据你的数据库类型选择合适的适配器'); ?></p>
                            </li>
                            <?php require_once './install/' . $type . '.php'; ?>
                            <li>
                            <label class="typecho-label"><?php _e('数据库前缀'); ?></label>
                            <input type="text" class="text mini" name="dbPrefix" value="<?php _v('dbPrefix', 'typecho_'); ?>" />
                            <p class="desption"><?php _e('默认前缀是 "typecho_"'); ?></p>
                            </li>
                        </ul>
                        
                        <script>
                        var _select = document.config.dbAdapter;
                        _select.onchange = function() {
                            setTimeout("window.location.href = 'install.php?config&dbAdapter=" + this.value + "'; ",0);
                        }
                        </script>
                        
                        <h2><?php _e('创建您的管理员帐号'); ?></h2>
                        <ul class="typecho-option">
                            <li>
                            <label class="typecho-label"><?php _e('网站地址'); ?></label>
                            <input type="text" name="userUrl" class="text" value="<?php _v('userUrl', _u()); ?>" />
                            <p class="desption"><?php _e('这是程序自动匹配的网站路径, 如果不正确请修改它'); ?></p>
                            </li>
                            <li>
                            <label class="typecho-label"><?php _e('用户名'); ?></label>
                            <input type="text" name="userName" class="text" value="<?php _v('userName', 'admin'); ?>" />
                            <p class="desption"><?php _e('请填写您的用户名'); ?></p>
                            </li>
                            <li>
                            <label class="typecho-label"><?php _e('邮件地址'); ?></label>
                            <input type="text" name="userMail" class="text" value="<?php _v('userMail', 'webmaster@yourdomain.com'); ?>" />
                            <p class="desption"><?php _e('请填写一个您的常用邮箱'); ?></p>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="action" value="config" />
                    <p class="submit"><button type="submit"><?php _e('确认, 开始安装 &raquo;'); ?></button></p>
                </form>
            <?php  else: ?>
                <form method="post" action="?config">
                <h1 class="typecho-install-title"><?php _e('欢迎使用Typecho'); ?></h1>
                <div class="typecho-install-body">
                <h2><?php _e('安装说明'); ?></h2>
                <p><strong><?php _e('本安装程序将自动检测服务器环境是否符合最低配置需求.如果不符合,将在上方出现提示信息,
请按照提示信息检查你的主机配置.如果服务器环境符合要求,将在下方出现"同意并安装"的按钮,点击此按钮即可一步完成安装.'); ?></strong></p>
                <h2><?php _e('许可及协议'); ?></h2>
                <p><?php _e('Typecho基于GPL协议发布,我们允许用户在GPL协议许可的范围内使用,拷贝,修改和分发此程序.
你可以自由地将其用于商业以及非商业用途.'); ?></p>
                <p><?php _e('Typecho软件由其社区提供支持,核心开发团队负责维护程序日常开发工作以及新特性的制定.如果你遇到使用上的问题,
程序中的BUG,以及期许的新功能,欢迎你在社区中交流或者直接向我们贡献代码.对于贡献突出者,他的名字将出现在贡献者名单中.'); ?></p>
                <h2><?php _e('此版本贡献者(排名不分先后)'); ?></h2>
                <ol>

                </ol>
                <p><a href="http://typecho.org"><?php _e('查看所有贡献者'); ?></a></p>
                </div>
                <p class="submit"><button type="submit"><?php _e('我准备好了, 开始下一步 &raquo;'); ?></button></p>
                </form>
            <?php endif; ?>
            
            </div>
        </div>
    </div>
</div>
<?php include 'admin/copyright.php'; ?>
