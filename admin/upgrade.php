<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-22 start-02">
                <div class="message notice">
                    <form action="<?php $options->index('Upgrade.do'); ?>" method="post">
                    <h6><?php _e('检测到新版本!'); ?></h6>
                    <blockquote>
                    <ul>
                        <li><?php _e('您已经更新了系统程序, 我们还需要执行一些后续步骤来完成升级'); ?></li>
                        <li><?php _e('此程序将把您的系统从 <strong>%s</strong> 升级到 <strong>%s</strong>', $currentVersion, Typecho_Common::$config['version']); ?></li>
                        <li><strong><?php _e('在升级之前强烈建议先备份您的数据'); ?></strong></li>
                    </ul>
                    </blockquote>
                    <br />
                    <p><button type="submit"><?php _e('完成升级 &raquo;'); ?></button></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
