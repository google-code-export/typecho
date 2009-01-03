<?php
/**
 *wordpress转typecho文章数据转换(contents)程序
 */
/** 载入配置支持 */
require_once 'config.php';
$articleQuery = mysql_query("SELECT * FROM {$tablepre}posts");
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
    if($articleInfo['post_type'] == 'revision') {
        $type = 'post';
    }
	$status = $articleInfo['post_status'];
	if($articleInfo['post_type'] == 'inherit') {
        $type = 'publish';
    }
    $password = $articleInfo['post_password'];
    $commentsNum = $articleInfo['comment_count'];
    $allowComment = $articleInfo['comment_status'];
	if($articleInfo['comment_status'] == 'open' || $articleInfo['comment_status'] == 'enable') {
        $allowComment = '1';
	}else{
		$allowComment = '0';
	}
    $allowPing = $articleInfo['ping_status'] == 'open' ? 'enable' : 'disable';
	if($articleInfo['ping_status'] == 'open' || $articleInfo['ping_status'] == 'enable') {
        $allowPing = '1';
	}else{
		$allowPing = '0';
	}
    $allowFeed = '1';
    mysql_query("INSERT INTO {$typechoPre}contents VALUES('$cid', '$title', '$slug', '$created', '$modified', '$text', '$meta', '$author', '$template', '$type', '$status', '$password', '$commentsNum', '$allowComment', '$allowPing', '$allowFeed')");
}
