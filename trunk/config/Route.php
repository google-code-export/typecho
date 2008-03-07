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
    'post'          =>  array('/archives/([0-9]+)[/]?', 'article.php', array('cid'), '/archives/%d'),
    'page'          =>  array('/([_0-9a-zA-Z-]+)[/]?', 'page.php', array('slug'), '/%s'),
    'category'      =>  array('/category/([_0-9a-zA-Z-]+)[/]?', 'archive.php', array('slug'), '/category/%s'),
    'category_page' =>  array('/category/([_0-9a-zA-Z-]+)/([0-9]+)[/]?', 'archive.php', array('slug', 'page'), '/category/%s/%d'),
);
