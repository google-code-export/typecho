<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
		<form action="" method="post" enctype="application/x-www-form-urlencoded">
		<table class="setting">
		<tr><th width="20%" /><th width="80%" /></tr>
		<tr><td colspan="2" class="submit"><input type="submit" value="更新资料" class="submit" /></td></tr>
		<tr><td><label for="username">用户名</label></td><td><input name="username" id="username" value="admin" type="text" class="text" style="width:50%" disabled="disabled" /><small>用户名无法更改.</small></td></tr>
		<tr><td><label for="nickname">用户昵称</label></td><td><input name="nickname" id="nickname" value="admin" type="text" class="text" style="width:50%" /><small>显示在文章作者上的名字.</small></td></tr>
		<tr><td><label for="website">我的网站</label></td><td><input name="website" id="website" value="http://" type="text" class="text" style="width:60%" /></td></tr>
		<tr><td><label for="biographical">个人简介</label></td><td><textarea name="biographical" id="biographical" style="width:90%" rows="5">这是我的个人简介</textarea><small>这里填写一些简单的个人履历信息.</small></td></tr>
		<tr><td><label for="qq">QQ</label></td><td><input name="qq" id="qq" value="" type="text" class="text" style="width:60%" /></td></tr>
		<tr><td><label for="msn">MSN</label></td><td><input name="msn" id="msn" value="" type="text" class="text" style="width:60%" /></td></tr>
		<tr><td><label for="gtalk">Gtalk</label></td><td><input name="gtalk" id="gtalk" value="" type="text" class="text" style="width:60%" /></td></tr>
		<tr><td><label for="email">E-mail*</label></td><td><input name="email" id="email" value="admin@admin.com" type="text" class="text" style="width:60%" /><small>用于找回密码和接收信件，并显示你的 Gravatar 头像.</small></td></tr>
		<tr><td><label for="password">修改密码</label></td><td><input name="password" id="password" type="password" class="password" style="width:30%" /><small>不修改密码请留空.</small><br /><input name="repassword" id="repassword" type="password" class="password" style="width:30%" /><small>再输入一次新密码.</small></td></tr>
		<input name="do" id="do" type="hidden" value="update" />
		<tr><td colspan="2" class="submit"><input type="submit" value="更新资料" class="submit" /></td></tr>
		</table>
		<hr class="space" />
		</form>

		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
