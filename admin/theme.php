<?php 
require_once 'common.php';
require_once 'header.php';
widget('Menu')->setCurrentParent('/admin/index.php');
widget('Menu')->setCurrentChild('/admin/theme.php');
require_once 'menu.php';
?>

	<div id="main">
		<h2>Setting Themes</h2>
		<div id="page">
			<table class="setting">
				<tr><th width="10%"></th><th width="40%"></th><th width="10%"></th><th width="40%"></th></tr>

				<tr>
					<td><a href="#"><img src="images/default_theme.png" alt="" class="theme" /></a></td>
					<td><img src="images/checked.png" class="right" alt="" /><h5><a href="#">Theme Name</a></h5><p>Simple & Clean Theme. Last updated at March 30th, 2008. The default WordPress theme based on the famous Kubrick.</p><p><a href="#">Edit this theme</a></p></td>

					<td><a href="#"><img src="images/default_theme.png" alt="" class="theme" /></a></td>
					<td><h5><a href="#">Theme Name</a></h5><p>Simple & Clean Theme. Last updated at March 30th, 2008. The default WordPress theme based on the famous Kubrick.</p><p><a href="#">Edit this theme</a></p></td>
				</tr>

				<?php for($a=0;$a!=2;$a++) echo'
				<tr>
					<td><a href="#"><img src="images/default_theme.png" alt="" class="theme" /></a></td>
					<td><h5><a href="#">Theme Name</a></h5><p>Simple & Clean Theme. Last updated at March 30th, 2008. The default WordPress theme based on the famous Kubrick.</p><p><a href="#">Edit this theme</a></p></td>

					<td><a href="#"><img src="images/default_theme.png" alt="" class="theme" /></a></td>
					<td><h5><a href="#">Theme Name</a></h5><p>Simple & Clean Theme. Last updated at March 30th, 2008. The default WordPress theme based on the famous Kubrick.</p><p><a href="#">Edit this theme</a></p></td>
				</tr>'; ?>
			</table>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
