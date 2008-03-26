<html>
<head>
<title><?php $options->title(); ?></title>
</head>
<body>

<div>
<!-- 第一种调用方法,调用公开方法生成列表 -->
<?php widget('contents.Posts')->output('li', '_blank', 'list', true, 10, '[...]'); ?>

<!-- 第二种调用方法,调用集成方法生成列表 -->
<?php widget('contents.Posts')->parse('<li><a href="{permalink}">{title}</a></li>'); ?>

<!-- 第三种调用方法,每行输出 -->
<?php widget('contents.Posts')->to($posts); ?>

<?php while($posts->get()): ?>

    <h2><a href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a></h2>
    
    <cite><?php $post->date('Y-m-d'); ?> | <?php $post->category(','); ?></cite>
    
    <div><?php $post->content('阅读更多...'); ?></div>

<?php endwhile; ?>
</div>
</body>
</html>
