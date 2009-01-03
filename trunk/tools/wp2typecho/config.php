<?php
/**
 *设置数据库 
 */
$res = mysql_connect("localhost", "root", "123456"); //依次是数据库服务器，数据库用户名，密码
mysql_select_db("typecho"); //数据库名
$tablepre = 'wp_'; //wordpress表前缀
$typechoPre = 'typecho_'; //typecho表前缀
mysql_query('SET NAMES utf8'); //设置为utf8