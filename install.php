<?php
define('__TYPECHO_INSTALL_VERSION__', 'Typecho Developer Preview');


/** 载入配置文件 */
if(file_exists('config.inc.php'))
{
    require_once 'config.inc.php';
}
else
{
    require_once 'config.sample.php';
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php _e('Typecho安装程序'); ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo __TYPECHO_CHARSET__; ?>" />
	<link href="admin/css/default.css" rel="stylesheet" type="text/css" />
	<link href="admin/css/style.css" rel="stylesheet" type="text/css" />
	<link href="admin/css/hack.css" rel="stylesheet" type="text/css" />
	<!--[if IE]><link rel="stylesheet" href="admin/css/ie.css" type="text/css" media="screen, projection"><![endif]-->
	<script src="admin/js/jquery-1.2.3.pack.js" type="text/javascript"></script>
	<script src="admin/js/add.js" type="text/javascript"></script>
	<script src="admin/js/jquery.curvycorners.packed.js" type="text/javascript"></script>
	<style>
		body { background: #E6EEF7; text-align: center; }

		#install { background: #fff; width: 500px; margin: 50px auto 0; }
		#i-logo { background: #000; padding: 5px; }
		#i-main { padding: 0 15px 15px; }
		ul.rows { border: 1px solid #E6EEF7; height: 300px; margin: 0 0 15px; list-style-type: none; padding: 10px 15px; overflow: auto; }
		h2 { color: #36c; }
	</style>
 </head>

<body>
	<div id="install">
		<div id="i-logo" class="round clearfix"><a href="http://typecho.org"><img src="admin/images/logo.png" alt="Typecho" /></a></div>
		<hr class="space" />
		<div id="i-main"><h2><?php _e('%s安装配置程序', __TYPECHO_INSTALL_VERSION__); ?></h2>

			<ul class="rows">
    		<li><h3>版权信息</h3>
    		<p>Magike Blog是一款开源免费的博客程序. 您可以在
    		<a href="http://www.opensource.org/licenses/gpl-license.php" target="__blank">GPL协议</a>允许的范围内使用此产品. </p>
    		<p>您可以在该协议授权的范围内使用或修改此软件. 此协议的约束范围并不包括在该软件中使用的第三方库资源. 所有在本软件中使用的第三方资源
    著作权归原作者所有, 其版权协议也继承自原有协议. </p>
    		</li>
    		<li><h3>支持及免责条款</h3>

    		<p>您可以在<a href="http://www.magike.org" target="__blank">Magike官方网站</a>或者<a href="http://forum.magike.org" target="__blank">Magike开发社区</a>获得持续的技术支持. </p>
    		<p>我们并不保证使用该程序不存在任何风险, 对使用该程序可能造成的损失不承担任何责任. 但是我们会对可能存在的风险进行持续的跟踪评估, 并尽量减少您的损失. </p>
    		</li>
    		<li><h3>致谢</h3>
    		<p>对所有在Magike开发过程中给予我们支持和帮助的朋友表示感谢. 对在Magike测试过程中辛勤劳动的测试人员表示感谢. 对以下在本软件中使用的第三方资源的原作者表示感谢: </p>

    		<p>
    			<strong>Silk图标</strong>, 作者: <a href="http://www.famfamfam.com" target="_blank">Mark James</a>, 使用范围: 后台部分图标及默认模板部分图标<br>
    			<strong>PHPMailer库</strong>, 作者: <a href="http://phpmailer.sourceforge.net" target="_blank">Chris Ryan</a>, 使用范围: 第三方类库<br>
    			<strong>XML-RPC库</strong>, 作者: <a href="http://scripts.incutio.com/xmlrpc/" target="_blank">Incutio Ltd</a>, 使用范围: 第三方类库<br>

    			<strong>ServicesJson库</strong>, 作者: <a href="http://pear.php.net/pepr/pepr-proposal-show.php?id=198" target="__blank">Michal Migurski, Matt Knapp, Brett Stimmerman</a>, 使用范围: 第三方类库<br>
    			<strong>jQuery库</strong>, 作者: <a href="http://www.jquery.com" target="_blank">jQuery team</a>, 使用范围: javascript框架<br>
    			<strong>Gettext库</strong>, 作者: <a href="http://pear.php.net/package/File_Gettext" target="_blank">Michael Wallner</a>, 使用范围: 国际化语言文件读取库<br>

    			<strong>NetIDNA库</strong>, 作者: <a href="http://pear.php.net/package/Net_IDNA/download" target="_blank">Markus Nix, Matthias Sommerfeld</a>, 使用范围: 第三方类库
                     <strong>管理后台语义标准化</strong>, 作者: <a href="http://www.awflasher.com" target="_blank">awflasher</a>, 使用范围: 后台语言源
              </p>
    		</li>
    	</ul>
		<input type="button" value="同意并安装" onclick="" />
		</div>
	</div>
</body>
</html>
