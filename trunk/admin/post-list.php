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
			<div class="table_nav">
            <form action="post-list.php">
				<input type="button" value="<?php _e('删除'); ?>" onclick="post.submit();" />
				<input type="text" name="keywords" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value=''" />
				<select name="category">
					<option value="" selected="selected"><?php _e('所有分类'); ?></option>
					<option value="">Design (6)</option>
				</select>
				<select name="status" style="width: 100px;">
					<option value="" selected="selected"><?php _e('我的所有文章'); ?></option>
					<option value=""><?php _e('我已发布的文章'); ?></option>
					<option value=""><?php _e('我的草稿'); ?></option>
				</select>
				<input type="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>
            
            <form method="post" name="post" id="post">
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
                    <td><a href="edit.php?cid=<?php $posts->cid(); ?>"><?php $posts->title(); ?></a></td>
                    <td><?php $posts->author(); ?></td>
                    <td><?php $posts->date(_t('y年n月j日 H时i分')); ?></td>
                    <td><?php $posts->category(); ?></td>
                    <td><?php $posts->commentsNum('%d'); ?></td>
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
			</table></form>
            
            <?php if($posts->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $posts->pageNav(); ?>
			</div>
            <?php endif; ?>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
