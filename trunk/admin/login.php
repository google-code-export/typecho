<?php
require_once 'common.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php Typecho::widget('Options')->charset(); ?>" />
    <title><?php _e('登录到%s', Typecho::widget('Options')->title); ?></title>
    <link href="<?php Typecho::widget('Options')->adminUrl('/css/default.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php Typecho::widget('Options')->adminUrl('/css/style.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php Typecho::widget('Options')->adminUrl('/css/hack.css'); ?>" rel="stylesheet" type="text/css" />
    <!--[if IE]><link rel="stylesheet" href="<?php Typecho::widget('Options')->adminUrl('/css/ie.css'); ?>" type="text/css" media="screen, projection"><![endif]-->
    <script src="<?php Typecho::widget('Options')->adminUrl('/js/jquery-1.2.3.pack.js'); ?>" type="text/javascript"></script>
    <script src="<?php Typecho::widget('Options')->adminUrl('/js/add.js'); ?>" type="text/javascript"></script>
</head>

<body style="background: #E6EEF7;">
	<div id="login" class="round clearfix">
		<p style="text-align: center;"><img id="login-logo.png" src="images/login-logo.png" alt="Typecho" /></p>
		<?php if(!Typecho::widget('Access')->hasLogin()): ?>
		<?php require_once 'notice.php'; ?>
		<form method="post" action="<?php Typecho::widget('Options')->index('Login.do'); ?>">
			<p><label for="name"><?php _e('用户名'); ?>:<br /><input type="text" class="text" id="name" name="name" /></label></p>
			<p><label for="password"><?php _e('密码'); ?>:<br /><input type="password" class="password" id="password" name="password" /></label></p>
			<p><label style="font-weight: normal;" for="remember"><input type="checkbox" id="remember" name="remember" value="1" /> <?php _e('记住我'); ?></label></p>
			<p class="left">
			<?php if(Typecho::widget('Options')->allowRegister): ?>
				<a href="<?php Typecho::widget('Options')->adminUrl('register.php'); ?>"><?php _e('注册'); ?></a> | 
			<?php endif; ?>
			<a href="<?php Typecho::widget('Options')->adminUrl('get-password.php'); ?>"><?php _e('忘记密码?'); ?></a><br />
			<a href="<?php Typecho::widget('Options')->siteUrl(); ?>"><?php _e('返回%s &raquo;', Typecho::widget('Options')->title); ?></a></p>
			<p class="right"><input type="submit" class="submit" value="<?php _e('登录'); ?>" />
            <input type="hidden" name="referer" value="<?php echo TypechoRequest::getParameter('referer'); ?>" /></p>
            <script>
                $('input[@name=name]').trigger('focus');
            </script>
		</form>
		<?php else: ?>
		<div class="notice">
			<ul>
			    <li><?php _e('您已经登录到%s', Typecho::widget('Options')->title); ?></li>
			    <li><?php _e('点击下面的链接继续操作'); ?></li>
			</ul>
		</div>
		<p class="left"><a href="<?php Typecho::widget('Options')->adminUrl(); ?>"><?php _e('&laquo; 进入后台'); ?></a> | 
		<a href="<?php Typecho::widget('Options')->siteUrl(); ?>"><?php _e('返回%s &raquo;', Typecho::widget('Options')->title); ?></a></p>
		<?php endif; ?>
	</div>
</body>
</html>
