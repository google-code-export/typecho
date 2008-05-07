<?php

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
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo __TYPECHO_CHARSET__; ?>" />
    <title><?php _e('安装Typecho'); ?></title>
    <link href="admin/css/default.css" rel="stylesheet" type="text/css" />
    <link href="admin/css/style.css" rel="stylesheet" type="text/css" />
    <link href="admin/css/hack.css" rel="stylesheet" type="text/css" />
    <!--[if IE]><link rel="stylesheet" href="admin/css/ie.css" type="text/css" media="screen, projection"><![endif]-->
</head>

<body style="background: #E6EEF7;">
    <div id="login" class="round clearfix" style="margin-top:50px;width:400px;padding-bottom:10px">
    <form action="install.php">
        <p style="text-align: center;"><img id="login-logo.png" src="admin/images/login-logo.png" alt="Typecho" /></p>
        <p>
            <ul style="list-style:none">
                <li><?php _e('Typecho基于<a href="http://www.opensource.org/licenses/gpl-2.0.php" target="_blank">GPL发行</a>.'); ?></li>
                <li><?php _e('此安装脚本在第一次运行前执行,如果您在安装过程中有什么疑问,
                请访问<a href="http://www.typecho.org/doku.php?id=install" target="_blank">此页面</a>获取更多信息.'); ?></li>
            </ul>
        </p>
        <p class="right"><input type="hidden" name="step" value="finish" /><input type="submit" value="<?php _e('只需一步,开始安装 &raquo;'); ?>" /></p>
    </form>
    </div>
</body>
</html>
