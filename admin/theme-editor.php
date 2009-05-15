<?php
include 'common.php';
include 'header.php';
include 'menu.php';

Typecho_Widget::widget('Widget_Themes_Files')->to($files);
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01">
                <ul class="typecho-option-tabs">
                    <li><a href="<?php $options->adminUrl('themes.php'); ?>"><?php _e('可以使用的外观'); ?></a></li>
                    <li class="current"><a href="<?php $options->adminUrl('theme-editor.php'); ?>">
                    <?php if ($options->theme == $files->theme): ?>
                    <?php _e('编辑当前外观'); ?>
                    <?php else: ?>
                    <?php _e('编辑%s外观', ' <cite>' . $files->theme . '</cite> '); ?>
                    <?php endif; ?>
                    </a></li>
                </ul>
                
                <div class="typecho-edit-theme">
                    <div>
                        <ul>
                            <?php while($files->next()): ?>
                            <li<?php if($files->current): ?> class="current"<?php endif; ?>>
                            <a href="<?php $options->adminUrl('theme-editor.php?theme=' . $files->currentTheme() . '&file=' . $files->file); ?>"><?php $files->file(); ?></a></li>
                            <?php endwhile; ?>
                        </ul>
                        <div class="content">
                        <form method="post" name="theme" id="theme" action="<?php $options->index('Themes/Edit.do'); ?>">
                            <textarea name="content" id="content"><?php echo $files->currentContent(); ?></textarea>
                            <?php if($files->currentIsWriteable()): ?>
                            <div class="submit">
                                <input type="hidden" name="theme" value="<?php echo $files->currentTheme(); ?>" />
                                <input type="hidden" name="edit" value="<?php echo $files->currentFile(); ?>" />
                                <button type="submit">保存文件</button>
                            </div>
                            <?php endif; ?>
                        </form>
                        <?php Typecho_Plugin::factory('admin/theme-editor.php')->form(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
