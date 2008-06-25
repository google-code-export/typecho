<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
Typecho_API::factory('Widget_Contents_Post_Admin')->to($posts);
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
            <?php require_once 'notice.php'; ?>
            
			<div class="table_nav">
            <form action="<?php $options->adminUrl('post-list.php'); ?>">
				<input type="button" class="button" value="<?php _e('删除'); ?>" onclick="post.submit();" />
				<input type="text" class="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<?php Typecho_API::factory('Widget_Query', 'from=table.metas&type=category&order=sort&sort=ASC')->to($category); ?>
                <select name="category" style="width: 160px;">
                <option value=""><?php _e('所有分类'); ?></option>
                <?php while($category->get()): ?>
                    <option value="<?php $category->mid(); ?>" <?php Typecho_Request::callParameter('category', $category->mid, 'selected="selected"'); ?>>
                        <?php $category->name(); ?> (<?php $category->count(); ?>)
                    </option>
                <?php endwhile; ?>
				</select>
				<select name="status">
					<option value="my" <?php Typecho_Request::callParameter('status', 'my', 'selected="selected"'); ?>>
                        <?php _e('我的所有文章 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&author={$access->uid}&type[]=post&type[]=draft")->num); ?>
                    </option>
					<option value="myPost" <?php Typecho_Request::callParameter('status', 'myPost', 'selected="selected"'); ?>>
                        <?php _e('我的已发布文章 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&author={$access->uid}&type=post")->num); ?>
                    </option>
					<option value="myDraft" <?php Typecho_Request::callParameter('status', 'myDraft', 'selected="selected"'); ?>>
                        <?php _e('我的草稿 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&author={$access->uid}&type=draft")->num); ?>
                    </option>
                    <?php if($access->pass('editor', true)): ?>
                    <option value="all" <?php Typecho_Request::callParameter('status', 'all', 'selected="selected"'); ?>>
                        <?php _e('所有文章 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&type[]=post&type[]=draft")->num); ?>
                    </option>
					<option value="allPost" <?php Typecho_Request::callParameter('status', 'allPost', 'selected="selected"'); ?>>
                        <?php _e('所有已发布的文章 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&type=post")->num); ?>
                    </option>
					<option value="allDraft" <?php Typecho_Request::callParameter('status', 'allDraft', 'selected="selected"'); ?>>
                        <?php _e('所有草稿 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&type=draft")->num); ?>
                    </option>
                    <?php endif; ?>
				</select>
				<input type="submit" class="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>
            
            <form method="post" name="post" id="post" action="<?php $options->index('DoPost.do'); ?>">
			<table class="latest">
				<tr>
					<th width="2%"><input type="checkbox" /></th>
					<th width="30%"><?php _e('标题'); ?></th>
					<th width="10%"><?php _e('作者'); ?></th>
					<th width="15%"><?php _e('发布日期'); ?></th>
					<th width="20%"><?php _e('分类'); ?></th>
					<th width="13%"><?php _e('评论'); ?></th>
					<th width="10%"><?php _e('状态'); ?></th>
				</tr>
                <?php if($posts->have()): ?>
                <?php while($posts->get()): ?>
                <tr>
                    <td><input type="checkbox" name="cid[]" value="<?php $posts->cid(); ?>" /></td>
                    <td><a href="<?php $options->adminUrl('/edit.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a>
                    <sup><?php $posts->tags(','); ?></sup></td>
                    <td><?php $posts->author(); ?></td>
                    <td><?php $posts->dateWord(); ?></td>
                    <td><?php $posts->category(' | '); ?></td>
                    <td><?php $posts->commentsNum('没有评论', '仅有一条评论', '%d条评论'); ?></td>
                    <td><?php if('post' == $posts->type):
                    _e('<a href="%s" title="在新页面打开" target="_blank">已发布</a>', $posts->permalink);
                    else:
                    _e('草稿');
                    endif;?>
                    <?php if(NULL != $posts->password): ?><sup><strong><?php _e('密码'); ?></strong></sup><?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7"><?php _e('对不起,没有找到任何记录'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
            <input type="hidden" name="do" value="delete"/>
            </form>
            
            <?php if($posts->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $posts->pageNav(); ?>
			</div>
            <?php endif; ?>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
