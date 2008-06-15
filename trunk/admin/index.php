<?php
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main"><h2><?php $menu->title(); ?></h2>
		<div class="left" style="width: 14%; margin-right: 1%;">
			<ul class="quick-links">
				<li><a href="#">撰写新文章</a></li>
				<li><a href="#">撰写新页面</a></li>
				<li><a href="#">待审核评论 <sup>10</sup></a></li>
				<li><a href="#">增加一个链接</a></li>
				<li><a href="#">编辑我的资料</a></li>
				<li><a href="#">更换站点外观</a></li>
				<li><a href="#">修改站点设置</a></li>
			</ul>
		</div>
		<div style="width: 59%" class="left">
            <?php Typecho_API::factory('Widget_Contents_Post_Recent', 5)->to($recentPosts); ?>
			<table class="latest">
				<tr>
					<th width="20%"><?php _e('最新文章'); ?></th>
				</tr>
                <?php if($recentPosts->have()): ?>
                <?php while($recentPosts->get()): ?>
				<tr>
					<td>
                        <strong><a target="_blank" href="<?php $recentPosts->permalink(); ?>"><?php $recentPosts->title(); ?></a></strong>
                        <sup><?php $recentPosts->tags(','); ?></sup>
                        <?php _e('发布于'); ?>
                        <?php $recentPosts->category(', '); ?>
                        <sup><?php $recentPosts->dateWord(); ?></sup>
                    </td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td><?php _e('暂时没有文章'); ?></td>
                </tr>
                <?php endif; ?>
			</table>

            <?php Typecho_API::factory('Widget_Comments_Recent', 5)->to($recentComments); ?>
			<table class="latest">
				<tr>
					<th><?php _e('最新评论'); ?></th>
				</tr>
                <?php if($recentComments->have()): ?>
                <?php while($recentComments->get()): ?>
				<tr>
					<td><?php _e('<strong>%s</strong> 发表在 <a href="%s">%s</a>', $recentComments->author, $recentComments->permalink, $recentComments->title); ?> <sup><?php $recentComments->dateWord(); ?></sup></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td><?php _e('暂时没有评论'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
		</div>
		<div style="width: 25%" class="right">
			<div id="userInfo">
                <img width="50" height="50" src="http://www.gravatar.com/avatar.php?gravatar_id=<?php echo md5($access->mail); ?>&rating=X&size=50&default=<?php $options->adminUrl('/images/default-userpic.jpg'); ?>" alt="<?php $access->screenName(); ?>" class="left" />
                <h6><?php $access->screenName(); ?></h6>
				<?php _e('总共撰写了<a href="%s">%d篇日志</a>和<a href="%s">%d篇页面</a>.', 
                Typecho_API::pathToUrl('/post-list.php?status=my', $options->adminUrl),
                Typecho_API::factory('Widget_Abstract_Contents')
                ->size(Typecho_API::factory('Widget_Abstract_Contents')->select()
                ->where('table.contents.`type` = ? AND table.contents.`author` = ?', 'post', $access->uid)), 
                Typecho_API::pathToUrl('/page-list.php?status=myPost', $options->adminUrl),
                Typecho_API::factory('Widget_Abstract_Contents')
                ->size(Typecho_API::factory('Widget_Abstract_Contents')->select()
                ->where('table.contents.`type` = ? AND table.contents.`author` = ?', 'page', $access->uid))); ?><br />
                <?php _e('上次登陆为%s.', Typecho_I18n::dateWord($access->logged + $options->timezone, $options->gmtTime + $options->timezone)); ?><br />
                <h6 style="margin-top:10px;"><?php _e('服务器环境'); ?></h6>
                <ol>
                    <li><small><?php $options->generator(); ?></small></li>
                    <li><small><?php echo PHP_OS; ?></small></li>
                    <li><small><?php echo isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : _t('不明'); ?></small></li>
                    <li><small><?php echo Typecho_Db::get()->version(); ?></small></li>
                    <li><small>PHP <?php echo PHP_VERSION; ?></small></li>
                </ol>
			</div>
		</div>
	</div><!-- end #main -->
	
<?php include( 'footer.php' ); ?>
