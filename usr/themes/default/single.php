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
			<?php for($a=1; $a!=6; $a++) echo'
				<li>
					<div class="c_user">'.$a.' | <strong><a href="#">Fen</a></strong> at January 12th, 2008 at 1:58 am </div>
					<div class="c_text">
						<img src="http://www.gravatar.com/avatar.php?gravatar_id=d18d64bf2b1d256fdcb811cafbbfa487&amp;size=32" width="32" height="32" class="gravatar" alt="" />
						<p>Wt? You are asking? I bet, if you start selling premium themes, you will be number one in the paid theme market. Honestly.</p>
						<p>The most best thing about you is attention to details that tops all other designers out there. I would only say, “go for it!”.</p>
					</div>
				</li>
			'; ?>
			</ol>
			<h4 id="response">Leave a Reply</h4>
			<form method="post" action="" id="comment_form">
				<p><input type="text" class="text" size="50" /><label>Name (Required)</label></p>
				<p><input type="text" class="text" size="50" /><label>E-mail (Required *will not be published)</label></p>
				<p><input type="text" class="text" size="50" /><label>Website</label></p>
				<p><textarea rows="12" cols=""></textarea></p>
				<p><input type="submit" value="Submit Comment" class="submit" /></p>
			</form>
		</div>
    </div><!-- end #content-->
	<?php include('sidebar.php'); ?>
	<?php include('footer.php'); ?>
