

    <div class="grid_5" id="sidebar">
		<div class="widget">
            <h2>Recent Articles</h2>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Recent')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
            </ul>
        </div>
		<div class="widget">
            <h2>Recent Comments</h2>
            <ul>
            <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
            <?php while($comments->next()): ?>
                <li><?php $comments->author(false); ?>: <a href="<?php $comments->permalink(); ?>"><?php $comments->excerpt(10, '[...]'); ?></a></li>
            <?php endwhile; ?>
            </ul>
        </div>
        <div class="widget">
            <h2>Categories</h2>
            <ul>
                <?php $this->widget('Widget_Metas_Category_List')
                ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
            </ul>
        </div>
        <div class="widget">
            <h2>Archives</h2>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=F Y')
                ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
            </ul>
        </div>
		<div class="widget">
            <h2>Meta</h2>
            <ul>
                <?php if($this->user->hasLogin()): ?>
                    <li class="last"><a href="<?php $this->options->index('Logout.do'); ?>">Logout (<?php $this->user->screenName(); ?>)</a></li>
                <?php else: ?>
                    <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>">Login</a></li>
                <?php endif; ?>
                <li><a href="http://validator.w3.org/check/referer">Valid XHTML</a></li>
                <li><a href="http://www.typecho.org">Typecho</a></li>
            </ul>
        </div>
    </div><!-- end #sidebar -->
