<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
?>

<?php Typecho_Widget::widget('Widget_Contents_Attachment_Admin')->to($attachments); ?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01">
                
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                        <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                        <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                        <?php _e('选中项'); ?>: 
                        <span rel="delete" lang="<?php _e('你确认要删除这些附件吗?'); ?>" class="operate-button operate-delete typecho-table-select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <?php if ('' != $request->keywords): ?>
                    <a href="<?php $options->adminUrl('manage-medias.php'); ?>"><?php _e('&laquo; 取消筛选'); ?></a>
                    <?php endif; ?>
                    <input type="text" value="<?php '' != $request->keywords ? print(htmlspecialchars($request->keywords)) : _e('请输入关键字'); ?>"<?php if ('' == $request->keywords): ?> onclick="value='';name='keywords';" <?php else: ?> name="keywords"<?php endif; ?>/>
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_medias" class="operate-form" action="<?php $options->index('/action/contents-attachment-edit'); ?>">
                <table class="typecho-list-table draggable">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="20"/>
                        <col width="275"/>
                        <col width="30"/>
                        <col width="120"/>
                        <col width="220"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th> </th>
                            <th><?php _e('文件名'); ?></th>
                            <th> </th>
                            <th><?php _e('上传者'); ?></th>
                            <th><?php _e('所属文章'); ?></th>
                            <th class="typecho-radius-topright"><?php _e('发布日期'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php if($attachments->have()): ?>
                        <?php while($attachments->next()): ?>
                        <?php $mime = Typecho_Common::mimeIconType($attachments->attachment->mime); ?>
                        <tr<?php $attachments->alt(' class="even"', ''); ?> id="<?php $attachments->theId(); ?>">
                            <td><input type="checkbox" value="<?php $attachments->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('manage-comments.php?cid=' . $attachments->cid); ?>" class="balloon-button right size-<?php echo Typecho_Common::splitByCount($attachments->commentsNum, 1, 10, 20, 50, 100); ?>"><?php $attachments->commentsNum(); ?></a></td>
                            <td><span class="typecho-mime typecho-mime-<?php echo $mime; ?>"></span></td>
                            <td><a href="<?php $options->adminUrl('media.php?cid=' . $attachments->cid); ?>"><?php $attachments->title(); ?></a></td>
                            <td>
                            <a class="right hidden-by-mouse" href="<?php $attachments->permalink(); ?>"><img src="<?php $options->adminUrl('images/view.gif'); ?>" title="<?php _e('浏览 %s', $attachments->title); ?>" width="16" height="16" alt="view" /></a>
                            </td>
                            <td><?php $attachments->author(); ?></td>
                            <td>
                            <?php if ($attachments->parentPost->cid): ?>
                            <a href="<?php $options->adminUrl('write-' . $attachments->parentPost->type . '.php?cid=' . $attachments->parentPost->cid); ?>"><?php $attachments->parentPost->title(); ?></a>
                            <?php else: ?>
                            <span class="description"><?php _e('未归档'); ?></span>
                            <?php endif; ?>
                            </td>
                            <td><?php $attachments->dateWord(); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="8"><h6 class="typecho-list-table-title"><?php _e('没有任何附件'); ?></h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
                
                <?php if($attachments->have()): ?>
            <div class="typecho-pager">
                <div class="typecho-pager-content">
                    <ul>
                        <?php $attachments->pageNav(); ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
