<?php 
require_once 'common.php';
require_once 'header.php';
widget('Menu')->setCurrentParent('/admin/edit.php');
widget('Menu')->setCurrentChild('/admin/edit.php');
require_once 'menu.php';
?>

	<div id="main" class="clearfix">
	<form method="post" action="*.php" id="test" name="test">

		<h2>Write New Post</h2>
		<div id="content">
			<div id="msg" class="notice">A saved version of this entry was auto-saved 6 days ago. <a href="#">Recover auto-saved content</a></div>
			<!-- <div id="msg" class="success">A saved version of this entry was auto-saved 6 days ago. <a href="#">Recover auto-saved content</a></div>
			<div id="msg" class="error">A saved version of this entry was auto-saved 6 days ago. <a href="#">Recover auto-saved content</a></div> -->
			<h4>Title</h4>
			<p><input id="title" type="text" name="" value="" /></p>
			<h4>Post</h4>
			<p><textarea id="post_content" name="" rows="15" cols="40"></textarea></p>
			<p style="text-align: right;"><input type="submit" value="Save" /> <input type="submit" value="Save and Continue Edit" /> <input type="submit" value="Publish" /></p>
			<h4>Tags</h4>
			<p><input id="tag" type="text" name="" value="" /><span id="tag_list"><a href="#" class="select">design</a> <a href="#">program</a> <a href="#">wordpress</a> </span></p>
			<h4>URL Title</h4>
			<p><input id="url_title" type="text" name="" value="" /></p>
		</div><!-- end #content -->
		
		<div id="sidebar">
			<h3>Publishing</h3>
			<div id="publishing">
				<p><label>Post Status</label>
				<select name="status" style="width: 155px;">
					<option value="" selected="selected">Published</option>
					<option value="">Unpublished</option>
				</select></p>
				<p><label>Publish Date</label><input type="text" name="test" value="<?php echo date('Y-m-d');?>" /></p>
				<p><label>Publish Time</label><input type="text" name="test" value="<?php echo date('H:i:s');?>" /></p>
			</div>

			<h3>Page Order</h3>
			<p><input type="text" id="" style="width: 240px;" value="0" /></p>

			<h3>Categories</h3>
			<p><input type="text" id="" style="color: #666; width: 155px; margin-right: 15px;" value="Add New Category" onclick="value=''" /><input type="button" value="Add" onclick="" /></p>
			<ul id="cat_list">
				<li><span class="right"><a href="#">&times;</a></span><label><input type="checkbox" id="" /> Category_1</label></li>
				<li><span class="right"><a href="#">&times;</a></span><label><input type="checkbox" id="" /> Category_2</label></li>
				<li><span class="right"><a href="#">&times;</a></span><label><input type="checkbox" id="" /> Category_3</label></li>
			</ul>
			<hr class="space">

			<h3>Comments &amp; Pings</h3>
			<div id="allow_status">
				<p><input type="checkbox" id="comment_status" checked="checked"/><label for="comment_status">Allow Comments</label><br />
				<input type="checkbox" id="ping_status" checked="checked"/><label for="ping_status">Allow Pings</label></p>
			</div>

			<h3>Password Protect This Post/Page</h3>
			<p><input type="text" id="" style="width: 225px;" /></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><small>Setting a password will require people who visit your blog to enter the above password to view this post and its comments.</small></p>

			<h3>Trackbacks</h3>
			<p><textarea id="" rows="5" cols="" style="width: 225px;"></textarea></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><small>Separate multiple URLs with spaces</small></p>
		</div><!-- end #sidebar -->
		<div class="clear"></div>

	</form>
	</div><!-- end #main -->

<?php include( 'footer.php' ); ?>
