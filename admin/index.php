<?php
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/index.php');
Typecho::widget('Menu')->setCurrentChild('/admin/index.php');
require_once 'header.php';
require_once 'menu.php';
?>
	<div id="main" class="clearfix">
		<div id="sidebar">
			<div id="userInfo">
				<img src="images/default-userpic.jpg" alt="" class="left" />
				<h6><?php Typecho::widget('Access')->screenName(); ?></h6>
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
			<a class="botton right" href="<?php Typecho::widget('Options')->adminUrl('/edit-page.php'); ?>"><?php _e('创建一个新页面'); ?></a>
			<a class="botton right" href="<?php Typecho::widget('Options')->adminUrl('/edit.php'); ?>"><?php _e('撰写一篇新文章'); ?></a>
            <h3><?php _e('最新发布的文章'); ?></h3>
			<table class="latest">
				<tr>
					<th width="25%"><?php _e('作者'); ?></th>
					<th width="75%"><?php _e('内容'); ?></th>
				</tr>
                <?php Typecho::widget('contents.AuthorsRecentPost', Typecho::widget('Access')->uid)->to($post); ?>
                <?php if($post->have()): ?>
                <?php while($post->get()): ?>
				<tr>
					<td><strong><?php $post->author(); ?></strong><br /><?php $post->date(_t('y年n月j日 H时i分')); ?></td>
					<td><a href="<?php $post->permalink(); ?>"><?php $post->title(); ?></a> | 
					<a href="<?php Typecho::widget('Options')->adminUrl('/edit.php?cid=' . $post->cid); ?>"><?php _e('编辑'); ?></a>
					<br /><?php $post->excerpt(100); ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                	<td colspan="2"><strong><?php _e('对不起, 您没有发布任何文章'); ?></strong></td>
                </tr>
                <?php endif; ?>
			</table>

			<a class="botton right" href="#"><?php _e('%d篇评论等待审核', 2); ?></a>
			<a class="botton right" href="#"><?php _e('%d篇垃圾评论', 5); ?></a>
            <h3><?php _e('最新评论/引用'); ?></h3>
			<table class="latest">
				<tr>
					<th width="25%"><?php _e('作者'); ?></th>
					<th width="75%"><?php _e('评论'); ?></th>
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
