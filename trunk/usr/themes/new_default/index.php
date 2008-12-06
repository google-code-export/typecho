<?php
/**
 * 这是typecho系统的一套默认皮肤
 * 你可以自由的使用，修改和分发它，此皮肤的样式表基于 <a href="http://960.gs">960 Grid System</a> 制作, 它完全符合 CSS 2.0 以及 XHTML 1.0 Strict 规范。你可以在<a href="http://typecho.org">typecho的官方网站</a>获得更多关于此皮肤的信息
 * 
 * @package Typecho Default Theme 
 * @author fen
 * @version 1.0.2
 * @link http://hellowiki.com
 */
 
 include('header.php');
 ?>

    <div class="grid_11" id="content">
	<?php while($this->next()): ?>
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<div class="entry_data">Published by <?php $this->author(); ?> on <?php $this->date('F j, Y'); ?> in <?php $this->category(','); ?>. <?php $this->commentsNum('%d Comments'); ?>.</div>
			<div class="entry_text">
				<?php $this->content('Continue Reading...'); ?>
		    </div>
        </div>
	<?php endwhile; ?>

        <div class="pages clearfix">
            <?php $this->pageNav(); ?>
        </div>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
