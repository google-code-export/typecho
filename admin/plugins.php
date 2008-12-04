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
                <?php Typecho_Widget::widget('Widget_Plugins_List_Activated')->to($activatedPlugins); ?>
                <?php if ($activatedPlugins->have()): ?>
                <h6 class="typecho-list-table-title">激活的插件</h6>
                <table class="typecho-list-table">
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
                        <?php while ($activatedPlugins->next()): ?>
                        <tr<?php $activatedPlugins->alt(' class="even"', ''); ?>>
                            <td><?php $activatedPlugins->title(); ?></td>
                            <td><?php $activatedPlugins->description(); ?></td>
                            <td><?php $activatedPlugins->version(); ?></td>
                            <td><?php echo empty($activatedPlugins->homepage) ? $activatedPlugins->author : '<a href="' . $activatedPlugins->homepage
                            . '">' . $activatedPlugins->author . '</a>'; ?></td>
                            <td>
                                <?php if ($activatedPlugins->activate): ?>
                                    <?php if ($activatedPlugins->activated): ?>
                                        <?php if ($activatedPlugins->config): ?>
                                            <a href="<?php $options->adminUrl('option-plugin.php?config=' . $activatedPlugins->name); ?>"><?php _e('配置'); ?></a> 
                                            | 
                                        <?php endif; ?>
                                        <a href="<?php $options->index('Plugins/Edit.do?deactivate=' . $activatedPlugins->name); ?>"><?php _e('禁用'); ?></a>
                                    <?php else: ?>
                                        <a href="<?php $options->index('Plugins/Edit.do?activate=' . $activatedPlugins->name); ?>"><?php _e('激活'); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php endif; ?>
                
                <?php Typecho_Widget::widget('Widget_Plugins_List_Deactivated')->to($deactivatedPlugins); ?>
                <?php if ($deactivatedPlugins->have() || !$activatedPlugins->have()): ?>
                <h6 class="typecho-list-table-title">禁用的插件</h6>
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
                        <?php if ($deactivatedPlugins->have()): ?>
                        <?php while ($deactivatedPlugins->next()): ?>
                        <tr<?php $deactivatedPlugins->alt(' class="even"', ''); ?>>
                            <td><?php $deactivatedPlugins->title(); ?></td>
                            <td><?php $deactivatedPlugins->description(); ?></td>
                            <td><?php $deactivatedPlugins->version(); ?></td>
                            <td><?php echo empty($deactivatedPlugins->homepage) ? $deactivatedPlugins->author : '<a href="' . $deactivatedPlugins->homepage
                            . '">' . $deactivatedPlugins->author . '</a>'; ?></td>
                            <td>
                                <?php if ($deactivatedPlugins->activate): ?>
                                    <?php if ($deactivatedPlugins->activated): ?>
                                        <?php if ($deactivatedPlugins->config): ?>
                                            <a href="<?php $options->adminUrl('option-plugin.php?config=' . $deactivatedPlugins->name); ?>"><?php _e('配置'); ?></a> 
                                            | 
                                        <?php endif; ?>
                                        <a href="<?php $options->index('Plugins/Edit.do?deactivate=' . $deactivatedPlugins->name); ?>"><?php _e('禁用'); ?></a>
                                    <?php else: ?>
                                        <a href="<?php $options->index('Plugins/Edit.do?activate=' . $deactivatedPlugins->name); ?>"><?php _e('激活'); ?></a>
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
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
