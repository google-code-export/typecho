<?php
/**
 * 这是 Typecho 系统的一套默认皮肤
 * 
 * @package Typecho Default Theme 
 * @author fen
 * @version 1.0.5
 * @link http://typecho.org
 */
 
 include('header.php');
 ?>

    <div class="grid_11" id="content">
	<?php while($this->next()): ?>
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<div class="entry_data"><?php _e('作者：'); ?><?php $this->author(); ?> | <?php _e('发布时间：'); ?><?php $this->date('F j, Y'); ?> | <?php _e('分类：'); ?><?php $this->category(','); ?> | <?php $this->commentsNum('%d'); ?> <?php _e('条评论'); ?></div>
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
