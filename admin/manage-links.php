<?php 
require_once 'common.php';
Typecho::widget('Metas.EditLink')->to($link);
require_once 'header.php';
require_once 'menu.php';
Typecho::widget('Links')->to($links);
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div id="page">
        <?php require_once 'notice.php'; ?>
        
		<form method="get">
			<div class="table_nav">
                <input type="button" class="button" onclick="window.location = '<?php Typecho::widget('Options')->adminUrl('/manage-links.php'); ?>#edit'" value="<?php _e('增加链接'); ?>" />
				<input type="button" class="button" onclick="$('#link input[@name=do]').val('delete');link.submit();" value="<?php _e('删除'); ?>" />
                <input type="text" class="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                <input type="submit" class="submit" value="<?php _e('过滤'); ?>" />
			</div>
        </form>

        <form method="post" name="link" id="link" action="<?php Typecho::widget('Options')->index('DoLink.do'); ?>">
			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%"><?php _e('名称'); ?></th>
					<th width="49%"><?php _e('描述'); ?></th>
					<th width="30%"><?php _e('地址'); ?></th>
				</tr>
                
                <?php if($links->have()): ?>
				<?php while($links->get()): ?>
				<tr>
					<td><input type="checkbox" name="mid[]" value="<?php $links->mid(); ?>" /></td>
					<td><a href="<?php Typecho::widget('Options')->adminUrl('/manage-links.php?mid=' . $links->mid); ?>#edit"><?php $links->name(); ?></a></td>
					<td><?php $links->description(); ?></td>
					<td><a href="<?php $links->url(); ?>"><?php $links->url(); ?></a></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <td colspan="4"><?php if(NULL === TypechoRequest::getParameter('keywords')){ _e('没有任何链接,请在下方添加'); }
                else{ _e('没有找到匹配的链接'); } ?></td>
                <?php endif; ?>
			</table>
            <input type="hidden" name="do" value="delete" />
        </form>
        
        <form method="post" action="<?php Typecho::widget('Options')->index('DoLink.do'); ?>">
			<hr class="space" />
			<h4 id="edit"><?php if('update' == $link->do){ _e('编辑链接'); }else{ _e('增加链接'); } ?></h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label for="name"><?php _e('链接名称'); ?>*</label></td>
					<td><input type="text" class="text" id="name" name="name" value="<?php $link->name(); ?>" style="width: 60%;" />
                    <?php Typecho::widget('Notice')->display('name', '<span class="detail">%s</span>'); ?>
                    <small><?php _e('用一个名称描述此链接.'); ?></small></td>
				</tr>
				<tr>
					<td><label for="slug"><?php _e('链接地址'); ?>*</label></td>
					<td><input type="text" class="text" id="slug" name="slug" value="<?php $link->slug(); ?>" style="width: 60%;" />
                    <?php Typecho::widget('Notice')->display('slug', '<span class="detail">%s</span>'); ?>
                    <small><?php _e('此链接的网址,请用<strong>http://</strong>开头.'); ?></small></td>
				</tr>
				
				<tr>
					<td><label for="description"><?php _e('链接描述'); ?></label></td>
					<td><textarea id="description" name="description" rows="5" cols=""  style="width: 80%;"><?php $link->description(); ?></textarea>
                    <small><?php _e('用简短的语言描述此链接,在某些模板中它将被显示.'); ?></small></td>
				</tr>
				<tr>
					<td><input type="hidden" name="do" value="<?php $link->do(); ?>" />
                    <input type="hidden" name="mid" value="<?php $link->mid(); ?>" /></td>
					<td><input type="submit" class="submit" value="<?php if('update' == $link->do){ _e('编辑链接'); }else{ _e('增加链接'); } ?>" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->

<?php require_once 'footer.php'; ?>
