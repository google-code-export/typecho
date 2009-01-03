<?php
/**
 *wordpress转typecho用户数据转换(users)程序
 */

/** 载入配置支持 */
require_once 'config.php';
$userQuery = mysql_query("SELECT * FROM {$tablepre}users");
while($userInfo = mysql_fetch_array($userQuery)) {
    $uid = $userInfo['ID'];
    $name = $userInfo['user_login'];
    /**
     * 此用密码转换暂时空缺
     */
    $password = $userInfo[''];
    $mail = $userInfo['user_email'] ;
    $url = $userInfo['user_url'];
    $screenName = $userInfo['display_name'];
    $created = strtotime($userInfo['user_registered']);
    $activated = $created;
    $logged = $created;
/**
 * 获取用户组信息
 */
    $userMeta = mysql_fetch_array(mysql_query("SELECT meta_value FROM {$tablepre}usermeta WHERE meta_key='wp_capabilities' AND user_id = $userInfo[ID]"));
    if($userMeta['meta_value']) {
        $metaArray = unserialize($userMeta['meta_value']);
        foreach($metaArray as $key => $value) {
            $group = $key;
        }
    } else {
        $group = 'subscriber';
    }
    $authCode = '';
    mysql_query("INSERT INTO {$typechoPre}users VALUES('$uid', '$name', '$password', '$mail', '$url',
        '$screenName', '$created', '$activated', '$logged', '$group', '$authCode')");
}
?>



