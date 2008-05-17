<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div id="page">
        <?php require_once 'notice.php'; ?>
        
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="<?php _e('删除'); ?>" />
				<select id="" style="width: 160px;">
                    <?php Typecho::widget('Query', 'from=table.metas&type=category&order=sort&sort=ASC')
                    ->parse('<option value="{mid}">{name}</option>'); ?>
				</select>
				<input type="submit" value="<?php _e('合并'); ?>" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%"><?php _e('分类名称'); ?></th>
					<th width="50%"><?php _e('分类描述'); ?></th>
					<th width="10%"><?php _e('文章'); ?></th>
					<th width="19%"><?php _e('分类缩略名'); ?></th>
				</tr>
                <?php Typecho::widget('Metas', 'category')->to($category); ?>
                <?php if($category->have()): ?>
                <?php while($category->get()): ?>
                <tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#"><?php $category->name(); ?></a></td>
					<td><?php $category->description(); ?></td>
					<td><a href="<?php Typecho::widget('Options')->adminUrl('post-list.php?status=allPost&category=' . $category->mid); ?>">
                    <?php $category->count(); ?></a></td>
					<td><?php $category->slug(); ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5"><?php _e('没有任何分类,请在下方添加'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
			<hr class="space" />
			<h4><?php _e('增加分类'); ?></h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label for="name"><?php _e('分类名称'); ?></label></td>
					<td><input type="text" name="name" id="name" style="width: 60%;" /></td>
				</tr>
				<tr>
					<td><label for="slug"><?php _e('分类缩略名'); ?></label></td>
					<td><input type="text" name="slug" id="slug" style="width: 60%;" />
                    <small><?php _e('分类缩略名用于创建友好的链接形式,请使用纯字母或者下划线.'); ?></small></td>
				</tr>
				<tr>
					<td><label for="description"><?php _e('分类描述'); ?></label></td>
					<td><textarea name="description" id="description" rows="5" style="width: 80%;"></textarea>
                    <small><?php _e('此文字用于描述分类,在有的主题中它会被显示.'); ?></small></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="<?php _e('增加分类'); ?>" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
