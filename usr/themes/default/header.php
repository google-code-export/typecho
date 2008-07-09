<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php $this->options->charset(); ?>" />
<title><?php
    /** 自定义标题 */
    switch ($this->options->archiveType)
    {
        /** 如果是分类 */
        case 'category':
            $this->options->archiveTitle('分类"%s" &raquo; ');
            break;
        /** 如果是标签 */
        case 'tag':
            $this->options->archiveTitle('标签"%s" &raquo; ');
            break;
        /** 如果是日期归档 */
        case 'date':
            $this->options->archiveTitle('按日期归档 %s &raquo; ');
            break;
        /** 如果是搜索 */
        case 'search':
            $this->options->archiveTitle('搜索关键字"%s" &raquo; ');
            break;
        /** 如果是文章或独立页面 */
        case 'post':
        case 'page':
            $this->options->archiveTitle('%s &raquo; ');
            break;
        default:
            $this->options->archiveTitle();
            break;
    }
?><?php $this->options->title(); ?></title>

<!-- 使用url函数转换相关路径 -->
<link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('css/960.gs.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('css/style.css'); ?>" />

<!-- 通过自有函数输出HTML头部信息 -->
<?php $this->header(); ?>
</head>

<body>
<div class="container_16 clearfix">
    <div id="header" class="clearfix">
		<div class="grid_16">
			<ul class="clearfix" id="nav_menu">
				<li><a href="<?php $this->options->siteUrl(); ?>" class="current">Home</a></li>
				<?php $this->widget('Widget/Contents/Page/List')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
                <?php if($this->widget('Widget/Users/Current')->hasLogin()): ?>
                    <li class="last"><a href="<?php $this->options->index('Logout.do'); ?>">Logout (<?php $this->widget('Widget/Users/Current')->screenName(); ?>)</a></li>
                    <li class="last"><a href="<?php $this->options->adminUrl(); ?>">Admin</a></li>
                <?php else: ?>
                    <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>">Login</a></li>
                <?php endif; ?>
			</ul>
		</div>
		<div class="grid_9">
	        <h1><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a></h1>
			<span><?php $this->options->description() ?></span>
		</div>
		<div class="grid_7">
			<div id="search">
                <form method="post" action="">
                <p><input type="text" name="keywords" class="text" size="40" /> <input type="submit" class="submit" value="Search" /></p>
                </form>
            </div>
		</div>
    </div><!-- end #header -->
