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
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作：<a href="#">全选</a>，<a href="#">反选</a>，<a href="#">删除选中项</a></p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                    <select name="category">
                    	<option value=""><?php _e('所有分类'); ?></option>
                    	<?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($category); ?>
                    	<?php while($category->next()): ?>
                    	<option value="<?php $category->mid(); ?>"<?php if(Typecho_Request::getParameter('category') == $category->mid): ?> selected="true"<?php endif; ?>><?php $category->name(); ?></option>
                    	<?php endwhile; ?>
                    </select>
            
                    <select name="status">
                        <option value=""><?php _e('所有文章'); ?></option>
                        <option value="published"<?php if(Typecho_Request::getParameter('status') == 'published'): ?> selected="true"<?php endif; ?>><?php _e('已发布'); ?></option>
                        <option value="draft"<?php if(Typecho_Request::getParameter('status') == 'draft'): ?> selected="true"<?php endif; ?>><?php _e('草稿'); ?></option>
                    </select>
                    
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
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
                        <tr<?php $posts->alt('', ' class="even"'); ?>>
                            <td><input type="checkbox" value="<?php $posts->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('write-post.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a></td>
                            <td><?php $posts->author(); ?></td>
                            <td><?php $posts->dateWord(); ?></td>
                            <td><?php $posts->category(' | '); ?></td>
                            <td><?php $posts->commentsNum(_t('没有评论'), _t('仅有一条评论'), _t('%d条评论')); ?></td>
                            <td><?php if('post' == $posts->type):
                        _e('<a href="%s" title="在新页面打开" target="_blank">已发布</a>', $posts->permalink);
                        else:
                        _e('草稿');
                        endif;?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><?php _e('没有任何文章'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            
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
