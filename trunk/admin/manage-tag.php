<?php 
require_once 'common.php';
Typecho_API::factory('Widget_Metas_Tag_Edit')->to($tag);
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
        
        <?php require_once 'notice.php'; ?>
        
		<form method="get">
			<div class="table_nav">
                <input rel="<?php $options->adminUrl('/images/icons/add.gif'); ?>" type="button" class="button" onclick="window.location = '<?php $options->adminUrl('/manage-tag.php'); ?>#edit'" value="<?php _e('增加标签'); ?>" />
				<input rel="<?php $options->adminUrl('/images/icons/delete.gif'); ?>" type="button" class="button" onclick="$('#tag input[@name=do]').val('delete');tag.submit();" value="<?php _e('删除'); ?>" />
                <input type="text" class="text" style="width: 120px;" value="<?php _e('请输入合并入的标签'); ?>" onclick="value='';" id="merge" />
                <input rel="<?php $options->adminUrl('/images/icons/arrow_join.gif'); ?>" type="button" class="button" onclick="$('#tag input[@name=do]').val('merge');$('#tag input[@name=merge]').val($('#merge').val());tag.submit();" value="<?php _e('合并'); ?>" />
                <input type="text" class="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                <input rel="<?php $options->adminUrl('/images/icons/filter.gif'); ?>" type="submit" class="submit" value="<?php _e('过滤'); ?>" />
			</div>
        </form>
        
        <form method="post" name="tag" id="tag" action="<?php $options->index('/Metas/Tag/Edit.do'); ?>">
			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="40%"><?php _e('标签名称'); ?></th>
					<th width="20%"><?php _e('文章'); ?></th>
					<th width="39%"><?php _e('标签缩略名(用于自定义链接)'); ?></th>
				</tr>
                <?php Typecho_API::factory('Widget_Metas_Tag_Admin')->to($tags); ?>
                <?php if($tags->have()): ?>
                <?php while($tags->get()): ?>
                <tr>
					<td><input type="checkbox" name="mid[]" value="<?php $tags->mid(); ?>" />
                    <input type="hidden" name="sort[]" value="<?php $tags->mid(); ?>" /></td>
					<td><a href="<?php $tags->adminUrl('/manage-tag.php?mid=' . $categories->mid); ?>#edit">
                    <?php $tags->name(); ?></a>
                    </td>
					<td><a href="<?php $tags->permalink(); ?>">
                    <?php _e('%d篇', $tags->count); ?></a></td>
					<td><?php $tags->slug(); ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5"><?php _e('没有任何标签,请在下方添加'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
            
            <?php if($tags->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $tags->pageNav(); ?>
			</div>
            <?php endif; ?>
            
            <input type="hidden" name="do" value="delete" />
            <input type="hidden" name="merge" value="" />
        </form>
            
        <?php $tag->form()->render(); ?>

		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
