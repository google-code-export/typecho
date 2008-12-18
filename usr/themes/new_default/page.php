<?php include('header.php'); ?>

    <div class="grid_10" id="content">
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<div class="entry_data">Published by <?php $this->author(); ?> on <?php $this->date('F j, Y'); ?> in <?php $this->category(','); ?>. <?php $this->commentsNum('%d Comments'); ?>.</div>
			<div class="entry_text">
				<?php $this->content('Continue Reading...'); ?>
		    </div>
		</div>

		<?php include('comments.php'); ?>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
