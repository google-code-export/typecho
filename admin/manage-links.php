<?php 
require_once 'common.php';
Typecho_API::factory('Widget_Metas_Link_Edit')->to($link);
require_once 'header.php';
require_once 'menu.php';
Typecho_API::factory('Widget_Metas_Link_List')->to($links);
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
        <?php require_once 'notice.php'; ?>
        
		<form method="post" action="<?php $options->index('/Metas/Link/Edit.do'); ?>">
			<div class="table_nav">
                <input type="button" class="button" onclick="window.location = '<?php $options->adminUrl('/manage-links.php'); ?>#edit'" value="<?php _e('增加链接'); ?>" />
				<input type="submit" class="submit" value="<?php _e('删除'); ?>" />
                <input type="hidden" name="do" value="delete" />
			</div>

			<table class="latest">
				<tr class="nodrop">
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%"><?php _e('名称'); ?></th>
					<th width="49%"><?php _e('描述'); ?></th>
					<th width="30%"><?php _e('地址'); ?></th>
				</tr>
                
                <?php if($links->have()): ?>
				<?php while($links->get()): ?>
				<tr>
					<td><input type="checkbox" name="mid[]" value="<?php $links->mid(); ?>" />
                    <input type="hidden" name="sort[]" value="<?php $links->mid(); ?>" /></td>
					<td><a href="<?php $options->adminUrl('/manage-links.php?mid=' . $links->mid); ?>#edit"><?php $links->name(); ?></a></td>
					<td><?php $links->description(); ?></td>
					<td><a target="_blank" href="<?php $links->url(); ?>"><?php $links->url(); ?></a></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <td colspan="4"><?php _e('没有任何链接,请在下方添加'); ?></td>
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
                                url: '<?php $options->index('/Metas/Link/Edit.do'); ?>',
                                data: $("input[@type=hidden]", table).serialize() + '&do=sort',
                                cache: false
                            });
                        }
                    });
                });
            </script>
        </form>
        
        <?php $link->form()->render(); ?>
		</div><!-- end #page -->
	</div><!-- end #main -->

<?php require_once 'footer.php'; ?>
