<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>外观设置</h2>
		<div id="page">
			<table class="setting">
				<tr><th width="10%" /><th width="70%" /><th width="20%" /></tr>
				<tr>
					<td><a href="#"><img src="images/default_theme.png" alt="" class="theme" /></a></td>
					<td><h5><a href="#">Theme Name</a></h5><p>Simple &amp; Clean Theme. Last updated at March 30th, 2008. The default WordPress theme based on the famous Kubrick.<small>作者：<a href="#">Typecho</a>，版本：1.0，所在路径：<code>/usr/themes/default</code></small></p></td><td>当前模板</td>
				</tr>

				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><a href="#"><img src="images/default_theme.png" alt="" class="theme" /></a></td>
					<td><h5><a href="#">Theme Name</a></h5><p>Simple &amp; Clean Theme. Last updated at March 30th, 2008. The default WordPress theme based on the famous Kubrick.</p></td><td><a href="#">使用该模板</a></td>
				</tr>'; ?>
			</table>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
