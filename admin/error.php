<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo __TYPECHO_CHARSET__; ?>" />
	<title><?php echo $exception->getMessage(); ?> - Powered By Typecho</title>
	<style>
		body { background: #E6EEF7;padding:0;margin:0; font-family:Georgia,Times,"Times New Roman",serif }
        #e-logo { background: #000; padding: 10px; width: 420px; margin:0 auto; text-align:right; }
		#exception {background: #fff; width: 398px; margin:0 auto; padding:20px; 
        border:1px solid #D6E3F1; border-top:none; }
		ul { padding:0;margin:20px 0 0 0;list-style:none;font-size:12pt }
        ul li { line-height:24px; }
		h2 { color:#36c;padding:0;margin:0;font-size:24pt;text-align:center;text-decoration:underline }
        a { color:#000 }
	</style>
</head>
<body>
    <div id="exception">
        <h2><?php _e('%s错误', $exception->getCode()); ?></h2>
        <?php if(TypechoException::NOTFOUND == $exception->getCode()): ?>
            <ul>
                <li><?php _e('您访问的页面 %s 不存在', $_SERVER['REQUEST_URI']); ?></li>
                <li><?php _e('请确认您访问的地址是否正确'); ?></li>
            </ul>
        <?php elseif(TypechoException::FORBIDDEN == $exception->getCode()): ?>
            <ul>
                <li><?php _e('您请求的地址被禁止访问'); ?></li>
            </ul>
        <?php elseif(TypechoException::RUNTIME == $exception->getCode()): ?>
            <ul>
                <li><?php _e('服务器在运行中出现错误'); ?></li>
                <li><?php _e('请检查服务器的运行日志或者访问Typecho.org获得更多支持'); ?></li>
            </ul>
        <?php elseif(TypechoException::UNVAILABLE == $exception->getCode()): ?>
            <ul>
                <li><?php _e('数据库当前不可用'); ?></li>
                <li><?php _e('请检查数据库是否运行正常'); ?></li>
                <li><?php _e('或者程序配置出现问题'); ?></li>
                <li><?php _e('访问Typecho.org获得更多支持'); ?></li>
            </ul>
        <?php endif; ?>
        <small style="float:right;font-size:8pt;font-weight:bold">Powered By <a href="http://www.typecho.org">Typecho</a></small>
    </div>
</body>
</html>
