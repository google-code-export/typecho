<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
Typecho_API::factory('Widget_Contents_Page_Admin')->to($page);
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
            <?php require_once 'notice.php'; ?>
            
			<div class="table_nav">
            <form action="<?php $options->adminUrl('page-list.php'); ?>">
				<input rel="<?php $options->adminUrl('/images/icons/delete.gif'); ?>" type="button" class="button" value="<?php _e('删除'); ?>" onclick="page.submit();" />
				<input type="text" class="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
				<select name="status">
					<option value="all" <?php Typecho_Request::callParameter('status', 'all', 'selected="selected"'); ?>>
                        <?php _e('所有页面 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&type[]=page&type[]=page_draft")->num); ?>
                    </option>
					<option value="page" <?php Typecho_Request::callParameter('status', 'page', 'selected="selected"'); ?>>
                        <?php _e('已发布页面 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&type=page")->num); ?>
                    </option>
					<option value="draft" <?php Typecho_Request::callParameter('status', 'draft', 'selected="selected"'); ?>>
                        <?php _e('草稿 (%d)', Typecho_API::factory('*Widget_Count', "from=table.contents&count=cid&type=page_draft")->num); ?>
                    </option>
				</select>
				<input rel="<?php $options->adminUrl('/images/icons/filter.gif'); ?>" type="submit" class="submit" value="<?php _e('过滤'); ?>" />
            </form>
			</div>
            
            <form method="post" name="page" id="page" action="<?php $options->index('/Contents/Page/Edit.do'); ?>">
			<table class="latest">
				<tr class="nodrop nodrag">
					<th width="2%"><input type="checkbox" /></th>
					<th width="40%"><?php _e('标题'); ?></th>
					<th width="20%"><?php _e('作者'); ?></th>
					<th width="15%"><?php _e('发布日期'); ?></th>
					<th width="13%"><?php _e('评论'); ?></th>
					<th width="10%"><?php _e('状态'); ?></th>
				</tr>
                <?php if($page->have()): ?>
                <?php while($page->get()): ?>
                <tr>
                    <td><input type="checkbox" name="cid[]" value="<?php $page->cid(); ?>" />
                    <input type="hidden" name="sort[]" value="<?php $page->cid(); ?>" /></td>
                    <td><a href="<?php $options->adminUrl('/edit-page.php?cid=' . $page->cid); ?>"><?php $page->title(); ?></a>
                    <sup><?php $page->tags(','); ?></sup></td>
                    <td><?php $page->author(); ?></td>
                    <td><?php $page->dateWord(); ?></td>
                    <td><?php $page->commentsNum(_t('没有评论'), _t('仅有一条评论'), _t('%d条评论')); ?></td>
                    <td><?php if('page' == $page->type):
                    _e('<a href="%s" title="在新页面打开" target="_blank">已发布</a>', $page->permalink);
                    else:
                    _e('草稿');
                    endif;?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6"><?php _e('对不起,没有找到任何记录'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
            <input type="hidden" name="do" value="delete"/>
            <script src="<?php $options->adminUrl('/js/jquery.tablednd.js'); ?>" type="text/javascript"></script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $(".latest").tableDnD({
                        onDragClass: "drag",
                        onDrop: function(table, row) {
                            $("tr", table).removeClass("alt");
                            $("tr:even", table).addClass("alt");
                            $.ajax({
                                type: 'POST',
                                url: '<?php $options->index('/Contents/Page/Edit.do'); ?>',
                                data: $("input[@type=hidden]", table).serialize() + '&do=sort',
                                cache: false
                            });
                        }
                    });
                });
            </script>
            </form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
