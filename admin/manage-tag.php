<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/post-list.php');
Typecho::widget('Menu')->setCurrentChild('/admin/manage-tag.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php _e('管理标签'); ?></h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="<?php _e('删除'); ?>" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="60%">name</th>
					<th width="30%">slug</th>
					<th width="9%">post</th>
				</tr>
                <?php Typecho::widget('Metas', 'tag')->to($tag); ?>
				<?php while($tag->get()): ?>
				<tr>
					<td><input type="checkbox" id="<?php $tag->mid(); ?>" /></td>
					<td><a href="#"><?php $tag->name(); ?></a></td>
					<td><?php $tag->slug(); ?></td>
					<td><a href="<?php $tag->permalink(); ?>"><?php $tag->count(); ?></a></td>
				</tr>
                <?php endwhile; ?>
			</table>
			<hr class="space" />
			<h4>Add Tag</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Tag Name</label></td>
					<td><input type="text" id="" style="width: 50%;" /><small>The name is how the tag appears on your site.</small></td>
				</tr>
				<tr>
					<td><label>Tag Slug</label></td>
					<td><input type="text" id="" style="width: 50%;" /><small>The “slug” is the Url-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Add Tag" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
