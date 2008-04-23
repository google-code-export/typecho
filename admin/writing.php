<?php 
require_once 'common.php';
widget('Menu')->setCurrentParent('/admin/general.php');
widget('Menu')->setCurrentChild('/admin/writing.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Writing Settings</h2>
		<div id="page">
			<form method="post" action="">
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Size of the post box</label></td>
					<td><input type="text" id="" style="width: 20%;" value="16" /><small>Editor's lines</small></td>
				</tr>
				<tr>
					<td><label>Default Post Category</label></td>
					<td><select id="" style="width: 20%;">
						<option value="" selected="selected">Uncategorized</option>
						<option value="">Category_1</option>
					</select></td>
				</tr>
				<tr>
					<td><label>Default Auto Save</label></td>
					<td><input type="radio" id="" name="auto_save" checked="checked" /> On <input type="radio" id="" name="auto_save" style="margin-left: 1em;" /> Off</td>
				</tr>
				<tr>
					<td><label>Default Accept Comments</label></td>
					<td><input type="radio" id="" name="acpt_cmt" checked="checked" /> Yes <input type="radio" id="" name="acpt_cmt" style="margin-left: 1em;" /> No</td>
				</tr>
				<tr>
					<td><label>Default Accept Trackbacks</label></td>
					<td><input type="radio" id="" name="acpt_tb" checked="checked" /> Yes <input type="radio" id="" name="acpt_tb" style="margin-left: 1em;" /> No</td>
				</tr>
				<tr>
					<td><label>Default Entry Status</label></td>
					<td><select id="" style="width: 15%;">
						<option value="" selected="selected">Published</option>
						<option value="">Unpublished</option>
					</select></td>
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
