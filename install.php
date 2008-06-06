<?php
define('__TYPECHO_INSTALL_VERSION__', 'Typecho Developer Preview');


/** 载入配置文件 */
if(file_exists('config.inc.php'))
{
    require_once 'config.inc.php';
    $configured = true;
}
else
{
    require_once 'config.sample.php';
    $configured = false;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo __TYPECHO_CHARSET__; ?>" />
	<title><?php _e('Typecho安装程序'); ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link href="admin/css/default.css" rel="stylesheet" type="text/css" />
	<link href="admin/css/style.css" rel="stylesheet" type="text/css" />
	<link href="admin/css/hack.css" rel="stylesheet" type="text/css" />
	<!--[if IE]><link rel="stylesheet" href="admin/css/ie.css" type="text/css" media="screen, projection"><![endif]-->
	<script src="admin/js/jquery-1.2.3.pack.js" type="text/javascript"></script>
	<script src="admin/js/add.js" type="text/javascript"></script>
	<script src="admin/js/jquery.curvycorners.packed.js" type="text/javascript"></script>
	<style>
		body { background: #E6EEF7;}

		#install { background: #fff; width: 500px; margin: 50px auto 0; }
		#i-logo { background: #000; padding: 5px; }
		#i-main { padding: 0 15px 15px; }
		ul.rows { border: 1px solid #E6EEF7; height: 300px; margin: 0 0 15px; list-style-type: none; padding: 10px 15px; overflow: auto; }
		h2 { color: #36c; }
        ol { margin-left:20px }
        ol li { margin:0 }
	</style>
 </head>

<body>
	<div id="install">
		<div id="i-logo" class="round clearfix" style="text-align:center"><a href="http://typecho.org"><img src="admin/images/logo.png" alt="Typecho" /></a></div>
		<hr class="space" />
		<div id="i-main"><h2><?php _e('欢迎使用Typecho'); ?></h2>
        <?php if($configured): ?>
        <ul class="rows">
    		<li>
                <h3><?php _e('安装说明'); ?></h3>
                <p><strong><?php _e('本安装程序将自动检测服务器环境是否符合最低配置需求.如果不符合,将在上方出现提示信息,
请按照提示信息检查你的主机配置.如果服务器环境符合要求,将在下方出现"同意并安装"的按钮,点击此按钮即可一步完成安装.'); ?></strong></p>
                <h3><?php _e('许可及协议'); ?></h3>
                <p><?php _e('Typecho基于GPL协议发布,我们允许用户在GPL协议许可的范围内使用,拷贝,修改和分发此程序.
你可以自由地将其用于商业以及非商业用途.'); ?></p>
                <p><?php _e('Typecho软件由其社区提供支持,核心开发团队负责维护程序日常开发工作以及新特性的制定.如果你遇到使用上的问题,
程序中的BUG,以及期许的新功能,欢迎你在社区中交流或者直接向我们贡献代码.对于贡献突出者,他的名字将出现在贡献者名单中.'); ?></p>
                <h3><?php _e('此版本贡献者(排名不分先后)'); ?></h3>
                <ol>
                    <li><a href="http://www.joyqi.com">Joyqi</a></li>
                    <li><a href="http://www.luweiqing.com">Sluke</a></li>
                    <li><a href="http://www.hellowiki.com">Fen</a></li>
                </ol>
                <p><a href="http://www.typecho.org">查看所有贡献者</a></p>
    		</li>
    	</ul>
		<input type="button" class="button" value="同意并安装" onclick="" />
        <?php else: ?>
            <?php if(is_writable('.')): ?>
            <div class="notice">
                <?php _e('还没有配置文件?<a href="install.php?config">点击这里创建</a> <sup><a href="#">什么是配置文件?</a></sup>'); ?>
            </div>
            <?php else: ?>
            <div class="error">
                <?php _e('程序根目录 <sup><a href="#">什么是根目录?</a></sup>无法写入 <sup><a href="#">为何无法写入?</a></sup>,请检查 <sup><a href="#">如何检查?</a></sup>服务器环境.'); ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
		</div>
	</div>
</body>
</html>
