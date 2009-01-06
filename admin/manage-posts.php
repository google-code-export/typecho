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
                    <li<?php if(!Typecho_Request::isSetParameter('status') || 'publish' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php'); ?>"><?php _e('已发布'); ?></a></li>
                    <li<?php if('draft' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php?status=draft'); ?>"><?php _e('草稿'); ?>
                    <?php if('on' != Typecho_Request::getParameter('__typecho_all_posts') && $stat->myDraftPostsNum > 0): ?> 
                        <span class="balloon"><?php $stat->myDraftPostsNum(); ?></span>
                    <?php elseif('on' == Typecho_Request::getParameter('__typecho_all_posts') && $stat->draftPostsNum > 0): ?>
                        <span class="balloon"><?php $stat->draftPostsNum(); ?></span>
                    <?php endif; ?>
                    </a></li>
                    <li<?php if('waiting' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php?status=waiting'); ?>"><?php _e('待审核'); ?>
                    <?php if('on' != Typecho_Request::getParameter('__typecho_all_posts') && $stat->myWaitingPostsNum > 0): ?> 
                        <span class="balloon"><?php $stat->myWaitingPostsNum(); ?></span>
                    <?php elseif('on' == Typecho_Request::getParameter('__typecho_all_posts') && $stat->waitingPostsNum > 0): ?>
                        <span class="balloon"><?php $stat->waitingPostsNum(); ?></span>
                    <?php endif; ?>
                    </a></li>
                    <?php if($user->pass('editor', true)): ?>
                        <li class="right<?php if('on' == Typecho_Request::getParameter('__typecho_all_posts')): ?> current<?php endif; ?>"><a href="<?php echo Typecho_Request::uri('__typecho_all_posts=on'); ?>"><?php _e('所有'); ?></a></li>
                        <li class="right<?php if('on' != Typecho_Request::getParameter('__typecho_all_posts')): ?> current<?php endif; ?>"><a href="<?php echo Typecho_Request::uri('__typecho_all_posts=off'); ?>"><?php _e('我的'); ?></a></li>
                    <?php endif; ?>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                        <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                        <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                        <?php _e('选中项'); ?>: 
                        <span rel="delete" class="operate-button typecho-table-select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                    <select name="category">
                    	<option value=""><?php _e('所有分类'); ?></option>
                    	<?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($category); ?>
                    	<?php while($category->next()): ?>
                    	<option value="<?php $category->mid(); ?>"<?php if(Typecho_Request::getParameter('category') == $category->mid): ?> selected="true"<?php endif; ?>><?php $category->name(); ?></option>
                    	<?php endwhile; ?>
                    </select>
                    <button type="submit"><?php _e('筛选'); ?></button>
                    <?php if(Typecho_Request::isSetParameter('status')): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars(Typecho_Request::getParameter('status')); ?>" name="status" />
                    <?php endif; ?>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_posts" class="operate-form" action="<?php $options->index('Contents/Post/Edit.do'); ?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="350"/>
                        <col width="125"/>
                        <col width="200"/>
                        <col width="250"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th><?php _e('标题'); ?></th>
                            <th><?php _e('作者'); ?></th>
                            <th><?php _e('发布日期'); ?></th>
                            <th class="typecho-radius-topright"><?php _e('分类'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Contents_Post_Admin')->to($posts); ?>
                    	<?php if($posts->have()): ?>
                        <?php while($posts->next()): ?>
                        <tr<?php $posts->alt(' class="even"', ''); ?> id="<?php $posts->theId(); ?>">
                            <td><input type="checkbox" value="<?php $posts->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $posts->permalink(); ?>" class="balloon-button right <?php
                            switch (true) {
                                case 0 == $posts->commentsNum:
                                    echo 'size-0';
                                    break;
                                case 0 < $posts->commentsNum && $posts->commentsNum < 10:
                                    echo 'size-1';
                                    break;
                                case 10 <= $posts->commentsNum && $posts->commentsNum < 20:
                                    echo 'size-2';
                                    break;
                                case 20 <= $posts->commentsNum && $posts->commentsNum < 50:
                                    echo 'size-3';
                                    break;
                                case 50 <= $posts->commentsNum && $posts->commentsNum < 100:
                                    echo 'size-4';
                                    break;
                                case 100 <= $posts->commentsNum:
                                    echo 'size-5';
                                    break;
                            }
                            ?>"><?php $posts->commentsNum(); ?></a></td>
                            <td><a href="<?php $options->adminUrl('write-post.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a></td>
                            <td><?php $posts->author(); ?></td>
                            <td><?php $posts->dateWord(); ?></td>
                            <td><?php $posts->category(', '); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><h6 class="typecho-list-table-title"><?php _e('没有任何文章'); ?></h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            
            <?php if($posts->have()): ?>
            <div class="typecho-pager">
                <div class="typecho-pager-content">
                    <h5><?php _e('页面'); ?>:&nbsp;</h5>
                    <ul>
                        <?php $posts->pageNav(); ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
