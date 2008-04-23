<?php 
require_once 'common.php';
widget('Menu')->setCurrentParent('/admin/general.php');
widget('Menu')->setCurrentChild('/admin/reading.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Reading Settings</h2>
		<div id="page">
			<form method="post" action="">
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Date &amp; Time Format</label></td>
					<td><input type="text" id="" style="width: 10%; margin-right: .5em;" value="F j, Y" /> / <input type="text" id="" style="width: 10%; margin-left: .5em;" value="g:i a" /><small>Get Support from <a href="http://php.net/date">PHP Manual</a></small></td>
				</tr>
				<tr>
					<td><label>Entry Listing Default</label></td>
					<td><input type="text" id="" style="width: 30%;" value="10" /></td>
				</tr>
				<tr>
					<td><label>Excerpt Length</label></td>
					<td><input type="text" id="" style="width: 30%;" value="0" /><small>You can use "&lt;!--more--&gt;" to instead if the value is "0"</small></td>
				</tr>
				<tr>
					<td><label>RSS Full Articles Layout</label></td>
					<td><input type="radio" id="" name="rss_full" checked="checked" /> Yes <input type="radio" id="" name="rss_full" style="margin-left: 1em;" /> No</td>
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
