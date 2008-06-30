<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">
            <?php require_once 'notice.php'; ?>
			<table class="latest" id="plugin">
				<tr>
					<th width="15%"><?php _e('名称'); ?></th>
					<th width="5%"><?php _e('版本'); ?></th>
					<th width="40%"><?php _e('描述'); ?></th>
					<th width="14%"><?php _e('作者'); ?></th>
					<th width="11%"><?php _e('状态'); ?></th>
					<th width="15%"><?php _e('操作'); ?></th>
				</tr>
                <?php Typecho_API::factory('Widget_Plugins_List')->to($plugins); ?>
                <?php foreach($plugins as $plugin): ?>
				<tr>
					<td><?php echo $plugin['title']; ?></td>
					<td><a target="_blank" href="<?php echo $plugin['check']; ?>"><?php echo $plugin['version']; ?></a></td>
					<td><?php echo $plugin['description']; ?></td>
					<td><a target="_blank" href="<?php echo $plugin['homepage']; ?>"><?php echo $plugin['author']; ?></a></td>
					<td><?php if($plugin['activated']):
                        _e('已激活');
                    else:
                        _e('已禁用');
                    endif; ?></td>
					<td><?php if($plugin['activated']): ?>
                        <a href="<?php $options->index('/Plugins/Edit.do?do=deactivate&plugin=' . $plugin['name']); ?>"><?php _e('禁用'); ?></a>
                        <?php if($plugin['config']): ?>
                            | <a href="<?php $options->index('/Plugins/Edit.do?do=config&plugin=' . $plugin['name']); ?>"><?php _e('配置'); ?></a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php $options->index('/Plugins/Edit.do?do=activate&plugin=' . $plugin['name']); ?>"><?php _e('激活'); ?></a>
                    <?php endif; ?>
                    </td>
				</tr>
                <?php endforeach; ?>
			</table>
		</div><!-- end #page -->
	</div><!-- end #main -->

<?php require_once 'footer.php'; ?>
