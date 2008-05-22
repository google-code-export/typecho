<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
                <input type="button" onclick="window.location = '<?php Typecho::widget('Options')->adminUrl('/manage-tag.php'); ?>#edit'" value="<?php _e('增加标签'); ?>" />
				<input type="submit" value="<?php _e('删除'); ?>" />
                <input type="text" style="width: 120px;" value="<?php _e('请输入合并入的标签'); ?>" onclick="value='';name='merge';" />
                <input type="button" onclick="$('#category input[@name=do]').val('merge');category.submit();" value="<?php _e('合并'); ?>" />
                <input type="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                <input type="submit" value="<?php _e('过滤'); ?>" />
                <input type="hidden" name="do" value="delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="60%"><?php _e('标签名称'); ?></th>
					<th width="30%"><?php _e('标签缩略名'); ?></th>
					<th width="9%"><?php _e('文章'); ?></th>
				</tr>
                <?php Typecho::widget('Tags')->to($tag); ?>
				<?php while($tag->get()): ?>
				<tr>
					<td><input type="checkbox" id="<?php $tag->mid(); ?>" /></td>
					<td><a href="#"><?php $tag->name(); ?></a></td>
					<td><?php $tag->slug(); ?></td>
					<td><a href="<?php $tag->permalink(); ?>"><?php $tag->count(); ?></a></td>
				</tr>
                <?php endwhile; ?>
			</table>
            
            <?php if($tag->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $tag->pageNav('manage-tag.php'); ?>
			</div>
            <?php endif; ?>
            
			<hr class="space" />
			<h4 id="edit">Add Tag</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label for="name"><?php _e('标签名称'); ?></label></td>
					<td><input type="text" id="name" name="name" style="width: 50%;" /><small>The name is how the tag appears on your site.</small></td>
				</tr>
				<tr>
					<td><label for="slug"><?php _e('标签缩略名'); ?></label></td>
					<td><input type="text" id="slug" name="slug" style="width: 50%;" /><small>The “slug” is the Url-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small></td>
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
