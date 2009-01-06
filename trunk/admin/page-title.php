<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if(is_file(__TYPECHO_ROOT_DIR__ . '/install.php')): ?>
<div class="message error popup">
<ul>
    <li><?php _e('安装程序还存在于您的主机上. 为了保证使用安全, 请删除它们.'); ?></li>
</ul>
</div>
<?php elseif($notice->have() && in_array($notice->noticeType, array('success', 'notice', 'error'))): ?>
<div class="message <?php $notice->noticeType(); ?> popup">
<ul>
    <?php $notice->lists(); ?>
</ul>
</div>
<?php endif; ?>
<div class="container typecho-page-title">
    <div class="column-24 start-01">
        <h2><?php echo $menu->title; ?></h2>
        <p><a href="<?php $options->siteUrl(); ?>"><?php _e('查看我的站点'); ?></a></p>
    </div>
</div>
