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
    'index'         =>  array('/', 'index.php'),
    'article'       =>  array('/archives/[cid]', 'article.php'),
    'page'          =>  array('/[slug]', 'page.php'),
    'category'      =>  array('/category/[slug]', 'archive.php'),
    'category_page' =>  array('/category/[slug]/[page]', 'archive.php'),
    'tag'           =>  array('/tags/[tag_name]', 'archive.php')
);
