<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/post-list.php');
Typecho::widget('Menu')->setCurrentChild('/admin/comment-list.php');
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Comments</h2>
		<div id="page">
			<div class="table_nav">
				<a href="#" class="botton right">Awaiting Moderation (2)</a>
				<input type="submit" value="Approve" />
				<input type="submit" value="Mark as Span" />
				<input type="submit" value="Unapprove" />
				<input type="submit" value="Delete" />
				<input type="text" id="" style="width: 200px;" value="Keywords" onclick="value=''" />
				<input type="submit" value="Filter" />
			</div>

			<table id="comment_list" class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="10%">date</th>
					<th width="7%">name</th>
					<th width="53%">comment</th>
					<th width="22%">parent</th>
					<th width="7%">status</th>
				</tr>
				<?php for($a=0;$a!=10;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Fen</a></td>
					<td><p>The textarea in the comment form seems be in the extreme left. Any suggestions to fix it?</p></td>
					<td><a href="#">How to cultivate mad-hot creative flow</a></td>
					<td>Approve</td>
				</tr>'; ?>
			</table>

			<div class="table_nav page_nav">
				Pages: <a href="#">&lt;</a> <a href="#">1</a> <a class="select" href="#">2</a> ... <a href="#">9</a> <a href="#">10</a> <a href="#">&gt;</a>
			</div>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
