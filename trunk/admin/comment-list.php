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
				<input type="submit" value="<?php _e('展现'); ?>" />
				<input type="submit" value="<?php _e('垃圾'); ?>" />
				<input type="submit" value="<?php _e('待审核'); ?>" />
				<input type="submit" value="<?php _e('删除'); ?>" />
				<input type="text" id="" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<input type="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="10%"><?php _e('日期'); ?></th>
					<th width="15%"><?php _e('作者'); ?></th>
					<th width="45%"><?php _e('摘要'); ?></th>
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
                    <sup><?php $comments->mode(); ?></sup>
                    <sub>
                        <?php if($comments->url): ?><a target="_blank" href="<?php $comments->url(); ?>">网址</a><?php endif; ?>
                        <?php if($comments->mail): ?><a href="mailto:<?php $comments->mail(); ?>">邮件</a><?php endif; ?>
                    </sub>
                    </td>
					<td><?php $comments->excerpt(30); ?></td>
					<td><a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a></td>
					<td><?php $comments->status(); ?></td>
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
