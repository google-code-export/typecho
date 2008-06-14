<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php $this->options->charset(); ?>" />
<title><?php $this->options->title(); ?></title>
<?php $this->header(); ?>
</head>
<body>

<div>

<?php $this->parse('<li><a href="{permalink}">{title}</a></li>'); ?>

</div>
</body>
</html>
