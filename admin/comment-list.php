<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho_API::factory('Widget_Menu')->title(); ?></h2>
        
		<div id="page">
            <?php require_once 'notice.php'; ?>
            
			<div class="table_nav">
            <form action="<?php $options->adminUrl('comment-list.php'); ?>">
				<input type="button" class="button" onclick="$('input[@name=do]').val('approved');comment.submit();" value="<?php _e('展现'); ?>" />
				<input type="button" class="button" onclick="$('input[@name=do]').val('spam');comment.submit();" value="<?php _e('垃圾'); ?>" />
				<input type="button" class="button" onclick="$('input[@name=do]').val('waiting');comment.submit();" value="<?php _e('待审核'); ?>" />
				<input type="button" class="button" onclick="$('input[@name=do]').val('delete');comment.submit();" value="<?php _e('删除'); ?>" />
				<input type="text" class="text" id="" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<select name="status" style="width: 160px;">
					<option value="all" <?php Typecho_Request::callParameter('status', 'all', 'selected="selected"'); ?>>
                        <?php _e('所有评论 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid")->num); ?>
                    </option>
					<option value="approved" <?php Typecho_Request::callParameter('status', 'approved', 'selected="selected"'); ?>>
                        <?php _e('展现 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid&status=approved")->num); ?>
                    </option>
					<option value="waiting" <?php Typecho_Request::callParameter('status', 'waiting', 'selected="selected"'); ?>>
                        <?php _e('待审核 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid&status=waiting")->num); ?>
                    </option>
					<option value="spam" <?php Typecho_Request::callParameter('status', 'spam', 'selected="selected"'); ?>>
                        <?php _e('垃圾箱 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid&status=spam")->num); ?>
                    </option>
				</select>
				<input type="submit" class="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>

            <form method="post" name="comment" id="comment" action="<?php $options->index('/Comments/Edit.do'); ?>">
			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="10%"><?php _e('日期'); ?></th>
					<th width="20%"><?php _e('作者'); ?></th>
					<th width="40%"><?php _e('摘要'); ?></th>
					<th width="22%"><?php _e('文章'); ?></th>
					<th width="7%"><?php _e('状态'); ?></th>
				</tr>
                <?php Typecho_API::factory('Widget_Comments_Admin')->to($comments); ?>
                <?php if($comments->have()): ?>
				<?php while($comments->get()): ?>
				<tr>
					<td><input type="checkbox" name="coid[]" value="<?php $comments->coid(); ?>" /></td>
					<td><?php $comments->dateWord(); ?></td>
					<td><?php $comments->author(false); ?>
                    <sup><?php
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
                    ?></sup>
                    <sub>
                        <?php if($comments->url): ?><a target="_blank" href="<?php $comments->url(); ?>">网址</a><?php endif; ?>
                        <?php if($comments->mail): ?><a href="mailto:<?php $comments->mail(); ?>">邮件</a><?php endif; ?>
                    </sub>
                    </td>
					<td><?php $comments->excerpt(30); ?></td>
					<td><a target="_blank" href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a></td>
					<td><span class="<?php $comments->status(); ?>">
                    <?php
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
                    ?></span></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6"><?php _e('没有找到评论'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
            <input type="hidden" name="do" value=""/>
            </form>

            <?php if($comments->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $comments->pageNav(); ?>
			</div>
            <?php endif; ?>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
