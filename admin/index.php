<?php
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<a class="botton right" href="#">2 Comments Awaiting</a><a class="botton right" href="#">Write a New Post</a><h2>Welcome to Typecho</h2>
		<div style="width: 49%" class="left">
			<h3>Most Recent Weblog Entires</h3>
			<table class="latest">
				<tr>
					<th width="30%">date</th>
					<th width="70%">entries</th>
				</tr>
				<tr>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Example post title</a></td>
				</tr>
				<tr>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Example post title</a></td>
				</tr>
			</table>

			<h3>Most Recent Comments/Trackbacks</h3>
			<table class="latest">
				<tr>
					<th width="30%">date</th>
					<th width="70%">entries</th>
				</tr>
				<tr>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Example post title</a></td>
				</tr>
				<tr>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Example post title</a></td>
				</tr>
			</table>
		</div>
		<div style="width: 49%" class="right">
			<h3>Site Statistics</h3>
			<table class="latest">
				<tr>
					<th width="70%">Name</th>
					<th width="30%">Value</th>
				</tr>
				<tr>
					<td>Version</td>
					<td>1.0</td>
				</tr>
				<tr>
					<td>Total Weblog Entries</td>
					<td>2</td>
				</tr>
				<tr>
					<td>Total Comments</td>
					<td>2</td>
				</tr>
				<tr>
					<td>Total Trackbacks</td>
					<td>2</td>
				</tr>
				<tr>
					<td>Comments Awaiting Validation</td>
					<td>2</td>
				</tr>
				<tr>
					<td>Spam Comments</td>
					<td>2</td>
				</tr>
			</table>

			<h3>Official News</h3>
			<table class="latest">
				<tr>
					<th width="30%">date</th>
					<th width="70%">entries</th>
				</tr>
				<tr>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Example post title</a></td>
				</tr>
				<tr>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Example post title</a></td>
				</tr>
			</table>
		</div>
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
