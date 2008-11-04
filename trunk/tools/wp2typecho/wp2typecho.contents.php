<?php
/**
 *wordpress转typecho文章数据转换(contents)程序  
 */

$res = mysql_connect("localhost", "root", "123456");
mysql_select_db("program_wordpress");
$tablepre = 'wp_';
$typechoPre = 'typecho_';
mysql_query('SET NAMES utf8');
$articleQuery = mysql_query("SELECT * FROM {$tablepre}posts WHERE post_status != 'inherit'");
while($articleInfo = mysql_fetch_array($articleQuery)) {
    $cid = $articleInfo['ID'];
    $title = addslashes($articleInfo['post_title']);
    $slug = addslashes($articleInfo['post_name']);
    $uri = NULL;
    $created = strtotime($articleInfo['post_date_gmt']);
    $modified = strtotime($articleInfo['post_modified_gmt']);
    $text =  addslashes($articleInfo['post_content']);
/**
 * 预留字段
 */

    $meta = $cid;
    $author =  $articleInfo['post_author'];
    $template = NULL;

    $type = $articleInfo['post_type'];
    if($articleInfo['post_status'] == 'draft' || $articleInfo['post_status'] == 'pending') {
        $type = 'draft';
    }
    $password = $articleInfo['post_password'];
    $commentsNum = $articleInfo['comment_count'];
    $allowComment = $articleInfo['comment_status'] == 'open' ? 'enable' : 'disable';
    $allowPing = $articleInfo['ping_status'] == 'open' ? 'enable' : 'disable';
    $allowFeed = 'enable';
    mysql_query("INSERT INTO {$typechoPre}contents VALUES('$cid', '$title', '$slug', '$uri', '$created', '$modified', '$text', '$meta', '$author', '$template', '$type', '$password', '$commentsNum', '$allowComment', '$allowPing', '$allowFeed')");
}
