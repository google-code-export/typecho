<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
Typecho_API::factory('Widget_Users_Admin')->to($users);
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
		<form method="post" action="">
			<div class="table_nav">
				<input type="submit" value="Delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="15%"><?php _e('用户名'); ?></th>
					<th width="25%"><?php _e('个人网站'); ?></th>
					<th width="20%"><?php _e('电子邮件'); ?></th>
					<th width="14%"><?php _e('权限'); ?></th>
					<th width="15%"><?php _e('最后登录'); ?></th>
				</tr>
                
                <?php if($users->have()): ?>
				<?php while($users->get()): ?>
				<tr>
					<td><input type="checkbox" name="uid[]" value="<?php $users->uid(); ?>" /></td>
					<td><a href="#"><?php $users->name(); ?></a></td>
					<td><a target="_blank" href="<?php $users->url(); ?>"><?php $users->url(); ?></a></td>
					<td><a href="mailto:<?php $users->mail(); ?>"><?php $users->mail(); ?></a></td>
					<td><?php switch($users->group)
                    {
                        case 'visitor':
                            _e('访问者');
                            break;
                        case 'subscriber':
                            _e('关注者');
                            break;
                        case 'contributor':
                            _e('贡献者');
                            break;
                        case 'editor':
                            _e('编辑');
                            break;
                        case 'administrator':
                        default:
                            _e('管理员');
                            break;
                    }
                    ?></td>
					<td><?php echo Typecho_I18n::dateWord($users->logged, $options->gmtTime); ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <td colspan="6"><?php _e('没有找到任何用户'); ?></td>
                <?php endif; ?>
			</table>
			<hr class="space" />
			<h4>Add User</h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label>Username</label></td>
					<td><input type="text" id="" style="width: 40%;" /></td>
				</tr>
				<tr>
					<td><label>Password (twice)</label></td>
					<td><input type="password" id="" style="width: 22%; margin-right: 15px;" /><input type="password" id="" style="width: 22%;" /></td>
				</tr>
				<tr>
					<td><label>E-mail</label></td>
					<td><input type="text" id="" style="width: 40%;" /></td>
				</tr>
				<tr>
					<td><label>Website</label></td>
					<td><input type="text" id="" value="http://" style="width: 40%;" /></td>
				</tr>
				<tr>
					<td><label>About Yourself</label></td>
					<td><textarea id="" rows="5" cols=""  style="width: 70%;"></textarea></td>
				</tr>
				<tr>
					<td><label>Privileges</label></td>
					<td>
					<select id="" style="width: 160px;">
						<option value="" selected="selected">Administrator</option>
						<option value="">Author</option>
					</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Add User" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
