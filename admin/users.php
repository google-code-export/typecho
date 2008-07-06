<?php 
require_once 'common.php';
Typecho_API::factory('Widget_Users_Edit')->to($user);
require_once 'header.php';
require_once 'menu.php';
Typecho_API::factory('Widget_Users_Admin')->to($users);
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
            <?php require_once 'notice.php'; ?>
        
            <form action="<?php $options->adminUrl('users.php'); ?>">
			<div class="table_nav">
                <input rel="<?php $options->adminUrl('/images/icons/add.gif'); ?>" type="button" class="button" onclick="window.location = '<?php $options->adminUrl('/users.php'); ?>#edit'" value="<?php _e('增加用户'); ?>" />
				<input rel="<?php $options->adminUrl('/images/icons/delete.gif'); ?>" type="button" class="button" value="<?php _e('删除'); ?>" onclick="users.submit();" />
                <input type="text" class="text" style="width: 200px;" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                <input rel="<?php $options->adminUrl('/images/icons/filter.gif'); ?>" type="submit" class="submit" value="<?php _e('过滤'); ?>" />
			</div>
            </form>

            <form id="users" name="users" action="<?php $options->index('/Users/Edit.do'); ?>" method="post">
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
					<td>
                    <?php if(1 != $users->uid): ?>
                    <input type="checkbox" name="uid[]" value="<?php $users->uid(); ?>" />
                    <?php endif; ?>
                    </td>
					<td><a href="<?php $options->adminUrl('users.php?uid=' . $users->uid); ?>#edit"><?php $users->name(); ?></a><?php if(1 == $users->uid): ?> <sup><?php _e('初始用户'); ?></sup><?php endif; ?></td>
					<td><a target="_blank" href="<?php $users->url(); ?>"><?php $users->domainPath(); ?></a></td>
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
					<td>
                    <?php if($users->logged > 0): ?>
                    <?php echo Typecho_I18n::dateWord($users->logged + $options->timezone, $options->gmtTime + $options->timezone); ?>
                    <?php else: ?>
                    <?php _e('从未'); ?>
                    <?php endif; ?>
                    </td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <td colspan="6"><?php _e('没有找到任何用户'); ?></td>
                <?php endif; ?>
			</table>
            <input type="hidden" name="do" value="delete"/>
            </form>
            
            <?php if($users->have()): ?>
			<div class="table_nav page_nav">
				<?php _e('分页:'); ?> <?php $users->pageNav(); ?>
			</div>
            <?php endif; ?>
            
			<?php $user->form()->render(); ?>
            
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
