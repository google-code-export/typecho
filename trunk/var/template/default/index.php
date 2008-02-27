<html>
<head>
<title><?php $options->title(); ?></title>
</head>
<body>
<div>
	<?php while($archives->get()): ?>
		<h2><?php $archives->post_title(); ?></h2>
              <cite><?php $archives->time('Y年m月'); ?></cite>
	<?php endwhile; ?>
</div>

<div><?php $archives->pageNav(); ?></div>
</body>
</html>
