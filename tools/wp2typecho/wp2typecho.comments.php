<?php
/**
 *wordpress转typecho评论数据转换(comments)程序  
 */
/** 载入配置支持 */
require_once 'config.php';
$commentQuery = mysql_query("SELECT * FROM {$tablepre}comments");
while($commentArray = mysql_fetch_array($commentQuery)) {
    $coid = $commentArray['comment_ID'];
    $cid = $commentArray['comment_post_ID'];
    $created = $commentArray['comment_date_gmt'];
    $author = $commentArray['comment_author'];
	$authorId = null;
	$ownerId = $commentArray['comment_useid'];
    $mail = $commentArray['comment_author_mail'];
    $url = $commentArray['comment_author_url'];
    $ip = $commentArray['comment_author_IP'];
    $agent = $commentArray['comment_agent'];
    $text = $commentArray['comment_content'];
    if($commentArray['comment_type'] == 'pingback') {
        $type = 'pingback';
    } elseif($commentArray['comment_type'] == 'trackback') {
        $type = 'trackback';
    } else {
        $type = 'comment';
    }
    if($commentArray['comment_approved'] == 1) {
        $status = 'approved';
    } elseif($commentArray['comment_approved'] == 0) {
        $status = 'waiting';
    } else {
        $status = 'spam';
    }
    $parent = $commentArray['comment_parent'];
    mysql_query("INSERT INTO {$typechoPre}comments VALUES('$coid', '$cid', '$created', '$author', '$authorId', '$ownerId', '$mail', '$url', '$ip', '$agent', '$text', '$type', '$status', '$parent')");
}

