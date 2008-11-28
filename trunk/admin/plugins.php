<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <table class="typecho-list-table" class="operate-table">
                    <colgroup>
                        <col width="150"/>
                        <col width="400"/>
                        <col width="100"/>
                        <col width="150"/>
                        <col width="200"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?php _e('名称'); ?></th>
                            <th><?php _e('描述'); ?></th>
                            <th><?php _e('版本'); ?></th>
                            <th><?php _e('作者'); ?></th>
                            <th><?php _e('操作'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Plugins_List')->to($plugins); ?>
                    	<?php if ($plugins->have()): ?>
                        <?php while ($plugins->next()): ?>
                        <tr<?php $plugins->alt('', ' class="even"'); ?>>
                            <td><?php $plugins->title(); ?></td>
                            <td><?php $plugins->description(); ?></td>
                            <td><?php $plugins->version(); ?></td>
                            <td><?php echo empty($plugins->homepage) ? $plugins->author : '<a href="' . $plugins->homepage
                            . '">' . $plugins->author . '</a>'; ?></td>
                            <td>
                                <?php if ($plugins->activate): ?>
                                    <?php if ($plugins->activated): ?>
                                        <?php if ($plugins->config): ?>
                                            <a href="<?php $options->adminUrl('option-plugin.php?config=' . $plugins->name); ?>"><?php _e('配置'); ?></a> 
                                            | 
                                        <?php endif; ?>
                                        <a href="<?php $options->index('Plugins/Edit.do?deactivate=' . $plugins->name); ?>"><?php _e('禁用'); ?></a>
                                    <?php else: ?>
                                        <a href="<?php $options->index('Plugins/Edit.do?activate=' . $plugins->name); ?>"><?php _e('激活'); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="6"><?php _e('没有安装插件'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
