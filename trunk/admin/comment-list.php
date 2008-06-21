<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
        
		<div id="page">
			<div class="table_nav">
            <form action="<?php Typecho::widget('Options')->adminUrl('comment-list.php'); ?>">
				<input type="submit" class="submit" value="<?php _e('展现'); ?>" />
				<input type="submit" class="submit" value="<?php _e('垃圾'); ?>" />
				<input type="submit" class="submit" value="<?php _e('待审核'); ?>" />
				<input type="submit" class="submit" value="<?php _e('删除'); ?>" />
				<input type="text" class="text" id="" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<select name="status" style="width: 160px;">
					<option value="all" <?php TypechoRequest::callParameter('status', 'all', 'selected="selected"'); ?>>
                        <?php _e('所有评论(%s)', Typecho::widget('Abstract.Comments')->count()); ?>
                    </option>
					<option value="approved" <?php TypechoRequest::callParameter('status', 'approved', 'selected="selected"'); ?>>
                        <?php _e('展现(%s)', Typecho::widget('Abstract.Comments')->count('approved')); ?>
                    </option>
					<option value="waiting" <?php TypechoRequest::callParameter('status', 'waiting', 'selected="selected"'); ?>>
                        <?php _e('待审核(%s)', Typecho::widget('Abstract.Comments')->count('waiting')); ?>
                    </option>
					<option value="spam" <?php TypechoRequest::callParameter('status', 'spam', 'selected="selected"'); ?>>
                        <?php _e('垃圾箱(%s)', Typecho::widget('Abstract.Comments')->count('spam')); ?>
                    </option>
				</select>
				<input type="submit" class="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="10%"><?php _e('日期'); ?></th>
					<th width="20%"><?php _e('作者'); ?></th>
					<th width="40%"><?php _e('摘要'); ?></th>
					<th width="22%"><?php _e('文章'); ?></th>
					<th width="7%"><?php _e('状态'); ?></th>
				</tr>
                <?php Typecho::widget('Comments.AdminComments')->to($comments); ?>
                <?php if($comments->have()): ?>
				<?php while($comments->get()): ?>
				<tr>
					<td><input type="checkbox" id="" /></td>
					<td><?php $comments->dateWord(); ?></td>
					<td><?php $comments->author(); ?>
                    <sup><?php
                        switch($comments->status)
                        {
                            case 'approved':
                                echo _t('展现');
                                break;
                            case 'spam':
                                echo _t('垃圾');
                                break;
                            case 'waiting':
                                echo _t('待审核');
                                break;
                            default:
                                echo _t('不明');
                                break;
                        }
                    ?></sup>
                    <sub>
                        <?php if($comments->url): ?><a target="_blank" href="<?php $comments->url(); ?>">网址</a><?php endif; ?>
                        <?php if($comments->mail): ?><a href="mailto:<?php $comments->mail(); ?>">邮件</a><?php endif; ?>
                    </sub>
                    </td>
					<td><?php $comments->excerpt(30); ?></td>
					<td><a target="_blank" href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a></td>
					<td><?php
                        switch($comments->mode)
                        {
                            case 'pingback':
                                echo _t('广播');
                                break;
                            case 'trackback':
                                echo _t('引用');
                                break;
                            case 'comment':
                                echo _t('评论');
                                break;
                            default:
                                echo _t('不明');
                                break;
                        }
                    ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6"><?php _e('没有找到评论'); ?></td>
                </tr>
                <?php endif; ?>
			</table>

            <?php if($comments->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $comments->pageNav(); ?>
			</div>
            <?php endif; ?>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
