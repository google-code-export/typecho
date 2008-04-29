<?php
require_once 'common.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php _e('登录到%s', widget('Options')->title); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="css/default.css" rel="stylesheet" type="text/css" />
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="css/hack.css" rel="stylesheet" type="text/css" />
	<!--[if IE]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen, projection"><![endif]-->
	<script src="js/jquery-1.2.3.pack.js" type="text/javascript"></script>
	<script src="js/add.js" type="text/javascript"></script>
	<script src="js/jquery.curvycorners.packed.js" type="text/javascript"></script>
 </head>

<body style="background: #E6EEF7;">
	<div id="login" class="round">
		<p style="text-align: center;"><img id="login-logo.png" src="images/login-logo.png" alt="" /></p>
		<?php if(widget('Notice')->have()): ?>
		<div class="error">
		<ul>
			<?php widget('Notice')->lists(); ?>
		</ul>
		</div>
		<?php endif; ?>
		<form method="post" action="<?php widget('Options')->index('Login.do'); ?>">
			<p><label for="name"><?php _e('用户名'); ?>:<br /><input type="text" id="name" name="name" /></label></p>
			<p><label for="password"><?php _e('密码'); ?>:<br /><input type="password" id="password" name="password" /></label></p>
			<p><label style="font-weight: normal; vertical-align: middle;" for="remember"><input type="checkbox" id="remember" name="remember" value="1" /> <?php _e('记住我'); ?></label></p>
			<p class="left"><a href="<?php widget('Options')->adminURL('get-password.php'); ?>"><?php _e('忘记密码?'); ?></a><br />
			<a href="<?php widget('Options')->siteURL(); ?>"><?php _e('返回%s &raquo;', widget('Options')->title); ?></a></p>
			<p class="right"><input type="submit" value="<?php _e('登录'); ?>" /></p>
		</form>
		<div class="clear"></div>
	</div>
</body>
</html>
