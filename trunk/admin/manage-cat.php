<?php 
require_once 'common.php';
widget('Menu')->setCurrentParent('/admin/post-list.php');
widget('Menu')->setCurrentChild('/admin/manage-cat.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Categories</h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="Delete" />
				<select id="" style="width: 160px;">
					<option value="" selected="selected">default</option>
					<option value="">Design</option>
				</select>
				<input type="submit" value="Merge" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%">name</th>
					<th width="50%">description</th>
					<th width="10%">post</th>
					<th width="19%">slug</th>
				</tr>
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">sample</a></td>
					<td>The textarea in the comment form seems be in the extreme left. Any suggestions to fix it?</td>
					<td><a href="#">10</a></td>
					<td>default</td>
				</tr>'; ?>
			</table>
			<hr class="space" />
			<h4>Add Category</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Category Name</label></td>
					<td><input type="text" id="" style="width: 60%;" /></td>
				</tr>
				<tr>
					<td><label>Category Slug</label></td>
					<td><input type="text" id="" style="width: 60%;" /><small>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small></td>
				</tr>
				<tr>
					<td><label>Category description</label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 80%;"></textarea><small>The description is not prominent by default, however some themes may show it.</small></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Add Category" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
