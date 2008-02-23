<?php include 'header.php' ?>

<div id="menu">
	<?php $widget->pages(); ?>
</div>

<?php $archives->get('index'); ?>

<div>
	<?php while($archives->has()): ?>
		<h2><?php $archives->title(); ?></h2>
	<?php endwhile; ?>
</div>

<div><?php $archives->pageNav(); ?></div>

<div>
	<?php include 'sidebar.php' ?>
</div>

<?php include 'footer.php' ?>
