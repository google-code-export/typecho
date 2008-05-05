<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/general.php');
Typecho::widget('Menu')->setCurrentChild('/admin/general.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>General Settings</h2>
		<div id="page">
			<form method="post" action="">
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Site Name</label></td>
					<td><input type="text" id="" style="width: 70%;" /><small>Blog's name, such as "Very's Blog"</small></td>
				</tr>
				<tr>
					<td><label>Description</label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 90%;"></textarea><small>Say somthing about your blog</small></td>
				</tr>
				<tr>
					<td><label>Site URL</label></td>
					<td><input type="text" id="" style="width: 70%;" /><small>Your blog's url, end without "/"</small></td>
				</tr>
				<tr>
					<td><label>Keywords</label></td>
					<td><input type="text" id="" style="width: 70%;" /><small>Separated keywords with ","</small></td>
				</tr>
				<tr>
					<td><label>Default Language</label></td>
					<td><select id="" style="width: 20%;">
						<option value="" selected="selected">English</option>
						<option value="">Simple Chinese</option>
					</select></td>
				</tr>
				<tr>
					<td><label>Time Zone</label></td>
					<td><select id="" style="width: 20%;">
						<option value="" selected="selected">GMT +08:00</option>
						<option value="">GMT +09:00</option>
					</select></td>
				</tr>
				<tr>
					<td><label>Protect Files</label></td>
					<td><input type="radio" id="" name="pt" /> On <input type="radio" id="" name="pt" style="margin-left: 1em;" checked="checked" /> Off<small>Protection of the resources on your site from other sites theft</small></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Save Changes" /></td>
				</tr>
			</table>
			</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
