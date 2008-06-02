<?php 
require_once 'common.php';
Typecho::widget('Metas.EditTag')->to($tag);
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div id="page">
        
        <?php require_once 'notice.php'; ?>
        
		<form method="get">
			<div class="table_nav">
                <input type="button" class="button" onclick="window.location = '<?php Typecho::widget('Options')->adminUrl('/manage-tag.php'); ?>#edit'" value="<?php _e('增加标签'); ?>" />
				<input type="button" class="button" onclick="$('#tag input[@name=do]').val('delete');tag.submit();" value="<?php _e('删除'); ?>" />
                <input type="text" class="text" style="width: 120px;" value="<?php _e('请输入合并入的标签'); ?>" onclick="value='';" id="merge" />
                <input type="button" class="button" onclick="$('#tag input[@name=do]').val('merge');$('#tag input[@name=merge]').val($('#merge').val());tag.submit();" value="<?php _e('合并'); ?>" />
                <input type="text" class="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                <input type="submit" class="submit" value="<?php _e('过滤'); ?>" />
			</div>
        </form>
        
        <form method="post" name="tag" id="tag" action="<?php Typecho::widget('Options')->index('DoTag.do'); ?>">
            <p>
                <?php Typecho::widget('Tags')->to($tags); ?>
                <?php if($tags->have()): ?>
				<?php while($tags->get()): ?>
				<label class="table_nav" for="tag-<?php $tags->mid(); ?>">
					<input type="checkbox" name="mid[]" id="tag-<?php $tags->mid(); ?>" value="<?php $tags->mid(); ?>" />
					<a style="<?php $tags->split('font-size:8pt', 'font-size:11pt', 'font-size:13pt;font-weight:bold'); ?>" href="<?php Typecho::widget('Options')->adminUrl('/manage-tag.php?mid=' . $tags->mid); ?>#edit"><?php $tags->name(); ?></a>
					<sup><a href="<?php $tags->permalink(); ?>" target="_blank"><?php $tags->count(); ?></a></sup>
				</label>&nbsp;&nbsp;
                <?php endwhile; ?>
                <?php else: ?>
                <span><?php if(NULL === TypechoRequest::getParameter('keywords')){ _e('没有任何标签,请在下方添加'); }
                else{ _e('没有找到匹配的标签'); } ?></span>
                <?php endif; ?>
            </p>
            
            <input type="hidden" name="do" value="delete" />
            <input type="hidden" name="merge" value="" />
        </form>
            
        <form method="post" action="<?php Typecho::widget('Options')->index('DoTag.do'); ?>">
			<hr class="space" />
			<h4 id="edit"><?php if('update' == $tag->do){ _e('编辑标签'); }else{ _e('增加标签'); } ?></h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label for="name"><?php _e('标签名称'); ?>*</label></td>
					<td><input type="text" class="text" id="name" name="name" style="width: 60%;" value="<?php $tag->name(); ?>" />
                    <?php Typecho::widget('Notice')->display('name', '<span class="detail">%s</span>'); ?>
                    <small><?php _e('这是标签在站点中显示的名称.'); ?></small></td>
				</tr>
				<tr>
					<td><label for="slug"><?php _e('标签缩略名'); ?></label></td>
					<td><input type="text" class="text" id="slug" name="slug" style="width: 60%;" value="<?php $tag->slug(); ?>" />
                    <?php Typecho::widget('Notice')->display('slug', '<span class="detail">%s</span>'); ?>
                    <small><?php _e('标签缩略名用于创建友好的链接形式,如果留空则默认使用标签名称.'); ?></small></td>
				</tr>
				<tr>
					<td><input type="hidden" name="do" value="<?php $tag->do(); ?>" />
                    <input type="hidden" name="mid" value="<?php $tag->mid(); ?>" /></td>
					<td><input type="submit" class="submit" value="<?php if('update' == $tag->do){ _e('编辑标签'); }else{ _e('增加标签'); } ?>" /></td>
				</tr>
			</table>
		</form>

		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
