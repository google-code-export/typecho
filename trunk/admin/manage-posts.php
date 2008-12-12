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
                <ul class="typecho-option-tabs">
                    <li<?php if(!Typecho_Request::isSetParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php'); ?>"><?php _e('所有'); ?></a></li>
                    <li<?php if('published' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php?status=published'); ?>"><?php _e('已发布'); ?></a></li>
                    <li<?php if('waiting' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php?status=waiting'); ?>"><?php _e('待审核'); ?></a></li>
                    <li<?php if('draft' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-posts.php?status=draft'); ?>"><?php _e('草稿'); ?></a></li>
                </ul>
            
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作: 
                        <span onclick="typechoOperate('.typecho-list-table', 'selectAll');" class="operate-button select-all">全选</span>, 
                        <span onclick="typechoOperate('.typecho-list-table', 'selectNone');" class="operate-button select-reverse">不选</span>, 
                        <span class="operate-button select-submit">删除选中项</span><?php if($user->pass('editor', true)):
                        if('yes' == Typecho_Request::getParameter('seeAll')): ?>, <a href="?seeAll=no">查看我的文章</a>
                        <?php else: ?>, <a href="?seeAll=yes">查看所有人的文章</a><?php endif;
                        endif; ?>
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
            
                    <?php if(Typecho_Request::isSetParameter('status')): ?>
                        <input type="hidden" value="<?php echo Typecho_Request::getParameter('status'); ?>" name="status" />
                    <?php endif; ?>
                    
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <form method="post" class="operate-form">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="400"/>
                        <col width="100"/>
                        <col width="125"/>
                        <col width="150"/>
                        <col width="100"/>
                        <col width="100"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th> </th>
                            <th><?php _e('标题'); ?></th>
                            <th><?php _e('作者'); ?></th>
                            <th><?php _e('发布日期'); ?></th>
                            <th><?php _e('分类'); ?></th>
                            <th><?php _e('评论'); ?></th>
                            <th><?php _e('状态'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Contents_Post_Admin')->to($posts); ?>
                    	<?php if($posts->have()): ?>
                        <?php while($posts->next()): ?>
                        <tr<?php $posts->alt(' class="even"', ''); ?>>
                            <td><input type="checkbox" value="<?php $posts->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('write-post.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a></td>
                            <td><?php $posts->author(); ?></td>
                            <td><?php $posts->dateWord(); ?></td>
                            <td><?php $posts->category(' | '); ?></td>
                            <td><?php $posts->commentsNum(_t('没有评论'), _t('仅有一条评论'), _t('%d条评论')); ?></td>
                            <td><?php if('post' == $posts->type):
                        _e('<a href="%s">已发布</a>', $posts->permalink);
                        elseif('waiting' == $posts->type):
                        _e('待审核');
                        else:
                        _e('草稿');
                        endif;?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><h6 class="typecho-list-table-title"><?php _e('没有任何文章'); ?></h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
