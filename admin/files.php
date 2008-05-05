<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/post-list.php');
Typecho::widget('Menu')->setCurrentChild('/admin/files.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Files</h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="Delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="15%">name</th>
					<th width="59%">description</th>
					<th width="15%">date Added</th>
					<th width="10%">size</th>
				</tr>
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">screenshot.png</a></td>
					<td><a href="#">test</a></td>
					<td>2008-01-01 00:00</td>
					<td>512 KB</td>
				</tr>'; ?>
			</table>
			<hr class="space" />
			<h4>Upload Files</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Upload Files</label></td>
					<td><input type="file" id="" /></td>
				</tr>
				<tr>
					<td><label>Description</label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 70%;"></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Upload" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
