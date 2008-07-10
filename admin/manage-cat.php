<?php 
require_once 'common.php';
Typecho_API::factory('Widget_Metas_Category_Edit')->to($category);
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
        <?php require_once 'notice.php'; ?>
        
		<form method="post" id="category" name="category" action="<?php $options->index('/Metas/Category/Edit.do'); ?>">
			<div class="table_nav">
                <input rel="<?php $options->adminUrl('/images/icons/add.gif'); ?>" type="button" class="button" onclick="window.location = '<?php $options->adminUrl('/manage-cat.php'); ?>#edit'" value="<?php _e('增加分类'); ?>" />
				<input rel="<?php $options->adminUrl('/images/icons/delete.gif'); ?>" type="button" class="button" onclick="$('#category input[@name=do]').val('delete');category.submit();" value="<?php _e('删除'); ?>" />
				<select name="merge" style="width: 160px;">
                    <?php Typecho_API::factory('Widget_Query', 'from=table.metas&type=category&order=sort&sort=ASC')
                    ->parse('<option value="{mid}">{name}</option>'); ?>
				</select>
				<input rel="<?php $options->adminUrl('/images/icons/arrow_join.gif'); ?>" type="button" class="button" onclick="$('#category input[@name=do]').val('merge');category.submit();" value="<?php _e('合并'); ?>" />
                <input type="hidden" name="do" value="delete" />
			</div>

			<table class="latest">
				<tr class="nodrop nodrag">
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%"><?php _e('分类名称'); ?></th>
					<th width="50%"><?php _e('分类描述'); ?></th>
					<th width="10%"><?php _e('文章'); ?></th>
					<th width="19%"><?php _e('分类缩略名'); ?></th>
				</tr>
                <?php Typecho_API::factory('Widget_Metas_Category_List')->to($categories); ?>
                <?php if($categories->have()): ?>
                <?php while($categories->get()): ?>
                <tr>
					<td><input type="checkbox" name="mid[]" value="<?php $categories->mid(); ?>" />
                    <input type="hidden" name="sort[]" value="<?php $categories->mid(); ?>" /></td>
					<td><a href="<?php $options->adminUrl('/manage-cat.php?mid=' . $categories->mid); ?>#edit">
                    <?php $categories->name(); ?></a>
                    <?php if($options->defaultCategory == $categories->mid): ?> <sup><strong><?php _e('默认分类'); ?></strong></sup>
                    <?php else: ?> <sub><a href="<?php $options->index('/Metas/Category/Edit.do'); ?>?do=default&mid=<?php $categories->mid(); ?>"><?php _e('设为默认'); ?></a></sub><?php endif; ?>
                    </td>
					<td><?php $categories->description(); ?></td>
					<td><a href="<?php $categories->permalink(); ?>">
                    <?php _e('%d篇', $categories->count); ?></a></td>
					<td><?php $categories->slug(); ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5"><?php _e('没有任何分类,请在下方添加'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
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
                                url: '<?php $options->index('/Metas/Category/Edit.do'); ?>',
                                data: $("input[@type=hidden]", table).serialize() + '&do=sort',
                                contentType: "application/x-www-form-urlencoded; charset=<?php $options->charset(); ?>",
                                cache: false
                            });
                        }
                    });
                });
            </script>
        </form>

        <?php $category->form()->render(); ?>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
