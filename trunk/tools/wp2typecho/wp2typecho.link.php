<?php
/**
 *wordpress转typecho链接数据转换(links)程序  
 *这个要在meta转换完之后再转
 */

/** 载入配置支持 */
require_once 'config.php';
$linkQuery = mysql_query("SELECT * FROM {$tablepre}links");
while($linkInfo = mysql_fetch_array($linkQuery)) {
    $name = addslashes($linkInfo['link_name']);
    $slug = $linkInfo['link_url'];
    $type = 'link';
    $description = $linkInfo['link_description'] ;
    $count = 0;
    $sort = $linkInfo['link_rating'];
    
    mysql_query("INSERT INTO {$typechoPre}metas VALUES(NULL, '$name', '$slug', '$type', '$description', '$count', '$sort')");
   echo  mysql_error();

}
