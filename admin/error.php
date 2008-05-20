<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo __TYPECHO_CHARSET__; ?>" />
	<title><?php echo $exception->getMessage(); ?> - Powered By Typecho</title>
	<style>
		body { background: #E6EEF7;padding:0;margin:0; font-family:Georgia,Times,"Times New Roman",serif }
        #e-logo { background: #000; padding: 10px; width: 420px; margin:0 auto; text-align:right; }
		#exception {background: #fff; width: 398px; margin:0 auto; padding:20px; 
        border-right:1px solid #D6E3F1; border-left:1px solid #D6E3F1; }
		ul { padding:0;margin:20px 0 0 0;list-style:none;font-size:12pt }
		h2 { color:#36c;padding:0;margin:0;font-size:24pt;text-align:center;text-decoration:underline }
	</style>
</head>
<body>
    <div id="exception">
        <h2><?php _e('%s错误', $exception->getCode()); ?></h2>
        <ul>
            <?php if($messages = $exception->getMessages()): ?>
                <li><?php echo implode('</li><li>', $messages); ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <div id="e-logo">
        <img src="http://www.typecho.org/logo.png" alt="Typecho" />
    </div>
</body>
</html>
