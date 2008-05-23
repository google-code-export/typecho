<html>
<head>
<title><?php Typecho::widget('Options')->archiveTitle('%s - '); ?><?php Typecho::widget('Options')->title(); ?></title>
<?php Typecho::header(); ?>
</head>
<body>

<div>

<!-- 第二种调用方法,调用集成方法生成列表 -->
<?php Typecho::widget('contents.Posts')->parse('<li><a href="{permalink}">{title}</a></li>'); ?>

<!-- 第三种调用方法,每行输出 -->
<?php Typecho::widget('contents.Posts')->to($posts); ?>

<?php while($posts->get()): ?>

    <h2><a href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a></h2>
    
    <cite><?php $posts->date('Y-m-d'); ?> | <?php $posts->category(','); ?></cite>
    
    <div><?php $posts->content('阅读更多...'); ?></div>

<?php endwhile; ?>
</div>
</body>
</html>
