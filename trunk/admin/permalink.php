<?php 
require_once 'common.php';
widget('Menu')->setCurrentParent('/admin/general.php');
widget('Menu')->setCurrentChild('/admin/permalink.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Permalink Settings</h2>
		<div id="page">
			<form method="post" action="">
			<table class="setting">
				<tr><th width="2%"><th width="20%"></th><th width="78%"></th></tr>
				<tr>
					<td><input type="radio" id="" name="" /></td>
					<td><label>Default</label></td>
					<td><code style="font-size: 1em;">http://yourdomain/archives/[post_id=%d]/</code></td>
				</tr>
				<tr>
					<td><input type="radio" id="" name="" /></td>
					<td><label>Categories and Name</label></td>
					<td><code style="font-size: 1em;">http://yourdomain/[category_postname=%a]/[post_name=%s].html</code></td>
				</tr>
				<tr>
					<td><input type="radio" id="" name="" /></td>
					<td><label>Day and Name</label></td>
					<td><code style="font-size: 1em;">http://yourdomain/[post_year=%d]/[post_month=%d]/[post_day=%d]/[post_name=%s].html</code></td>
				</tr>
				<tr>
					<td><input type="radio" id="" name="" /></td>
					<td><label>WordPress Style</label></td>
					<td><code style="font-size: 1em;">http://yourdomain/archives/[post_name=%s].html</code></td>
				</tr>
				<tr>
					<td><input type="radio" id="" name="" /></td>
					<td><label> Custom Structure</label></td>
					<td><input type="text" id="" style="width: 70%;" class="code" /><small><a href="#">Get Helps</a></small></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><input type="submit" value="Save Changes" /></td>
				</tr>
			</table>
			</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
