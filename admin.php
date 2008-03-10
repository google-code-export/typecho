<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入项目配置文件 **/
require 'includes/config.php';

widget('Options')->to($options);
widget('Access')->to($access);

require TypechoRoute::handle('./var/admin', 'mod', 'index', array('header', 'menu', 'footer', 'do'));
