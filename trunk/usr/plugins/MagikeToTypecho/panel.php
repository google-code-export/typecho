<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$success = true;
try {
    $dbConfig = $options->plugin('MagikeToTypecho');

    /** 初始化一个db */
    if (Typecho_Db_Adapter_Mysql::isAvailable()) {
        $magikeDb = new Typecho_Db('Mysql', $dbConfig->prefix);
    } else {
        $magikeDb = new Typecho_Db('Pdo_Mysql', $dbConfig->prefix);
    }

    /** 只读即可 */
    $magikeDb->addServer(array (
      'host' => $dbConfig->host,
      'user' => $dbConfig->user,
      'password' => $dbConfig->password,
      'charset' => 'utf8',
      'port' => $dbConfig->port,
      'database' => $dbConfig->database
    ), Typecho_Db::READ);
    
    $rows = $magikeDb->fetchAll($magikeDb->select()->from('table.statics'));
} catch (Typecho_Db_Exception $e) {
    $success = false;
}

include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-22 start-02">
                <?php if ($success): ?>
                <div class="message notice">
                    <?php _e('我们检测到了你已经安装的 Magike 系统信息, 点击下方的开始按钮开始数据转换, 数据转换可能会耗时较长.'); ?>
                </div>
                <?php else: ?>
                <div class="message error">
                    <?php _e('我们在连接到 Magike 的数据库时发生了错误, 请<a href="%s">重新设置</a>你的信息.', 
                    Typecho_Common::url('option-plugin.php?config=MagikeToTypecho', $options->adminUrl)); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
include 'common-js.php';
include 'copyright.php';
?>
