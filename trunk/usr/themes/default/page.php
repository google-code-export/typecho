<?php include('header.php'); ?>

    <div class="grid_11" id="content">
        <div class="post">
			<div class="entry_main">
				<div class="entry_title">
	                <h2><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
		        </div>
			    <div class="entry_text">
				    <?php $this->content(); ?>
					<div class="single_data clearfix">
						<h3>Related Articles</h3>
						<ul id="related_post">
							<li><a href="#">MySpace, a Virtual Mirror of Society</a></li>
							<li><a href="#">Religion in the Modern world</a></li>
							<li><a href="#">A 13-Year-Old is Not a Playboy Bunny</a></li>
							<li><a href="#">Truth Behind the Mask</a></li>
							<li><a href="#">The Business of Blogging and How It is Mostly Rubbish</a></li>
						</ul>
					</div>
		        </div>
				<div class="entry_data"><?php $this->author(); ?> on <?php $this->date('F j, Y'); ?> 
                | Filed Under <?php $this->category(','); ?> | <?php $this->commentsNum('%d Comments'); ?>.<br />
                Tags: <?php $this->tags(',', true, 'none'); ?></div>
			</div>
        </div>
		<div id="comments">
			<h4><?php $this->commentsNum('No Response', 'One Response to"' . $this->title . '"', '%d Responses to "' . $this->title . '"'); ?></h4>
			<ol id="comment_list">
			<?php $this->comments()->to($comments); ?>
            <?php while($comments->get()): ?>
				<li id="<?php $comments->id(); ?>">
					<div class="c_user"><?php echo $comments->sequence(); ?> | <strong><?php $comments->author(); ?></strong> at <?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?> </div>
					<div class="c_text">
						<?php $comments->content(); ?>
					</div>
				</li>
			<?php endwhile; ?>
			</ol>
			<h4 id="response">Leave a Reply</h4>
			<form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
				<p><input type="text" name="author" class="text" size="50" /><label>Name (Required)</label></p>
				<p><input type="text" name="mail" class="text" size="50" /><label>E-mail (Required *will not be published)</label></p>
				<p><input type="text" name="url" class="text" size="50" /><label>Website</label></p>
				<p><textarea rows="12" name="text" cols=""></textarea></p>
				<p><input type="submit" value="Submit Comment" class="submit" /></p>
			</form>
		</div>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
