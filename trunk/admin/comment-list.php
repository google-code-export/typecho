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
				<input rel="<?php $options->adminUrl('/images/icons/accept.gif'); ?>" type="button" class="button" onclick="$('input[@name=do]').val('approved');comment.submit();" value="<?php _e('呈现'); ?>" />
				<input rel="<?php $options->adminUrl('/images/icons/exclamation.gif'); ?>" type="button" class="button" onclick="$('input[@name=do]').val('spam');comment.submit();" value="<?php _e('垃圾'); ?>" />
				<input rel="<?php $options->adminUrl('/images/icons/error.gif'); ?>" type="button" class="button" onclick="$('input[@name=do]').val('waiting');comment.submit();" value="<?php _e('待审核'); ?>" />
				<input rel="<?php $options->adminUrl('/images/icons/delete.gif'); ?>" type="button" class="button" onclick="$('input[@name=do]').val('delete');comment.submit();" value="<?php _e('删除'); ?>" />
				<input type="text" class="text" id="" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<select name="status" style="width: 160px;">
					<option value="all" <?php Typecho_Request::callParameter('status', 'all', 'selected="selected"'); ?>>
                        <?php _e('所有评论 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid")->num); ?>
                    </option>
					<option value="approved" <?php Typecho_Request::callParameter('status', 'approved', 'selected="selected"'); ?>>
                        <?php _e('呈现 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid&status=approved")->num); ?>
                    </option>
					<option value="waiting" <?php Typecho_Request::callParameter('status', 'waiting', 'selected="selected"'); ?>>
                        <?php _e('待审核 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid&status=waiting")->num); ?>
                    </option>
					<option value="spam" <?php Typecho_Request::callParameter('status', 'spam', 'selected="selected"'); ?>>
                        <?php _e('垃圾箱 (%d)', Typecho_API::factory('*Widget_Count', "from=table.comments&count=coid&status=spam")->num); ?>
                    </option>
				</select>
				<input rel="<?php $options->adminUrl('/images/icons/filter.gif'); ?>" type="submit" class="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>

            <form method="post" name="comment" id="comment" action="<?php $options->index('/Comments/Edit.do'); ?>">
			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="15%"><?php _e('日期'); ?></th>
					<th width="20%"><?php _e('作者'); ?></th>
					<th width="35%"><?php _e('摘要'); ?></th>
					<th width="22%"><?php _e('文章'); ?></th>
					<th width="7%"><?php _e('状态'); ?></th>
				</tr>
                <?php Typecho_API::factory('Widget_Comments_Admin')->to($comments); ?>
                <?php if($comments->have()): ?>
				<?php while($comments->get()): ?>
				<tr class="<?php $comments->status(); ?>">
					<td><input type="checkbox" name="coid[]" value="<?php $comments->coid(); ?>" /></td>
					<td><img width="16" height="16" alt="<?php $comments->mode(); ?>" src="<?php
                        switch($comments->mode)
                        {
                            case 'pingback':
                                $options->adminUrl('/images/icons/pingback.gif');
                                break;
                            case 'trackback':
                                $options->adminUrl('/images/icons/trackback.gif');
                                break;
                            case 'comment':
                            default:
                                $options->adminUrl('/images/icons/comment.gif');
                                break;
                        }
                    ?>" />
                    <?php $comments->dateWord(); ?></td>
					<td class="showline">
                    <?php if($comments->mail): ?><a href="mailto:<?php $comments->mail(); ?>"><img width="16" height="16" src="<?php $options->adminUrl('/images/icons/email.gif'); ?>" alt="email" /></a><?php endif; ?>
                    <?php $comments->author(); ?>
                    </td>
					<td class="overflow"><?php $comments->excerpt(30); ?></td>
					<td><a target="_blank" href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a></td>
					<td>
                    <?php
                        switch($comments->status)
                        {
                            case 'approved':
                                _e('呈现');
                                break;
                            case 'spam':
                                _e('垃圾');
                                break;
                            case 'waiting':
                                _e('待审核');
                                break;
                            default:
                                _e('不明');
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
