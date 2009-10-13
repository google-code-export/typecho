<?php include('header.php'); ?>

    <div class="grid_10" id="content">
	<?php while($this->next()): ?>
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<p class="entry_data">
				<span><?php _e('作者：'); ?><?php $this->author(); ?></span>
				<span><?php _e('发布时间：'); ?><?php $this->date('F j, Y'); ?></span>
				<?php _e('分类：'); ?><?php $this->category(','); ?>
			</p>
			<?php $this->content('阅读剩余部分...'); ?>
		</div>
	<?php endwhile; ?>

        <ol class="pages clearfix">
            <?php $this->pageNav(); ?>
        </ol>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
