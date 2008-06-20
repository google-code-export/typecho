<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php $this->options->charset(); ?>" />
<title>Typecho</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('css/960.gs.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('css/style.css'); ?>" />
<?php $this->header(); ?>
</head>

<body>
<div class="container_16 clearfix">
    <div id="header" class="clearfix">
		<div class="grid_16">
			<ul class="clearfix" id="nav_menu">
				<li><a href="#" class="current">Home</a></li>
				<li><a href="#">About</a></li>
				<li><a href="#">Guestbook</a></li>
				<li><a href="#">Contact</a></li>
				<li><a href="#" style="float: right; margin-right: 0;">Login</a></li>
			</ul>
		</div>
		<div class="grid_9">
			<img src="<?php $this->options->themeUrl('images/header.png'); ?>" width="50" height="50" alt="" style="float: left; margin: 5px 10px 0 0;" />
	        <h1><a href="#"><?php $this->options->title() ?></a></h1>
			<span><?php $this->options->description() ?></span>
		</div>
		<div class="grid_7">
			<div id="search"><input type="text" class="text" size="40" /> <input type="submit" class="submit" value="Search" /></div>
		</div>
    </div><!-- end #header -->
