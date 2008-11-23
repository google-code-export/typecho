<?php
define('__TYPECHO_INSTALL_VERSION__', 'Typecho 0.3/8.11.11');

/** 载入配置文件 */
if (file_exists('config.inc.php')) {
    require_once 'config.inc.php';
    $configured = true;
} else {
    require_once 'config.sample.php';
    $configured = false;
}

/**
 * 获取传递参数
 * 
 * @param string $name 参数名称
 * @param string $default 默认值
 * @return string
 */
function _v($name, $default = '')
{
    echo isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

/**
 * 判断是否兼容某个环境(perform)
 * 
 * @param string $adapter 适配器
 * @return boolean
 */
function _p($adapter)
{
    switch ($adapter) {
        case 'Mysql':
            return function_exists('mysql_connect');
        case 'Pdo_Mysql':
            return extension_loaded('pdo') && in_array('mysql', PDO::getAvailableDrivers());
        case 'SQLite':
            return function_exists('sqlite_open');
        case 'Pdo_SQLite':
            return extension_loaded('pdo') && in_array('sqlite', PDO::getAvailableDrivers());
        case 'Pgsql':
            return function_exists('pg_connect');
        case 'Pdo_Pgsql':
            return extension_loaded('pdo') && in_array('pgsql', PDO::getAvailableDrivers());
        default:
            return false;
    }
}

$options = new stdClass();
$options->generator = __TYPECHO_INSTALL_VERSION__;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo __TYPECHO_CHARSET__; ?>" />
	<title><?php _e('Typecho安装程序'); ?></title>
    <link rel="stylesheet" type="text/css" href="admin/css/reset.source.css" />
    <link rel="stylesheet" type="text/css" href="admin/css/grid.source.css" />
    <link rel="stylesheet" type="text/css" href="admin/css/typecho.source.css" />
</head>
<body>
<div class="typecho-install-patch">
    <ol class="path">
        <li<?php if (!isset($_GET['finish']) && !isset($_GET['config'])) : ?> class="current"<?php endif; ?>><?php _e('欢迎使用'); ?></li>
        <li<?php if (isset($_GET['config'])) : ?> class="current"<?php endif; ?>><?php _e('输入您的配置'); ?></li>
        <li<?php if (isset($_GET['finish'])) : ?> class="current"<?php endif; ?>><?php _e('安装成功'); ?></li>
    </ol>
</div>
<div class="main">
    <div class="body body-950">
        <div class="container">
            <div class="column-14 start-06 typecho-install">
            <?php if (isset($_GET['finish'])) : ?>
                <h1 class="typecho-install-title"><?php _e('安装成功!'); ?></h1>
                <div class="typecho-install-body">
                    <div class="session">
                    <p><?php _e('您的用户名是'); ?>:<em>明城</em></p>
                    <p><?php _e('您的密码是'); ?>:<em>12345679</em></p>
                    </div>

                    <div class="session">
                    <p><?php _e('您可以将下面两个链接保存到您的收藏夹'); ?>:</p>
                    <ul>
                        <li><a href="/admin/index.php"><?php _e('点击这里访问您的控制面板'); ?></a></li>
                        <li><a href="/index.php"><?php _e('点击这里查看您的 Blog'); ?></a></li>
                    </ul>
                    </div>

                    <p><?php _e('希望你能尽情享用 Typecho 带来的乐趣!'); ?></p>
                </div>
            <?php  elseif (isset($_GET['config'])) : ?>
            <?php
                    $adapter = Typecho_Request::getParameter('dbAdapter', 'Mysql');
                    $type = explode('_', $adapter);
                    $type = array_pop($type);
            ?>
                <form method="post" action="?config" name="config">
                    <h1 class="typecho-install-title"><?php _e('确认您的配置'); ?></h1>
                    <div class="typecho-install-body">
                        <h2><?php _e('数据库配置'); ?></h2>
                        <?php
                            if ('config' == Typecho_Request::getParameter('do')) {
                                $installDb = new Typecho_Db($adapter, Typecho_Request::getParameter('dbPrefix'));
                                $_dbConfig = Typecho_Request::getParametersFrom('dbHost', 'dbUser', 'dbPassword', 'dbPort', 'dbDatabase', 'dbFile', 'dbDSN');
                                $dbConfig = array();
                                foreach($_dbConfig as $key => $val)
                                {
                                    $dbConfig[strtolower(substr($key, 2))] = $val;
                                }

                                $installDb->addServer($dbConfig, Typecho_Db::READ | Typecho_Db::WRITE);
                                
                                try {
                                    $installDb->query('SELECT 1 = 1');
                                } catch (Typecho_Db_Exception $e) {
                                    echo '<p class="message error">' . _t('安装程序捕捉到以下错误: "%s"',$e->getMessage()) . '</p>';
                                }
                            }
                        ?>
                        <ul class="typecho-option">
                            <li>
                            <label class="typecho-label"><?php _e('数据库适配器'); ?></label>
                            <select name="dbAdapter">
                                <?php if(_p('Mysql')): ?><option value="Mysql"<?php if('Mysql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Mysql原生函数适配器') ?></option><?php endif; ?>
                                <?php if(_p('SQLite')): ?><option value="SQLite"<?php if('SQLite' == $adapter): ?> selected=true<?php endif; ?>><?php _e('SQLite原生函数适配器(SQLite 2.x)') ?></option><?php endif; ?>
                                <?php if(_p('Pgsql')): ?><option value="Pgsql"<?php if('Pgsql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pgsql原生函数适配器') ?></option><?php endif; ?>
                                <?php if(_p('Pdo_Mysql')): ?><option value="Pdo_Mysql"<?php if('Pdo_Mysql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pdo驱动Mysql适配器') ?></option><?php endif; ?>
                                <?php if(_p('Pdo_SQLite')): ?><option value="Pdo_SQLite"<?php if('Pdo_SQLite' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pdo驱动SQLite适配器(SQLite 3.x)') ?></option><?php endif; ?>
                                <?php if(_p('Pdo_Pgsql')): ?><option value="Pdo_Pgsql"<?php if('Pdo_Pgsql' == $adapter): ?> selected=true<?php endif; ?>><?php _e('Pdo驱动PostgreSql适配器') ?></option><?php endif; ?>
                            </select>
                            <p class="desption"><?php _e('请根据你的数据库类型选择合适的适配器'); ?></p>
                            </li>
                            <?php require_once './install/' . $type . '.php'; ?>
                            <li>
                            <label class="typecho-label"><?php _e('数据库前缀'); ?></label>
                            <input type="text" class="text mini" name="dbPrefix" value="<?php _v('dbPrefix', 'typecho_'); ?>" />
                            <p class="desption"><?php _e('默认前缀是 "typecho_"'); ?></p>
                            </li>
                        </ul>
                        
                        <script>
                        var _select = document.config.dbAdapter;
                        _select.onchange = function()
                        {
                            var _form = document.config;
                            var _do = _form.do;
                            _do.value = 'change';
                            _form.submit();
                        }
                        </script>
                        
                        <h2><?php _e('创建您的管理员帐号'); ?></h2>
                        <ul class="typecho-option">
                            <li>
                            <label class="typecho-label"><?php _e('用户名'); ?></label>
                            <input type="text" name="userName" class="text" />
                            </li>
                            <li>
                            <label class="typecho-label"><?php _e('邮件地址'); ?></label>
                            <input type="text" name="userMail" class="text" />
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="do" value="config" />
                    <p class="submit"><button type="submit"><?php _e('确认, 开始安装 &raquo;'); ?></button></p>
                </form>
            <?php  else: ?>
                <form method="post" action="?config">
                <h1 class="typecho-install-title"><?php _e('欢迎使用Typecho'); ?></h1>
                <div class="typecho-install-body">
                <h2><?php _e('安装说明'); ?></h2>
                <p><strong><?php _e('本安装程序将自动检测服务器环境是否符合最低配置需求.如果不符合,将在上方出现提示信息,
请按照提示信息检查你的主机配置.如果服务器环境符合要求,将在下方出现"同意并安装"的按钮,点击此按钮即可一步完成安装.'); ?></strong></p>
                <h2><?php _e('许可及协议'); ?></h2>
                <p><?php _e('Typecho基于GPL协议发布,我们允许用户在GPL协议许可的范围内使用,拷贝,修改和分发此程序.
你可以自由地将其用于商业以及非商业用途.'); ?></p>
                <p><?php _e('Typecho软件由其社区提供支持,核心开发团队负责维护程序日常开发工作以及新特性的制定.如果你遇到使用上的问题,
程序中的BUG,以及期许的新功能,欢迎你在社区中交流或者直接向我们贡献代码.对于贡献突出者,他的名字将出现在贡献者名单中.'); ?></p>
                <h2><?php _e('此版本贡献者(排名不分先后)'); ?></h2>
                <ol>

                </ol>
                <p><a href="http://typecho.org"><?php _e('查看所有贡献者'); ?></a></p>
                </div>
                <p class="submit"><button type="submit"><?php _e('我准备好了, 开始下一步 &raquo;'); ?></button></p>
                </form>
            <?php endif; ?>
            
            </div>
        </div>
    </div>
</div>
<?php include 'admin/copyright.php'; ?>
