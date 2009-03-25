<?php include('header.php'); ?>

    <div class="grid_11" id="content">
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<div class="entry_data"><?php _e('作者：'); ?><?php $this->author(); ?> | <?php _e('发布时间：'); ?><?php $this->date('F j, Y'); ?> | <?php _e('分类：'); ?><?php $this->category(','); ?></div>
			<div class="entry_text">
				<?php $this->content(); ?>
		    </div>
		</div>

		<div class="clearfix" id="post_extra">
			<?php _e('标签'); ?>: <?php $this->tags(', ', true, 'none'); ?>
		</div>

		<?php include('comments.php'); ?>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
