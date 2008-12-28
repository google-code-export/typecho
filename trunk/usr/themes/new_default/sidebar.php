

    <div class="grid_4" id="sidebar">
    
	    <div class="widget">
            <h2>最新文章</h2>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Recent')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
            </ul>
	    </div>
    
    
	    <div id="search" class="widget">
	        <form method="post" action="">
            	<div><input type="text" name="s" class="text" size="20" /> <input type="submit" class="submit" value="搜索" /></div>
	        </form>
	    </div>

	    <div class="widget">
            <h2>最近回复</h2>
            <ul>
            <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
            <?php while($comments->next()): ?>
                <li><a href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?></a>: <?php $comments->excerpt(28, '...'); ?></li>
            <?php endwhile; ?>
            </ul>
	    </div>

        <div class="widget">
            <h2>分类</h2>
            <ul>
                <?php $this->widget('Widget_Metas_Category_List')
                ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
            </ul>
		</div>

        <div class="widget">
            <h2>归档</h2>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=F Y')
                ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
            </ul>
		</div>

		<div class="widget">
            <h2>其它</h2>
            <ul>
                <?php if($this->user->hasLogin()): ?>
                    <li class="last"><a href="<?php $this->options->index('Logout.do'); ?>">注销 (<?php $this->user->screenName(); ?>)</a></li>
                <?php else: ?>
                    <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>">登录</a></li>
                <?php endif; ?>
                <li><a href="http://validator.w3.org/check/referer">Valid XHTML</a></li>
                <li><a href="http://www.typecho.org">Typecho</a></li>
            </ul>
		</div>

    </div><!-- end #sidebar -->
