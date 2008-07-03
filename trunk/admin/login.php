<?php
require_once 'common.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php $options->charset(); ?>" />
    <title><?php _e('登录到%s', $options->title); ?></title>
    <link href="<?php $options->adminUrl('/css/default.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $options->adminUrl('/css/style.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $options->adminUrl('/css/hack.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $options->adminUrl('/css/fonts-min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $options->adminUrl('/css/button.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $options->adminUrl('/css/menu.css'); ?>" rel="stylesheet" type="text/css" />
    <!--[if IE]><link rel="stylesheet" href="<?php $options->adminUrl('/css/ie.css'); ?>" type="text/css" media="screen, projection"><![endif]-->
    <script src="<?php $options->adminUrl('/js/jquery-1.2.3.pack.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/jquery.curvycorners.packed.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/yahoo-dom-event.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/container_core-min.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/menu-min.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/element-beta-min.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/button-min.js'); ?>" type="text/javascript"></script>
    <script src="<?php $options->adminUrl('/js/typecho.js'); ?>" type="text/javascript"></script>
</head>

<body class="yui-skin-sam" style="background: #E6EEF7;">
	<div id="login" class="round clearfix">
		<p style="text-align: center;"><img id="login-logo.png" src="images/login-logo.png" alt="Typecho" /></p>
		<?php if(!$access->hasLogin()): ?>
		<?php require_once 'notice.php'; ?>
		<form method="post" action="<?php $options->index('Login.do'); ?>">
			<p><label for="name"><?php _e('用户名'); ?>:<br /><input type="text" class="text" id="name" name="name" /></label></p>
			<p><label for="password"><?php _e('密码'); ?>:<br /><input type="password" class="password" id="password" name="password" /></label></p>
			<p><label style="font-weight: normal;" for="remember"><input type="checkbox" id="remember" name="remember" value="1" /> <?php _e('记住我'); ?></label></p>
			<p class="left">
			<?php if($options->allowRegister): ?>
				<a href="<?php $options->adminUrl('register.php'); ?>"><?php _e('注册'); ?></a> | 
			<?php endif; ?>
			<a href="<?php $options->adminUrl('get-password.php'); ?>"><?php _e('忘记密码?'); ?></a><br />
			<a href="<?php $options->siteUrl(); ?>"><?php _e('返回%s &raquo;', $options->title); ?></a></p>
			<p class="right"><input type="submit" class="submit" value="<?php _e('登录'); ?>" />
            <input type="hidden" name="referer" value="<?php echo Typecho_Request::getParameter('referer'); ?>" /></p>
            <script>
                document.getElementById("name").focus();
            </script>
		</form>
		<?php else: ?>
		<div class="notice">
			<ul>
			    <li><?php _e('您已经登录到%s', $options->title); ?></li>
			    <li><?php _e('点击下面的链接继续操作'); ?></li>
			</ul>
		</div>
		<p class="left"><a href="<?php $options->adminUrl(); ?>"><?php _e('&laquo; 进入后台'); ?></a> | 
		<a href="<?php $options->siteUrl(); ?>"><?php _e('返回%s &raquo;', $options->title); ?></a></p>
		<?php endif; ?>
	</div>
</body>
</html>
