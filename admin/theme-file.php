<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01">
                <ul class="typecho-option-tabs">
                    <li><a href="<?php $options->adminUrl('themes.php'); ?>"><?php _e('可以使用的外观'); ?></a></li>
                    <li class="current"><a href="<?php $options->adminUrl('theme-file.php'); ?>"><?php _e('编辑当前外观'); ?></a></li>
                </ul>
                
                <div class="typecho-edit-theme">
                    <div>
                        <ul>
                            <?php Typecho_Widget::widget('Widget_Themes_Files')->to($files); ?>
                            <?php while($files->next()): ?>
                            <li<?php if($files->current): ?> class="current"<?php endif; ?>>
                            <a href="<?php $options->adminUrl('theme-file.php?theme=' . $files->currentTheme() . '&file=' . $files->file); ?>"><?php $files->file(); ?></a></li>
                            <?php endwhile; ?>
                        </ul>
                        <div class="content">
                            <textarea><?php echo $files->currentContent(); ?></textarea>
                            <?php if($files->currentIsReadable()): ?>
                            <div class="submit"><button type="submit">保存文件</button></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
