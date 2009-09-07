<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <ul class="typecho-option-tabs">
                    <li<?php if(!isset($request->status) || 'publish' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php'); ?>"><?php _e('已发布'); ?></a></li>
                    <li<?php if('draft' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php?status=draft'); ?>"><?php _e('草稿'); ?>
                    <?php if($stat->draftPagesNum > 0): ?> 
                        <span class="balloon"><?php $stat->draftPagesNum(); ?></span>
                    <?php endif; ?>
                    </a></li>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                        <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                        <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                        <?php _e('选中项'); ?>: 
                        <span rel="delete" lang="<?php _e('你确认要删除这些页面吗?'); ?>" class="operate-button operate-delete typecho-table-select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />            
                    <?php if(isset($request->status)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($request->get('status')); ?>" name="status" />
                    <?php endif; ?>
                    
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_pages" class="operate-form" action="<?php $options->index('/action/contents-page-edit'); ?>">
                <table class="typecho-list-table draggable">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="355"/>
                        <col width="30"/>
                        <col width="180"/>
                        <col width="120"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th><?php _e('标题'); ?></th>
                            <th> </th>
                            <th><?php _e('缩略名'); ?></th>
                            <th><?php _e('作者'); ?></th>
                            <th class="typecho-radius-topright"><?php _e('发布日期'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Contents_Page_Admin')->to($pages); ?>
                    	<?php if($pages->have()): ?>
                        <?php while($pages->next()): ?>
                        <tr<?php $pages->alt(' class="even"', ''); ?> id="<?php $pages->theId(); ?>">
                            <td><input type="checkbox" value="<?php $pages->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('manage-comments.php?cid=' . $pages->cid); ?>" class="balloon-button right size-<?php echo Typecho_Common::splitByCount($pages->commentsNum, 1, 10, 20, 50, 100); ?>"><?php $pages->commentsNum(); ?></a></td>
                            <td><a href="<?php $options->adminUrl('write-page.php?cid=' . $pages->cid); ?>"><?php $pages->title(); ?></a></td>
                            <td>
                            <?php if ('publish' == $pages->status): ?>
                            <a class="right hidden-by-mouse" href="<?php $pages->permalink(); ?>"><img src="<?php $options->adminUrl('images/view.gif'); ?>" title="<?php _e('浏览 %s', $pages->title); ?>" width="16" height="16" alt="view" /></a>
                            <?php endif; ?>
                            </td>
                            <td><?php $pages->slug(); ?></td>
                            <td><?php $pages->author(); ?></td>
                            <td><?php $pages->dateWord(); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><h6 class="typecho-list-table-title"><?php _e('没有任何页面'); ?></h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>

<?php if(!isset($request->status) || 'publish' == $request->get('status')): ?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            Typecho.Table.dragStop = function (item, result) {
                var _r = new Request.JSON({
                    url: '<?php $options->index('/action/contents-page-edit'); ?>'
                }).send(result + '&do=sort');
            };
        });
    })();
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
