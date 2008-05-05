<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/post-list.php');
Typecho::widget('Menu')->setCurrentChild('/admin/manage-tag.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Tags</h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="Delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="80%">name</th>
					<th width="10%">slug</th>
					<th width="9%">post</th>
				</tr>
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">sample</a></td>
					<td>sample</td>
					<td><a href="#">10</a></td>
				</tr>'; ?>
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
					<td><input type="text" id="" style="width: 50%;" /><small>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small></td>
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
