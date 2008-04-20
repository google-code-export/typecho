<div class="container">
	<div id="header">
			<div id="nav-bar" class="right"><?php _e('你好'); ?>, <a href="#">admin</a> | 
            <a title="Log Out" href="#"><?php _e('登出'); ?></a> | 
            <a href="#"><?php _e('支持'); ?></a> | 
            <a href="#"><?php _e('报告错误'); ?></a>
            </div>
			<h1><a href="#">Typecho: Dashboard</a></h1>
			<div id="nav-menu">
				<?php widget('Menu')->outputParent(NULL); ?>
				<a href="#" style="float: right;"><span><?php _e('返回站点&raquo;'); ?></span></a>
				<div class="clear"></div>
				<ul class="level-2">
					<?php widget('Menu')->outputChild('li'); ?>
				</ul>
			</div><!-- end #nav-menu -->
	</div><!-- end #header -->
