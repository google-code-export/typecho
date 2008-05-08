<div class="container">
	<div id="header">
			<div id="nav-bar" class="right"><?php _e('你好'); ?>, <a href="#"><?php Typecho::widget('Access')->screenName(); ?></a> | 
            <a title="<?php _e('退出当前登录'); ?>" href="<?php Typecho::widget('Options')->index('Logout.do'); ?>"><?php _e('退出'); ?></a> | 
            <a href="http://www.typecho.org" target="_blank" title="<?php _e('Typecho官方网站'); ?>"><?php _e('支持'); ?></a> | 
            <a href="http://www.typecho.org" target="_blank" title="<?php _e('向Typecho报告错误以帮助我们更好地完善产品'); ?>"><?php _e('报告错误'); ?></a>
            </div>
			<h1><a href="<?php Typecho::widget('Options')->siteUrl(); ?>"><?php Typecho::widget('Options')->title(); ?></a></h1>
			<div id="nav-menu">
				<?php Typecho::widget('Menu')->outputParent(NULL); ?>
				<a href="<?php Typecho::widget('Options')->siteUrl(); ?>" style="float: right;"><span><?php _e('返回站点&raquo;'); ?></span></a>
				<div class="clear"></div>
				<ul class="level-2">
					<?php Typecho::widget('Menu')->outputChild('li'); ?>
				</ul>
			</div><!-- end #nav-menu -->
	</div><!-- end #header -->
