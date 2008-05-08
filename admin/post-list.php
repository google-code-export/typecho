<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/post-list.php');
Typecho::widget('Menu')->setCurrentChild('/admin/post-list.php');
require_once 'header.php';
require_once 'menu.php';
Typecho::widget('contents.AdminPosts')->to($posts);
?>

	<div id="main">
		<h2><?php _e('管理文章'); ?></h2>
		<div id="page">
            <?php require_once 'notice.php'; ?>
            
			<div class="table_nav">
            <form action="<?php Typecho::widget('Options')->adminUrl('post-list.php'); ?>">
				<input type="button" value="<?php _e('删除'); ?>" onclick="post.submit();" />
				<input type="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<select name="category">
					<option value="" selected="selected"><?php _e('所有分类'); ?></option>
					<option value="">Design (6)</option>
				</select>
				<select name="status">
					<option value="my" <?php TypechoRequest::callParameter('status', 'my', 'selected="selected"'); ?>><?php _e('我的所有文章'); ?></option>
					<option value="myPost" <?php TypechoRequest::callParameter('status', 'myPost', 'selected="selected"'); ?>><?php _e('我的已发布文章'); ?></option>
					<option value="myDraft" <?php TypechoRequest::callParameter('status', 'myDraft', 'selected="selected"'); ?>><?php _e('我的草稿'); ?></option>
                    <?php if(Typecho::widget('Access')->pass('editor', true)): ?>
                    <option value="all" <?php TypechoRequest::callParameter('status', 'all', 'selected="selected"'); ?>><?php _e('所有文章'); ?></option>
					<option value="allPost" <?php TypechoRequest::callParameter('status', 'allPost', 'selected="selected"'); ?>><?php _e('所有已发布的文章'); ?></option>
					<option value="allDraft" <?php TypechoRequest::callParameter('status', 'allDraft', 'selected="selected"'); ?>><?php _e('所有草稿'); ?></option>
                    <?php endif; ?>
				</select>
				<input type="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>
            
            <form method="post" name="post" id="post" action="<?php Typecho::widget('Options')->index('DoEditPost.do'); ?>">
			<table class="latest">
				<tr>
					<th width="2%"><input type="checkbox" /></th>
					<th width="35%"><?php _e('标题'); ?></th>
					<th width="10%"><?php _e('作者'); ?></th>
					<th width="20%"><?php _e('发布日期'); ?></th>
					<th width="15%"><?php _e('分类'); ?></th>
					<th width="10%"><?php _e('评论'); ?></th>
					<th width="8%"><?php _e('状态'); ?></th>
				</tr>
                <?php if($posts->have()): ?>
                <?php while($posts->get()): ?>
                <tr>
                    <td><input type="checkbox" name="cid[]" value="<?php $posts->cid(); ?>" /></td>
                    <td><a href="<?php Typecho::widget('Options')->adminUrl('/edit.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a></td>
                    <td><?php $posts->author(); ?></td>
                    <td><?php $posts->date(_t('y年n月j日 H时i分')); ?></td>
                    <td><?php $posts->category(); ?></td>
                    <td><?php $posts->commentsNum('没有评论', '仅有一条评论', '%d条评论'); ?></td>
                    <td><?php if('post' == $posts->type):
                    _e('已发布');
                    else:
                    _e('草稿');
                    endif;?></th>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7"><?php _e('对不起,没有找到任何记录'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
            <input type="hidden" name="do" value="delete"/>
            </form>
            
            <?php if($posts->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $posts->pageNav(); ?>
			</div>
            <?php endif; ?>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
