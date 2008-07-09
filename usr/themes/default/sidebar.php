

    <div class="grid_5" id="sidebar">
		<div class="widget">
            <h2>Recent Articles</h2>
            <ul>
                <?php $this->widget('Contents/Post/Recent')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
            </ul>
        </div>
		<div class="widget">
            <h2>Recent Comments</h2>
            <ul>
            <?php $this->widget('Comments/Recent')->to($comments); ?>
            <?php while($comments->get()): ?>
                <li><?php $comments->author(false); ?>: <a href="<?php $comments->permalink(); ?>"><?php $comments->excerpt(10, '[...]'); ?></a></li>
            <?php endwhile; ?>
            </ul>
        </div>
        <div class="widget">
            <h2>Categories</h2>
            <ul>
                <?php $this->widget('Metas/Category/List')
                ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
            </ul>
        </div>
        <div class="widget">
            <h2>Archives</h2>
            <ul>
                <?php $this->widget('Contents/Post/Date', 'month', 'F Y')
                ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
            </ul>
        </div>
		<div class="widget">
			<h2>Blogroll</h2>
			<ul>
				<?php $this->widget('Metas/Link/List')
                ->parse('<li><a title="{description}" href="{url}">{name}</a></li>'); ?>
			</ul>
		</div>
		<div class="widget">
            <h2>Meta</h2>
            <ul>
                <?php if($this->widget('Users/Current')->hasLogin()): ?>
                    <li class="last"><a href="<?php $this->options->index('Logout.do'); ?>">Logout (<?php $this->widget('Users/Current')->screenName(); ?>)</a></li>
                <?php else: ?>
                    <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>">Login</a></li>
                <?php endif; ?>
                <li><a href="http://validator.w3.org/check/referer">Valid XHTML</a></li>
                <li><a href="http://www.typecho.org">Typecho</a></li>
            </ul>
        </div>
    </div><!-- end #sidebar -->
