<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义需要用到的变量 **/
$template = Options::get('template');

/** 定义路由结构 **/
$route = array(
    '/'	=> '/var/template/' . $template . 'index.php',
    '/archives/[post_id]' => '/var/template/' . $template . 'post.php',
);
