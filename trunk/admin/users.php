<?php 
require_once 'common.php';
widget('Menu')->setCurrentParent('/admin/post-list.php');
widget('Menu')->setCurrentChild('/admin/users.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Users</h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="Delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="15%">username</th>
					<th width="25%">website</th>
					<th width="29%">e-mail</th>
					<th width="10%">rivileges</th>
					<th width="10">post</th>
				</tr>
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">Admin</a></td>
					<td><a href="#">http://example.com</a></td>
					<td><a href="#">admin@admin.com</a></td>
					<td>Publisher</td>
					<td><a href="#">10</a></td>
				</tr>'; ?>
			</table>
			<hr class="space" />
			<h4>Add User</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Username</label></td>
					<td><input type="text" id="" style="width: 40%;" /></td>
				</tr>
				<tr>
					<td><label>Password (twice)</label></td>
					<td><input type="password" id="" style="width: 22%; margin-right: 15px;" /><input type="password" id="" style="width: 22%;" /></td>
				</tr>
				<tr>
					<td><label>E-mail</label></td>
					<td><input type="text" id="" style="width: 40%;" /></td>
				</tr>
				<tr>
					<td><label>Website</label></td>
					<td><input type="text" id="" value="http://" style="width: 40%;" /></td>
				</tr>
				<tr>
					<td><label>About Yourself</label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 70%;"></textarea></td>
				</tr>
				<tr>
					<td><label>Privileges</label></td>
					<td>
					<select id="" style="width: 160px;">
						<option value="" selected="selected">Administrator</option>
						<option value="">Author</option>
					</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Add User" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
