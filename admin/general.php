<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
			<form method="post" action="">
            <div class="table_nav">
                <input type="submit" class="submit" value="<?php _e('保存设置'); ?>" />
            </div>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label><?php _e('站点名称'); ?></label></td>
					<td><input type="text" class="text" name="title" style="width: 70%;" value="<?php $options->title(); ?>" /><small><?php _e('站点的名称将显示在网页的标题处.'); ?></small></td>
				</tr>
				<tr>
					<td><label><?php _e('站点描述'); ?></label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 90%;"><?php $options->description(); ?></textarea><small><?php _e('站点描述将显示在网页代码的头部.'); ?></small></td>
				</tr>
				<tr>
					<td><label><?php _e('关键词'); ?></label></td>
					<td><input type="text" class="text" id="" style="width: 70%;" value="<?php $options->keywords(); ?>" /><small><?php _e('请以半角逗号","分割多个关键字.'); ?></small></td>
				</tr>
				<tr>
					<td><label><?php _e('时区'); ?></label></td>
					<td><select id="" style="width: 20%;">
						<option value="">GMT +08:00</option>
						<option value="">GMT +09:00</option>
					</select></td>
				</tr>
			</table>
            <hr class="space" />
            <div class="table_nav">
                <input type="submit" class="submit" value="<?php _e('保存设置'); ?>" />
            </div>
			</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
