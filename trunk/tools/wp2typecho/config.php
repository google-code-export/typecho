<?php
/**
 *�������ݿ� 
 */
$res = mysql_connect("localhost", "root", "123456"); //���������ݿ�����������ݿ��û���������
mysql_select_db("typecho"); //���ݿ���
$tablepre = 'wp_'; //wordpress��ǰ׺
$typechoPre = 'typecho_'; //typecho��ǰ׺
mysql_query('SET NAMES utf8'); //����Ϊutf8