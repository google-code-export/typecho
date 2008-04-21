<?php
require_once 'common.php';
require_once 'header.php';
widget('Menu')->setCurrentParent('/admin/index.php');
widget('Menu')->setCurrentChild('/admin/index.php');
require_once 'menu.php';
?>
	<div id="main" class="clearfix">
		<div id="sidebar">
			<div id="userInfo">
				<img src="images/default-userpic.jpg" alt="" class="left" />
				<h6>Author</h6>
				<p>You have <a href="#">6 posts</a>, <a href="#">1 page</a>, contained within <a href="#">4 categories</a> and <a href="#">14 tags</a>.</p>
			</div><!-- end #userInfo -->

			<h3><?php _e('快速链接'); ?></h3>
			<ul id="quick-links">
				<li><a href="#"><?php _e('更改我的资料'); ?></a></li>
				<li><a href="#"><?php _e('增加一个链接'); ?></a></li>
				<li><a href="#"><?php _e('更换站点外观'); ?></a></li>
				<li><a href="#"><?php _e('站点设置'); ?></a></li>
			</ul>
			
			<h3><?php _e('工具箱'); ?></h3>
			<ul id="toolbox">
				<li><a href="#"><?php _e('优化数据库'); ?></a></li>
				<li><a href="#"><?php _e('检查更新'); ?></a></li>
			</ul>
		</div><!-- end #sidebar -->
        
		<div id="content">
            <h2><?php _e('欢迎回到Typecho'); ?></h2>
			<a class="botton right" href="#"><?php _e('撰写一篇新文章'); ?></a><a class="botton right" href="#"><?php _e('创建一个新页面'); ?></a>
            <h3><?php _e('最新发布的文章'); ?></h3>
			<table class="latest">
				<tr>
					<th width="20%"><?php _e('信息'); ?></th>
					<th width="80%"><?php _e('内容'); ?></th>
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

			<a class="botton right" href="#">2 comments awaiting moderation</a>
            <h3><?php _e('最新评论/引用'); ?></h3>
			<table class="latest">
				<tr>
					<th width="20%"><?php _e('作者'); ?></th>
					<th width="80%"><?php _e('评论'); ?></th>
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

		</div><!-- end #content -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
