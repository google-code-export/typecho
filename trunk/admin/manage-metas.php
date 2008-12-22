<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main manage-metas">
                <div class="column-16 start-01">
                    <ul class="typecho-option-tabs">
                        <li<?php if(!Typecho_Request::isSetParameter('type') || 'category' == Typecho_Request::getParameter('type')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-metas.php'); ?>"><?php _e('分类'); ?></a></li>
                        <li<?php if('tag' == Typecho_Request::getParameter('type')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-metas.php?type=tag'); ?>"><?php _e('标签'); ?></a></li>
                    </ul>
                    <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($categories); ?>
                    <table class="typecho-list-table">
                        <colgroup>
                            <col width="10"/>
                            <col width="150"/>
                            <col width="150"/>
                            <col width="400"/>
                            <col width="100"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="typecho-radius-topleft"> </th>
                                <th><?php _e('名称'); ?></th>
                                <th><?php _e('缩略名'); ?></th>
                                <th><?php _e('描述'); ?></th>
                                <th class="typecho-radius-topright"><?php _e('文章数'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($categories->next()): ?>
                            <tr<?php $categories->alt(' class="even"', ''); ?>>
                                <td> </td>
                                <td><a href="<?php echo Typecho_Request::uri('mid=' . $categories->mid); ?>"><?php $categories->name(); ?></a></td>
                                <td><?php $categories->slug(); ?></td>
                                <td><?php $categories->description(); ?></td>
                                <td><?php $categories->count(); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="column-08 start-17 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                    <?php Typecho_Widget::widget('Widget_Metas_Category_Edit')->form()->render(); ?>
                </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
