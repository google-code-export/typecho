<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$header = '<link rel="stylesheet" type="text/css" href="' . Typecho_Common::url('css/bootstrap.css?v=' . $suffixVersion, $options->adminUrl) . '" /> 
<link rel="stylesheet" type="text/css" href="' . Typecho_Common::url('css/bootstrap-responsive.css?v=' . $suffixVersion, $options->adminUrl) . '" /> 
<link rel="stylesheet" type="text/css" href="' . Typecho_Common::url('css/typecho.css?v=' . $suffixVersion, $options->adminUrl) . '" />';

/** 注册一个初始化插件 */
$header = Typecho_Plugin::factory('admin/header.php')->header($header);

?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php $options->charset(); ?>" />
        <title><?php _e('%s - %s - Powered by Typecho', $menu->title, $options->title); ?></title>
        <meta name="robots" content="noindex,nofollow" />
        <?php echo $header; ?>
        <!--[if lt IE 9]>
        <script src="<?php $options->adminUrl('js/html5.js?v=' . $suffixVersion); ?>"></script>
        <![endif]-->
    </head>
    <body<?php if (isset($bodyClass)) {echo ' class="' . $bodyClass . '"';} ?>>
