<?php include( 'header.php' ); ?>

	<div id="main">
		<h2>Welcome to Typecho</h2>
		<div id="content">
			<a class="botton right" href="#">Write a New Post</a><a class="botton right" href="#">Write a New Page</a><h3>Most Recent Weblog Entries</h3>
			<table class="latest">
				<tr>
					<th width="20%">author</th>
					<th width="80%">entries</th>
				</tr>
				<tr>
					<td><strong>Author_1</strong><br />2008-03-26 16:00</td>
					<td><a href="#">Example post title</a><br />Hi, this is a comment. To delete a comment, just log in and view the post's comments.</td>
				</tr>
				<tr>
					<td><strong>Author_2</strong><br />2008-03-26 16:00</td>
					<td><a href="#">Example post title</a><br />Hi, this is a comment. To delete a comment, just log in and view the post's comments.</td>
				</tr>
			</table>
			<hr class="space" style="height: 2em;" />

			<a class="botton right" href="#">2 comments awaiting moderation</a><h3>Most Recent Comments/TB</h3>
			<table class="latest">
				<tr>
					<th width="20%">author</th>
					<th width="80%">comments</th>
				</tr>
				<tr>
					<td><strong>GuestOne</strong><br />2008-03-26 16:00</td>
					<td><a href="#">Example post title</a><br />Hi, this is a comment. To delete a comment, just log in and view the post's comments.</td>
				</tr>
				<tr>
					<td><strong>GuestOne</strong><br />2008-03-26 16:00</td>
					<td><a href="#">Example post title</a><br />Hi, this is a comment. To delete a comment, just log in and view the post's comments.</td>
				</tr>
			</table>
			<hr class="space" style="height: 2em;" />
		</div><!-- end #content -->
		
		<div id="sidebar">
			<div id="userInfo">
				<img src="images/default-userpic.jpg" alt="" class="left" />
				<h6>Author</h6>
				<p>You have <a href="#">6 posts</a>, <a href="#">1 page</a>, contained within <a href="#">4 categories</a> and <a href="#">14 tags</a>.</p>
			</div><!-- end #userInfo -->

			<h3>Quick start</h3>
			<ul id="quick-links">
				<li><a href="#">Update your profile</a></li>
				<li><a href="#">Add a link to your blogroll</a></li>
				<li><a href="#">Change your site's look or theme</a></li>
				<li><a href="#">Setting Preferences</a></li>
			</ul>
			
			<h3>Toolbox</h3>
			<ul id="toolbox">
				<li><a href="#">Clear Cache</a></li>
				<li><a href="#">Optimize Database</a></li>
				<li><a href="#">Check for Update</a></li>
			</ul>
		</div><!-- end #sidebar -->
		<div class="clear"></div>
	</div><!-- end #main -->
	
<?php include( 'footer.php' ); ?>
