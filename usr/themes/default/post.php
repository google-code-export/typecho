<?php include('header.php'); ?>

    <div class="grid_10" id="content">
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<p class="entry_data">
				<span><?php _e('作者：'); ?><?php $this->author(); ?></span>
				<span><?php _e('发布时间：'); ?><?php $this->date('F j, Y'); ?></span>
				<?php _e('分类：'); ?><?php $this->category(','); ?>
			</p>
			<?php $this->content(); ?>
			<p class="tags"><?php _e('标签'); ?>: <?php $this->tags(', ', true, 'none'); ?></p>
		</div>

		<?php include('comments.php'); ?>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
