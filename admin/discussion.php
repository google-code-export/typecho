<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div id="page">
			<form method="post" action="">
            <div class="table_nav">
                <input type="submit" value="<?php _e('保存设置'); ?>" />
            </div>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label><?php _e('评论日期格式'); ?></label></td>
					<td><input type="text" id="" style="width: 10%; margin-right: .5em;" value="F j, Y" /> / <input type="text" id="" style="width: 10%; margin-left: .5em;" value="g:i a" /><small>Get Support from <a href="http://php.net/date">PHP Manual</a></small></td>
				</tr>
				<tr>
					<td><label><?php _e('评论审核'); ?></label></td>
					<td><input type="radio" id="" name="cmt_mod" checked="checked" /> Yes <input type="radio" id="" name="cmt_mod" style="margin-left: 1em;" /> No</td>
				</tr>
				<tr>
					<td><label><?php _e('必须填写邮箱'); ?></label></td>
					<td><input type="radio" id="" name="email_required" checked="checked" /> Yes <input type="radio" id="" name="email_required" style="margin-left: 1em;" /> No</td>
				</tr>
				<tr>
					<td><label><?php _e('必须填写网址'); ?></label></td>
					<td><input type="radio" id="" name="site_required" /> Yes <input type="radio" id="" name="site_required" style="margin-left: 1em;" checked="checked" /> No</td>
				</tr>
				<tr>
					<td><label><?php _e('黑名单'); ?></label></td>
					<td><textarea id="" rows="8" cols=""  style="width: 70%;"></textarea><small>When a comment contains any of these words in its content, name, Url, e-mail, or IP, it will be marked as spam.</small></td>
				</tr>
			</table>
            <hr class="space" />
            <div class="table_nav">
                <input type="submit" value="<?php _e('保存设置'); ?>" />
            </div>
			</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
