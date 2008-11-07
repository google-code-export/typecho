<?php include('header.php'); ?>

    <div class="grid_11" id="content">
        <div class="post">
			<h2 class="entry_title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<div class="entry_data">Published by <?php $this->author(); ?> on <?php $this->date('F j, Y'); ?> in <?php $this->category(','); ?>. <?php $this->commentsNum('%d Comments'); ?>.</div>
			<div class="entry_text">
				<?php $this->content('Continue Reading...'); ?>
		    </div>
		</div>

		<div id="comments">
			<h4><?php $this->commentsNum('No Response', 'One Response to"' . $this->title . '"', '%d Responses to "' . $this->title . '"'); ?></h4>
			<ol id="comment_list">
			<?php $this->comments()->to($comments); ?>
            <?php while($comments->next()): ?>
				<li id="<?php $comments->theId(); ?>">
					<div class="comment_data"><?php echo $comments->sequence(); ?>. <strong><?php $comments->author(); ?></strong> on <?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?> </div>
					<div class="comment_body">
						<?php $comments->content(); ?>
					</div>
				</li>
			<?php endwhile; ?>
			</ol>

            <?php if($this->allow('comment')): ?>
			<h4 id="response">Leave a Reply</h4>
			<form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
                <?php if($this->user->hasLogin()): ?>
                <p>Logged in as <a href="<?php $this->options->adminUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->index('Logout.do'); ?>" title="Logout">Logout &raquo;</a></p>
                <?php else: ?>
				<p><input type="text" name="author" class="text" size="35" value="<?php $this->remember('author'); ?>" /><label>Name (Required)</label></p>
				<p><input type="text" name="mail" class="text" size="35" value="<?php $this->remember('mail'); ?>" /><label>E-mail (Required *will not be published)</label></p>
				<p><input type="text" name="url" class="text" size="35" value="<?php $this->remember('url'); ?>" /><label>Website</label></p>
                <?php endif; ?>
				<p><textarea rows="10" cols="50" name="text"><?php $this->remember('text'); ?></textarea></p>
				<p><input type="submit" value="Submit Comment" class="submit" /></p>
			</form>
            <?php endif; ?>
		</div>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
