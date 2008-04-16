<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义数据库参数 **/
define('__TYPECHO_DB_HOST__', 'localhost');
define('__TYPECHO_DB_PORT__', '3306');
define('__TYPECHO_DB_USER__', 'root');
define('__TYPECHO_DB_PASS__', '');
define('__TYPECHO_DB_NAME__', 'typecho');
define('__TYPECHO_DB_PREFIX__', 'typecho_');
define('__TYPECHO_DB_CHAR__', 'utf8');
define('__TYPECHO_DB_ADAPTER__', 'Mysql');

/** 定义调试开关 **/
define('__TYPECHO_DEBUG__', true);

/** 定义mo文件 **/
define('__TYPECHO_I18N_LANGUAGE__', false);

/** 定义gzip支持 **/
define('__TYPECHO_GZIP_ENABLE__', false);

/** 定义路由结构 **/
global $route;

$route = array(
    'index'         =>  array('/', 'index.php', NULL, NULL, array('contents.Posts')),
    'index_page'    =>  array('/page/([0-9]+)[/]?', 'index.php', array('page'), '/page/%s', array('contents.Posts')),
    'post'          =>  array('/archives/([0-9]+)[/]?', 'post.php', array('cid'), '/archives/%s', array('contents.Post')),
    'rss'           =>  array('/feed[/]?', '../../xml/rss.php', NULL, '/rss', NULL),
    'do'            =>  array('/([_0-9a-zA-Z-]+)\.do', array('DoWidget'), array('do'), '/%s.do'),
    'job'           =>  array('/([_0-9a-zA-Z-]+)\.job', array('JobWidget'), array('job'), '/%s.job'),
    'login'         =>  array('/login[/]?', 'admin/login.php'),
    'admin'         =>  array('/admin[/]?', 'admin/index.php'),
    'page'          =>  array('/([_0-9a-zA-Z-]+)[/]?', 'page.php', array('slug'), '/%s', array('contents.Post')),
);
