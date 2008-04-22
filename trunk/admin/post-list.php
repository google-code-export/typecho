<?php 
require_once 'common.php';
require_once 'header.php';
widget('Menu')->setCurrentParent('/admin/post-list.php');
widget('Menu')->setCurrentChild('/admin/post-list.php');
require_once 'menu.php';
?>

	<div id="main">
		<h2>Manage Posts</h2>
		<div id="page">
			<div class="table_nav">
				<input type="submit" value="Delete" />
				<input type="text" id="" style="width: 200px;" value="Keywords" onclick="value=''" />
				<select id="" style="width: 160px;">
					<option value="" selected="selected">View All Categories</option>
					<option value="">Design (6)</option>
				</select>
				<select id="" style="width: 100px;">
					<option value="" selected="selected">All Status</option>
					<option value="">Published</option>
					<option value="">Unpublished</option>
				</select>
				<input type="submit" value="Filter" />
			</div>

			<table class="latest">
				<tr>
					<th width="2%"><input type="checkbox" id="" /></th>
					<th width="40%">title</th>
					<th width="10%">author</th>
					<th width="15%">date</th>
					<th width="15%">categories</th>
					<th width="10%">comments</th>
					<th width="8%">status</th>
				</tr>
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">Switching to Linux: The GNOME desktop environment</a></td>
					<td><a href="#">Admin</a></td>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Design</a>, <a href="#">Theme</a></td>
					<td><a href="#">10</a></td>
					<td><a href="#">Published</a></td>
				</tr>'; ?>
				<!-- Comments Start -->
				<tr style="background: #fff;">
					<td colspan="7" style="padding: 0; border: none;">
						<table id="comment_list" class="right" style="margin: 1em 0;">
							<tr>
								<th width="65%">comment</th>
								<th width="15%">date</th>
								<th width="20%">actions</th>
							</tr>
							<tr><td colspan="3" style="background: #FFA;"><strong><a href="#">2 Comments Awaiting Moderation</a></strong></td></tr>
							<tr><td colspan="3" style="background: #A00;"><strong><a style="color: #fff;" href="#">3 Junk Comments</a></strong></td></tr>
							<?php for($a=0;$a!=5;$a++) echo'
							<tr>
								<td><p style="color: #C5D8EB;"><strong><a href="#"><img src="images/default-userpic.jpg" class="cmt_author left" alt="" /> Fen</a></strong><br /><a href="#">http://hellowiki.com</a> | <a href="#">fenbox@msn.com</a> | <a href="#">127.0.0.1</a></p><p>The textarea in the comment form seems be in the extreme left. Any suggestions to fix it?</p></td>
								<td>2008-03-26 16:00</td>
								<td style="color: #C5D8EB;"><a href="#">Approve</a> | <a href="#">Unapprove</a> | <a href="#">Spam</a> | <a href="#">Delete</a></td>
							</tr>'; ?>
							<tr><td colspan="3"><strong><a href="#">See More (14) &raquo;</a></strong></td></tr>
						</table>
					</td>
				</tr>
				<!-- Comments End -->
				<?php for($a=0;$a!=5;$a++) echo'
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">Switching to Linux: The GNOME desktop environment</a></td>
					<td><a href="#">Admin</a></td>
					<td>2008-03-26 16:00</td>
					<td><a href="#">Design</a>, <a href="#">Theme</a></td>
					<td><a href="#">10</a></td>
					<td><a href="#">Published</a></td>
				</tr>'; ?>
			</table>
			<hr class="space" />

			<div class="table_nav page_nav">
				Pages: <a href="#">&lt;</a> <a href="#">1</a> <a class="select" href="#">2</a> ... <a href="#">9</a> <a href="#">10</a> <a href="#">&gt;</a>
			</div>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
