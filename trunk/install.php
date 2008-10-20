<?php
define('__TYPECHO_INSTALL_VERSION__', 'Typecho 0.2/8.7.6');

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
        <?php if('finish' == Typecho_Request::getParameter('step')): ?>
        <?php
            $url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];	
            if(isset($_SERVER["QUERY_STRING"]))
            {
                $url = str_replace("?" . $_SERVER["QUERY_STRING"], "", $url);
            }
            
            $url = dirname($url);

            $db = Typecho_Db::get();
            
            $adapter = Typecho_Config::get('Db')->adapter;
            $sqlFiles = glob('./install/*.sql');
            $selectAdapter = '';
            $maxMatch = 0;
            
            foreach($sqlFiles as $file)
            {
                $file = substr(basename($file), 0, -4);
                
                if(false !== strpos($adapter, $file))
                {
                    $selectAdapter = $file;
                    if($file == $adapter)
                    {
                        break;
                    }
                }
            }
            
            $scripts = explode(';', file_get_contents('./install/' . $selectAdapter . '.sql'));
            
            /** 初始化结构 */
            foreach($scripts as $script)
            {
                if(trim($script))
                {
                    /** 替换前缀 */
                    $db->query(str_replace('typecho_', Typecho_Config::get('Db')->prefix, $script));
                }
            }
            
            /** 全局变量 */
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'theme', 'user' => 0, 'value' => 'default')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'timezone', 'user' => 0, 'value' => 28800)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'charset', 'user' => 0, 'value' => 'UTF-8')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'generator', 'user' => 0, 'value' => __TYPECHO_INSTALL_VERSION__)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'title', 'user' => 0, 'value' => 'Hello World')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'description', 'user' => 0, 'value' => 'Just So So ...')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'keywords', 'user' => 0, 'value' => 'typecho,php,blog')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'rewrite', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsRequireMail', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsRequireURL', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'attachmentExtensions', 'user' => 0, 'value' => 'zip|rar|jpg|png|gif|txt')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsRequireModeration', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'plugins', 'user' => 0, 'value' => 'a:0:{}')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentDateFormat', 'user' => 0, 'value' => 'Y-m-d H:i:s')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'siteUrl', 'user' => 0, 'value' => $url)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'defaultCategory', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'allowRegister', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'defaultAllowComment', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'defaultAllowPing', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'defaultAllowFeed', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'pageSize', 'user' => 0, 'value' => 5)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'postsListSize', 'user' => 0, 'value' => 10)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsListSize', 'user' => 0, 'value' => 10)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsHTMLTagAllowed', 'user' => 0, 'value' => NULL)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'postDateFormat', 'user' => 0, 'value' => 'Y-m-d')));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'feedFullArticlesLayout', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'editorSize', 'user' => 0, 'value' => 16)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'autoSave', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsPostTimeout', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsUrlNofollow', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsShowUrl', 'user' => 0, 'value' => 1)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsUniqueIpInterval', 'user' => 0, 'value' => 0)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsStopWords', 'user' => 0, 'value' => NULL)));
            $db->query($db->sql()->insert('table.options')->rows(array('name' => 'commentsIpBlackList', 'user' => 0, 'value' => NULL)));
            
            /** 初始分类 */
            $db->query($db->sql()->insert('table.metas')->rows(array('name' => _t('默认分类'), 'slug' => 'default', 'type' => 'category', 'description' => _t('只是一个默认分类'),
            'count' => 1, 'sort' => 1)));
            
            /** 初始关系 */
            $db->query($db->sql()->insert('table.relationships')->rows(array('cid' => 1, 'mid' => 1)));
            
            /** 初始链接 */
            $db->query($db->sql()->insert('table.metas')->rows(array('name' => _t('Typecho官方网站'), 'slug' => 'http://www.typecho.org', 'type' => 'link', 'description' => _t('Typecho的老巢'),
            'count' => 0, 'sort' => 1)));
            
            /** 初始内容 */
            $db->query($db->sql()->insert('table.contents')->rows(array('title' => _t('欢迎使用Typecho'), 'slug' => 'start', 'created' => 1211300209, 'modified' => 1211300209,
            'text' => _t('<p>如果您看到这篇文章,表示您的blog已经安装成功.</p>'), 'author' => 1, 'type' => 'post', 'commentsNum' => 1, 'allowComment' => 'enable',
            'allowPing' => 'enable', 'allowFeed' => 'enable')));
            
            $db->query($db->sql()->insert('table.contents')->rows(array('title' => _t('欢迎使用Typecho'), 'slug' => 'start', 'created' => 1211300209, 'modified' => 1211300209,
            'text' => _t('<p>这只是个测试页面.</p>'), 'author' => 1, 'meta' => 1, 'type' => 'page', 'commentsNum' => 1, 'allowComment' => 'enable',
            'allowPing' => 'enable', 'allowFeed' => 'enable')));
            
            /** 初始评论 */
            $db->query($db->sql()->insert('table.comments')->rows(array('cid' => 1, 'created' => 1211300209, 'author' => 'Typecho', 'url' => 'http://www.typecho.org',
            'ip' => '127.0.0.1', 'agent' => __TYPECHO_INSTALL_VERSION__, 'text' => '欢迎加入Typecho大家族', 'mode' => 'comment', 'status' => 'approved', 'parent' => 0)));
            
            /** 初始用户 */
            $db->query($db->sql()->insert('table.users')->rows(array('name' => 'admin', 'password' => '827ccb0eea8a706c4c34a16891f84e7b', 'mail' => 'example@yourdomain.com', 
            'url' => 'http://www.typecho.org', 'screenName' => 'admin', 'group' => 'administrator', 'created' => (time() - idate('Z')))));
        ?>
        <div class="success">
            <?php _e('安装成功,后台用户名为admin,密码为12345.<a href="./admin/login.php">点击这里进入</a>'); ?>
        </div>
        <?php else: ?>
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

                </ol>
                <p><a href="http://www.typecho.org">查看所有贡献者</a></p>
    		</li>
    	</ul>
        <form method="get">
		<input type="submit" class="button" value="同意并安装" />
        <input type="hidden" name="step" value="finish" />
        </form>
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
        <?php endif; ?>
		</div>
	</div>
</body>
</html>
