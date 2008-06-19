<?php 
require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php $menu->title(); ?></h2>
		<div id="page">

			<table class="latest" id="plugin">
				<tr>
					<th width="15%"><?php _e('名称'); ?></th>
					<th width="5%"><?php _e('版本'); ?></th>
					<th width="50%"><?php _e('描述'); ?></th>
					<th width="14%"><?php _e('作者'); ?></th>
					<th width="6%"><?php _e('状态'); ?></th>
					<th width="10%"><?php _e('操作'); ?></th>
				</tr>
                <?php $plugins = Typecho_Plugin::listAll(); ?>
                <?php foreach($plugins as $plugin): ?>
				<tr>
					<td><?php echo $plugin['title']; ?></td>
					<td><a target="_blank" href="<?php echo $plugin['check']; ?>"><?php echo $plugin['version']; ?></a></td>
					<td><?php echo $plugin['description']; ?></td>
					<td><a target="_blank" href="<?php echo $plugin['homepage']; ?>"><?php echo $plugin['author']; ?></a></td>
					<td><?php echo $plugin['status']; ?></td>
					<td><?php if($plugin['activated']): ?>
                        <a href="<?php $options->index('DoPlugin.do?do=deactivate&plugin=' . $plugin['name']); ?>"><?php _e('禁用'); ?></a>
                    <?php else: ?>
                        <a href="<?php $options->index('DoPlugin.do?do=activate&plugin=' . $plugin['name']); ?>"><?php _e('激活'); ?></a>
                    <?php endif; ?></td>
				</tr>
                <?php endforeach; ?>
			</table>
		</div><!-- end #page -->
	</div><!-- end #main -->

<?php require_once 'footer.php'; ?>
