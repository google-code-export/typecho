<html>
<head>
<title><?php Typecho::widget('Options')->title(); ?></title>
<?php Typecho::header(); ?>
</head>
<body>

<div>

<?php Typecho::widget('contents.Posts')->parse('<li><a href="{permalink}">{title}</a></li>'); ?>

<?php Typecho::widget('contents.Posts')->to($posts); ?>

<?php while($posts->get()): ?>

    <h2><a href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a></h2>
    
    <cite><?php $posts->date('Y-m-d'); ?> | <?php $posts->category(','); ?> | <?php $posts->tags(','); ?></cite>
    
    <div><?php $posts->content('阅读更多...'); ?></div>

<?php endwhile; ?>
</div>
</body>
</html>
