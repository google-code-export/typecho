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

widget('Options')->to($options);    //初始化配置组件
widget('Access')->to($access);      //初始化权限组件

require TypechoRoute::target('./var/template/' . $options->template);
