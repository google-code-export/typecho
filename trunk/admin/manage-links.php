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
		<form method="post" action="">
			<div class="table_nav">
                <input type="button" class="button" onclick="window.location = '<?php Typecho::widget('Options')->adminUrl('/manage-links.php'); ?>#edit'" value="<?php _e('增加链接'); ?>" />
				<input type="submit" class="submit" value="<?php _e('删除'); ?>" />
			</div>

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
					<td><input type="checkbox" id="" /></td>
					<td><a href="#">typecho.com</a></td>
					<td>official site</td>
					<td><a href="#">typecho.com</a></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <td colspan="4"><?php _e('没有任何链接,请在下方添加'); ?></td>
                <?php endif; ?>
			</table>
        </form>
        
        <form method="post" action="<?php Typecho::widget('Options')->index('DoLink.do'); ?>">
			<hr class="space" />
			<h4 id="edit"><?php if('update' == $link->do){ _e('编辑链接'); }else{ _e('增加链接'); } ?></h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label for="name"><?php _e('链接名称'); ?>*</label></td>
					<td><input type="text" class="text" id="name" name="name" style="width: 60%;" />
                    <small><?php _e('用一个名称描述此链接.'); ?></small></td>
				</tr>
				<tr>
					<td><label for="slug"><?php _e('链接地址'); ?>*</label></td>
					<td><input type="text" class="text" id="slug" name="slug" style="width: 60%;" />
                    <small><?php _e('此链接的网址,请用<strong>http://</strong>开头.'); ?></small></td>
				</tr>
				
				<tr>
					<td><label for="description"><?php _e('链接描述'); ?></label></td>
					<td><textarea id="description" name="description" rows="5" cols=""  style="width: 80%;"></textarea>
                    <small><?php _e('用简短的语言描述此链接,在某些模板中它将被显示.'); ?></small></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" class="submit" value="<?php if('update' == $link->do){ _e('编辑链接'); }else{ _e('增加链接'); } ?>" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->

<?php require_once 'footer.php'; ?>
