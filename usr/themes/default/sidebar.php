

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
				<li><a href="http://ooboy.net/blog/">Fdream</a></li>
				<li><a href="http://hee.ajaxeye.com">elliott</a></li>
				<li><a href="http://wi2ard.com/blog">wi2ard</a></li>
				<li><a href="http://www.monsternunu.com">monsternunu</a></li>
				<li><a href="http://www.hi-open.cn/">Feeling</a></li>
				<li><a href="http://www.magike.net">Magike</a></li>
				<li><a href="http://www.ytblog.com/">Mouse</a></li>
				<li><a href="http://www.ccvita.com">ccvita</a></li>
				<li><a href="http://www.vichair.cn">Vichair</a></li>
				<li><a href="http://www.luweiqing.com">sluke</a></li>
				<li><a href="http://www.coolcode.cn">coolcode</a></li>
				<li><a href="http://aileenguan.cn">Aileenguan</a></li>
				<li><a href="http://www.gracecode.com">Gracecode</a></li>
			</ul>
		</div>
		<div class="widget">
            <h2>Meta</h2>
            <ul>
                <li><a href="#">Login</a></li>
                <li><a href="#">Valid XHTML</a></li>
                <li><a href="#">Typecho</a></li>
            </ul>
        </div>
    </div><!-- end #sidebar -->
