<?php
require_once 'common.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php Typecho::widget('Options')->charset(); ?>" />
    <title><?php _e('获取密码'); ?></title>
    <link href="<?php Typecho::widget('Options')->adminURL('/css/default.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php Typecho::widget('Options')->adminURL('/css/style.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php Typecho::widget('Options')->adminURL('/css/hack.css'); ?>" rel="stylesheet" type="text/css" />
    <!--[if IE]><link rel="stylesheet" href="<?php Typecho::widget('Options')->adminURL('/css/ie.css'); ?>" type="text/css" media="screen, projection"><![endif]-->
    <script src="<?php Typecho::widget('Options')->adminURL('/js/jquery-1.2.3.pack.js'); ?>" type="text/javascript"></script>
    <script src="<?php Typecho::widget('Options')->adminURL('/js/add.js'); ?>" type="text/javascript"></script>
</head>

<body style="background: #E6EEF7;">
	<div id="login" class="round clearfix">
		<p style="text-align: center;"><img id="login-logo.png" src="images/login-logo.png" alt="Typecho" /></p>
		<?php if(!Typecho::widget('Access')->hasLogin()): ?>
		<div class="notice"><?php _e('请在下方的输入框中输入您的电子邮件地址,您将在电子邮箱中收到重置以后的密码.'); ?></div>
		<form method="post" action="">
			<p><label><?php _e('您的电子邮件地址'); ?>:</label><br /><input type="text" id="" /></p>
			<p class="left"><a href="<?php Typecho::widget('Options')->adminURL('login.php'); ?>"><?php _e('返回登录页 &raquo;'); ?></a></p>
			<p class="right"><input type="submit" value="<?php _e('重置我的密码'); ?>" /></p>
		</form>
		<?php else: ?>
		<div class="notice">
			<ul>
			    <li><?php _e('您已经登录到%s', Typecho::widget('Options')->title); ?></li>
			    <li><?php _e('点击下面的链接继续操作'); ?></li>
			</ul>
		</div>
		<p class="left"><a href="<?php Typecho::widget('Options')->adminURL(); ?>"><?php _e('&laquo; 进入后台'); ?></a> | 
		<a href="<?php Typecho::widget('Options')->siteURL(); ?>"><?php _e('返回%s &raquo;', Typecho::widget('Options')->title); ?></a></p>
		<?php endif; ?>
	</div>
</body>
</html>
