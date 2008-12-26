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
                    
                    <?php if(!Typecho_Request::isSetParameter('type') || 'category' == Typecho_Request::getParameter('type')): ?>
                    <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($categories); ?>
                    <form method="post" name="manage_categories" class="operate-form" action="<?php $options->index('Metas/Category/Edit.do'); ?>">
                    <div class="typecho-list-operate">
                        <p class="operate"><?php _e('操作'); ?>: 
                            <span onclick="typechoOperate('.typecho-list-table', 'selectAll');" class="operate-button select-all"><?php _e('全选'); ?></span>, 
                            <span onclick="typechoOperate('.typecho-list-table', 'selectNone');" class="operate-button select-reverse"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                            <?php _e('选中项'); ?>: 
                            <span onclick="typechoSubmit('form[name=manage_categories]', 'input[name=do]', 'delete');" class="operate-button select-submit"><?php _e('删除'); ?></span>, 
                            <span onclick="typechoSubmit('form[name=manage_categories]', 'input[name=do]', 'merge');" class="operate-button select-submit"><?php _e('合并到'); ?></span>
                            <select name="merge">
                                <?php $categories->parse('<option value="{mid}">{name}</option>'); ?>
                            </select>
                        </p>
                    </div>
                    
                    <table class="typecho-list-table">
                        <colgroup>
                            <col width="25"/>
                            <col width="150"/>
                            <col width="50"/>
                            <col width="150"/>
                            <col width="350"/>
                            <col width="85"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="typecho-radius-topleft"> </th>
                                <th><?php _e('名称'); ?></th>
                                <th> </th>
                                <th><?php _e('缩略名'); ?></th>
                                <th><?php _e('描述'); ?></th>
                                <th class="typecho-radius-topright"><?php _e('文章数'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($categories->have()): ?>
                            <?php while ($categories->next()): ?>
                            <tr<?php $categories->alt(' class="even"', ''); ?>>
                                <td><input type="checkbox" value="<?php $categories->mid(); ?>" name="mid[]"/></td>
                                <td><a href="<?php echo Typecho_Request::uri('mid=' . $categories->mid); ?>"><?php $categories->name(); ?></a></td>
                                <td>
                                <?php if ($options->defaultCategory == $categories->mid): ?>
                                <span class="right description"><?php _e('默认'); ?></span>
                                <?php else: ?>
                                <span class="right hidden-by-mouse"><a href="<?php $options->index('Metas/Category/Edit.do?do=default&mid=' . $categories->mid); ?>"><?php _e('默认'); ?></a></span>
                                <?php endif; ?>
                                </td>
                                <td><?php $categories->slug(); ?></td>
                                <td><?php $categories->description(); ?></td>
                                <td><?php $categories->count(); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr class="even">
                                <td colspan="5"><h6 class="typecho-list-table-title"><?php _e('没有任何分类'); ?></h6></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="do" value="delete" />
                    </form>
                    <?php else: ?>
                    <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
                    <div class="typecho-list-operate">
                        <p class="operate"><?php _e('操作'); ?>: 
                            <span onclick="typechoOperate('.typecho-list-notable', 'selectAll');" class="operate-button select-all"><?php _e('全选'); ?></span>, 
                            <span onclick="typechoOperate('.typecho-list-notable', 'selectNone');" class="operate-button select-reverse"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                            <?php _e('选中项'); ?>: 
                            <span onclick="typechoSubmit('form[name=manage_tags]', 'input[name=do]', 'delete');" class="operate-button select-submit"><?php _e('删除'); ?></span>
                        </p>
                    </div>
                    
                    <form method="post" name="manage_tags" class="operate-form" action="<?php $options->index('Metas/Tag/Edit.do'); ?>">
                    <ul class="typecho-list-notable tag-list clearfix typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                        <?php if($tags->have()): ?>
                        <?php while ($tags->next()): ?>
                        <li class="<?php $tags->split('size-1', 'size-2', 'size-3', 'size-4', 'size-5'); ?>">
                        <input type="checkbox" value="<?php $tags->mid(); ?>" name="mid[]"/>
                        <a href="<?php echo Typecho_Request::uri('mid=' . $tags->mid); ?>"><?php $tags->name(); ?></a>
                        </li>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <h6 class="typecho-list-table-title"><?php _e('没有任何标签'); ?></h6>
                        <?php endif; ?>
                    </ul>
                    <input type="hidden" name="do" value="delete" />
                    </form>
                    <?php endif; ?>
                    
                </div>
                <div class="column-08 start-17 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                    <?php if(!Typecho_Request::isSetParameter('type') || 'category' == Typecho_Request::getParameter('type')): ?>
                        <?php Typecho_Widget::widget('Widget_Metas_Category_Edit')->form()->render(); ?>
                    <?php else: ?>
                        <?php Typecho_Widget::widget('Widget_Metas_Tag_Edit')->form()->render(); ?>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
