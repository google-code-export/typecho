<?php
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main"><h2>Welcome to Typecho</h2>
		<div class="left" style="width: 14%; margin-right: 1%;">
			<h3>Quick Links</h3>
			<ul class="quick-links">
				<li><a href="#">撰写新文章</a></li>
				<li><a href="#">撰写新页面</a></li>
				<li><a href="#">待审核评论 (10)</a></li>
				<li><a href="#">增加一个链接</a></li>
				<li><a href="#">编辑我的资料</a></li>
				<li><a href="#">更换站点外观</a></li>
				<li><a href="#">修改站点设置</a></li>
			</ul>

			<h3>Useful Tools</h3>
			<ul class="quick-links">
				<li><a href="#">优化数据库</a></li>
				<li><a href="#">备份数据库</a></li>
				<li><a href="#">清理缓存</a></li>
				<li><a href="#">检查更新</a></li>
			</ul>
		</div>
		<div style="width: 59%" class="left">
			<h3>Most Recent Weblog Entires</h3>
			<table class="latest">
				<tr>
					<th width="25%">date</th>
					<th width="75%">entries</th>
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
					<th width="25%">date</th>
					<th width="75%">entries</th>
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

			<h3>Typecho Official News</h3>
			<table class="latest">
				<tr>
					<th width="25%">date</th>
					<th width="75%">entries</th>
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
		<div style="width: 25%" class="right">
			<h3>About Me</h3>
			<table class="latest">
				<tr>
					<th width="40%">Name</th>
					<th width="60%">Value</th>
				</tr>
				<tr>
					<td>用户名</td>
					<td>admin</td>
				</tr>
				<tr>
					<td>昵称</td>
					<td>nick</td>
				</tr>
				<tr>
					<td>注册时间</td>
					<td>08-05-31 00:00</td>
				</tr>
			</table>

			<h3>Site Statistics</h3>
			<table class="latest">
				<tr>
					<th width="40%">Name</th>
					<th width="60%">Value</th>
				</tr>
				<tr>
					<td>程序版本</td>
					<td>1.0</td>
				</tr>
				<tr>
					<td>日志总数</td>
					<td>2</td>
				</tr>
				<tr>
					<td>回复总数</td>
					<td>2</td>
				</tr>
				<tr>
					<td>引用总数</td>
					<td>2</td>
				</tr>
				<tr>
					<td>待审核评论</td>
					<td>2</td>
				</tr>
				<tr>
					<td>垃圾留言</td>
					<td>2</td>
				</tr>
			</table>
		</div>
	</div><!-- end #main -->
	
<?php include( 'footer.php' ); ?>
