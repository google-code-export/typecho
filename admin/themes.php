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
                    <li class="current"><a href="<?php $options->adminUrl('themes.php'); ?>"><?php _e('可以使用的外观'); ?></a></li>
                    <li><a href="<?php $options->adminUrl('theme-editor.php'); ?>"><?php _e('编辑当前外观'); ?></a></li>
                </ul>
                
                <table class="typecho-list-table typecho-theme-list">
                    <colgroup>
                        <col width="450"/>
                        <col width="450"/>
                    </colgroup>
                    <?php Typecho_Widget::widget('Widget_Themes_List')->to($themes); ?>
                    <?php while($themes->next()): ?>
                    <?php $themes->alt('<tr>', ''); ?>
                    <td class="<?php if($themes->activated): ?>current <?php endif; ?>typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                        <div class="column-04">
                        <img src="<?php $themes->screen(); ?>" width="120" height="90" align="left" />
                        </div>
                        <div class="column-08">
                        <h4><?php $themes->title(); ?>
                        <?php if($options->theme != $themes->name): ?><a class="button" href="<?php $options->index('Themes/Edit.do?change=' . $themes->name); ?>"><?php _e('激活'); ?></a><?php endif; ?></h4>
                        <cite><?php _e('作者'); ?>: <?php if($themes->homepage): ?><a href="<?php $themes->homepage() ?>"><?php endif; ?><?php $themes->author(); ?><?php if($themes->homepage): ?></a><?php endif; ?>
                        &nbsp;&nbsp;&nbsp;<?php _e('版本'); ?>: <?php $themes->version() ?>
                        </cite>
                        <p><?php echo nl2br($themes->description); ?></p>
                        </div>
                    </td>
                    <?php $last = $themes->sequence; ?>
                    <?php $themes->alt('', '</tr>'); ?>
                    <?php endwhile; ?>
                    <?php if($last % 2): ?>
                    <td>&nbsp;</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
