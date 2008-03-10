<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义路由结构 **/
global $route;

$route = array(
    'index'         =>  array('/', 'index.php', array()),
    'index_page'    =>  array('/page/([0-9]+)[/]?', 'index.php', array('page'), '/page/%d'),
    'post'          =>  array('/archives/([0-9]+)[/]?', 'article.php', array('cid'), '/archives/%d'),
    'category'      =>  array('/category/([_0-9a-zA-Z-]+)[/]?', 'archive.php', array('slug'), '/category/%s'),
    'category_page' =>  array('/category/([_0-9a-zA-Z-]+)/([0-9]+)[/]?', 'archive.php', array('slug', 'page'), '/category/%s/%d'),
    'rss'           =>  array('/feed[/]?', '../../xml/rss.php', array(), '/feed'),
    'post_rss'      =>  array('/archives/([0-9]+)/feed[/]?', '../../xml/post_rss.php', array('cid'), '/archives/%d/feed'),
    'category_rss'  =>  array('/category/([_0-9a-zA-Z-]+)/feed[/]?', '../../xml/category_rss.php', array('slug'), '/category/%s/feed'),
    'page'          =>  array('/([_0-9a-zA-Z-]+)[/]?', 'page.php', array('slug'), '/%s'),
    'do'            =>  array('/([_0-9a-zA-Z-]+)\.do?', '../../admin/do.php', array('do'), '/%s.do'),
);
