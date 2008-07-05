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
            <p>
                <?php Typecho_API::factory('Widget_Metas_Tag_Cloud')->to($tags); ?>
                <?php if($tags->have()): ?>
				<?php while($tags->get()): ?>
				<label class="table_nav" for="tag-<?php $tags->mid(); ?>">
					<input type="checkbox" name="mid[]" id="tag-<?php $tags->mid(); ?>" value="<?php $tags->mid(); ?>" />
					<a style="<?php $tags->split('font-size:8pt', 'font-size:11pt', 'font-size:13pt;font-weight:bold'); ?>" href="<?php $options->adminUrl('/manage-tag.php?mid=' . $tags->mid); ?>#edit"><?php $tags->name(); ?></a>
					<sup><a href="<?php $tags->permalink(); ?>" target="_blank"><?php $tags->count(); ?></a></sup>
				</label>&nbsp;&nbsp;
                <?php endwhile; ?>
                <?php else: ?>
                <span><?php if(NULL === Typecho_Request::getParameter('keywords')){ _e('没有任何标签,请在下方添加'); }
                else{ _e('没有找到匹配的标签'); } ?></span>
                <?php endif; ?>
            </p>
            
            <input type="hidden" name="do" value="delete" />
            <input type="hidden" name="merge" value="" />
        </form>
            
        <?php $tag->form()->render(); ?>

		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
