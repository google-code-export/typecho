		<div id="comments">
			<h4><?php $this->commentsNum('当前暂无评论', '仅有一条评论', '已有 %d 条评论'); ?></h4>
			<ol id="comment_list">
			<?php $this->comments()->to($comments); ?>
            <?php while($comments->next()): ?>
				<li id="<?php $comments->theId(); ?>">
					<div class="comment_data"><!-- <?php echo $comments->sequence(); ?> --><strong><?php $comments->author(); ?></strong><br /><?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?></div>
					<div class="comment_body">
						<?php $comments->content(); ?>
					</div>
				</li>
			<?php endwhile; ?>
			</ol>

            <?php if($this->allow('comment')): ?>
			<h4 id="response">添加新评论</h4>
			<form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
                <?php if($this->user->hasLogin()): ?>
                <p>Logged in as <a href="<?php $this->options->adminUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->index('Logout.do'); ?>" title="Logout">Logout &raquo;</a></p>
                <?php else: ?>
				<p><input type="text" name="author" class="text" size="35" value="<?php $this->remember('author'); ?>" /><label>名字 *</label></p>
				<p><input type="text" name="mail" class="text" size="35" value="<?php $this->remember('mail'); ?>" /><label>E-mail *</label></p>
				<p><input type="text" name="url" class="text" size="35" value="<?php $this->remember('url'); ?>" /><label>网站 （选填）</label></p>
                <?php endif; ?>
				<p><textarea rows="10" cols="50" name="text"><?php $this->remember('text'); ?></textarea></p>
				<p><input type="submit" value="提交评论" class="submit" /></p>
			</form>
            <?php endif; ?>
		</div>
