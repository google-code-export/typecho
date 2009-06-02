<?php

function threadedComments($comments)
{
?>
    <li id="<?php $comments->theId(); ?>"<?php $comments->levelsAlt(' class="odd"', ''); ?>>
					<div class="comment_data">
						<?php $comments->gravatar(32, 'X', '', 'avatar'); ?>
						<span class="author"><?php $comments->author(); ?></span>
						<?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?> <!-- <span class="count">#<?php echo $comments->sequence(); ?></span> -->
					</div>
					<?php $comments->content(); ?>
                    <?php $comments->threadedComments('<ol>', '</ol>'); ?>
                    <?php if (!$comments->isTopLevel): ?>
                    <div class="comment_reply alt">
                        <?php Helper::replyLink($comments->theId, $comments->coid, 'Reply', 'respond'); ?>
                    </div>
                    <?php endif; ?>
    </li>
<?php
}
?>

<div id="comments">
			<h4 class="alt"><?php $this->commentsNum(_t('当前暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?> &raquo;</h4>
			<ol id="comment_list">
			<?php $this->comments()->to($comments); ?>
            <?php while($comments->next()): ?>
				<li id="<?php $comments->theId(); ?>">
					<div class="comment_data">
						<?php $comments->gravatar(32, 'X', '', 'avatar'); ?>
						<span class="author"><?php $comments->author(); ?></span>
						<?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?> <!-- <span class="count">#<?php echo $comments->sequence(); ?></span> -->
					</div>
					<?php $comments->content(); ?>
                    <?php $comments->threadedComments('<ol>', '</ol>'); ?>
                    <div class="comment_reply alt">
                        <?php Helper::replyLink($comments->theId, $comments->coid, 'Reply', 'respond'); ?>
                    </div>
				</li>
			<?php endwhile; ?>
			</ol>

            <?php if($this->allow('comment')): ?>
            <div id="respond">
            <div class="cancle_comment_reply"><?php Helper::cancleCommentReplyLink('Click here to cancel reply', 'respond'); ?></div>
			<h4 id="response" class="alt"><?php _e('添加新评论'); ?> &raquo;</h4>
			<form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
                <?php if($this->user->hasLogin()): ?>
				<p>Logged in as <a href="<?php $this->options->adminUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('登出'); ?> &raquo;</a></p>
                <?php else: ?>
				<p>
					<input type="text" name="author" id="author" class="text" size="15" value="<?php $this->remember('author'); ?>" />
					<label for="author"><?php _e('称呼'); ?></label>
				</p>
				<p>
					<input type="text" name="mail" id="mail" class="text" size="15" value="<?php $this->remember('mail'); ?>" />
					<label for="mail"><?php _e('E-mail'); ?></label>
				</p>
				<p>
					<input type="text" name="url" id="url" class="text" size="15" value="<?php $this->remember('url'); ?>" />
					<label for="url"><?php _e('网站（选填）'); ?></label>
				</p>
                <?php endif; ?>
				<p><textarea rows="5" cols="50" name="text" class="textarea"><?php $this->remember('text'); ?></textarea></p>
				<p><input type="submit" value="<?php _e('提交评论'); ?>" class="submit" /></p>
			</form>
            </div>
            <?php endif; ?>
		</div>
