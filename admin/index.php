<?php
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main"><h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div class="left" style="width: 14%; margin-right: 1%;">
			<h3><?php _e('快速链接'); ?></h3>
			<ul class="quick-links">
				<li><a href="#">撰写新文章</a></li>
				<li><a href="#">撰写新页面</a></li>
				<li><a href="#">待审核评论 <sup>10</sup></a></li>
				<li><a href="#">增加一个链接</a></li>
				<li><a href="#">编辑我的资料</a></li>
				<li><a href="#">更换站点外观</a></li>
				<li><a href="#">修改站点设置</a></li>
			</ul>

			<h3><?php _e('工具箱'); ?></h3>
			<ul class="quick-links">
				<li><a href="#">优化数据库</a></li>
				<li><a href="#">备份数据库</a></li>
				<li><a href="#">清理缓存</a></li>
				<li><a href="#">检查更新</a></li>
			</ul>
		</div>
		<div style="width: 59%" class="left">
			<h3><?php _e('最新文章'); ?></h3>
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

			<h3><?php _e('最新评论'); ?></h3>
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

			<h3><?php _e('官方新闻'); ?></h3>
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
			<div id="userInfo">
                <h3><?php Typecho::widget('Access')->screenName(); ?></h3>
                <p><img src="images/default-userpic.jpg" alt="" class="left" />
				<?php _e('总共撰写了<a href="%s">%d篇日志</a>和<a href="%s">%d篇页面</a>.', 
                Typecho::pathToUrl('/post-list.php?status=my', Typecho::widget('Options')->adminUrl),
                Typecho::widget('Abstract.contents')->count('post', Typecho::widget('Access')->uid), 
                Typecho::pathToUrl('/page-list.php?status=my', Typecho::widget('Options')->adminUrl),
                Typecho::widget('Abstract.contents')->count('page', Typecho::widget('Access')->uid)); ?><br />
                <?php _e('上次登陆为%s.', TypechoI18n::dateWord(Typecho::widget('Access')->logged + Typecho::widget('Options')->timezone, Typecho::widget('Options')->gmtTime + Typecho::widget('Options')->timezone)); ?><br /><br />
                <h3><?php _e('服务器环境'); ?></h3>
                <ol>
                    <li><?php _e('当前服务器操作系统为%s', PHP_OS); ?></li>
                    <li><?php _e('网页服务器为%s', isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : _t('不明')); ?></li>
                    <li><?php _e('数据库驱动为%s', TypechoDb::get()->version()); ?></li>
                    <li><?php _e('PHP版本为%s', PHP_VERSION); ?></li>
                </ol>
                </p>
			</div>
		</div>
	</div><!-- end #main -->
	
<?php include( 'footer.php' ); ?>
