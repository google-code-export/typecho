<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/post-list.php');
Typecho::widget('Menu')->setCurrentChild('/admin/manage-links.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Links</h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="Delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%">name</th>
					<th width="49%">description</th>
					<th width="30%">Url</th>
				</tr>
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">typecho.com</a></td>
					<td>official site</td>
					<td><a href="#">typecho.com</a></td>
				</tr>'; ?>
			</table>
			<hr class="space" />
			<h4>Add Links</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Link Name</label></td>
					<td><input type="text" id="" style="width: 50%;" /><small>The name is how the tag appears on your site.</small></td>
				</tr>
				<tr>
					<td><label>Url</label></td>
					<td><input type="text" id="" style="width: 50%;" /><small>The “slug” is the Url-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small></td>
				</tr>
				
				<tr>
					<td><label>description</label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 70%;"></textarea><small>The description is not prominent by default, however some themes may show it.</small></td>
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
