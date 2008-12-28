<?php include('header.php'); ?>

    <div class="grid_10" id="content">
	<?php while($this->next()): ?>
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<div class="entry_data">由 <?php $this->author(); ?> 在 <?php $this->date('F j, Y'); ?> 发布于 <?php $this->category(','); ?>. <?php $this->commentsNum('%d 条评论'); ?>.</div>
			<div class="entry_text">
				<?php $this->content('阅读剩余部分...'); ?>
		    </div>
        </div>
	<?php endwhile; ?>

        <ol class="pages clearfix">
	    <li>页码:</li>
            <?php $this->pageNav(); ?>
        </ol>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
