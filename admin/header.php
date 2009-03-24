<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$mootoolsCore = new Typecho_Widget_Helper_Layout('script',
array('type' => 'text/javascript', 'src' => Typecho_Common::url('javascript/mootools-1.2.1-core-yc.js', $options->adminUrl)));
$mootoolsCore->setClose(false);

$mootoolsMore = new Typecho_Widget_Helper_Layout('script',
array('type' => 'text/javascript', 'src' => Typecho_Common::url('javascript/mootools-1.2.1-more.js', $options->adminUrl)));
$mootoolsMore->setClose(false);

$typechoUi = new Typecho_Widget_Helper_Layout('script',
array('type' => 'text/javascript', 'src' => Typecho_Common::url('javascript/typecho-ui.source.js', $options->adminUrl)));
$typechoUi->setClose(false);

/** 使用对象方式引用资源 */
$header = new Typecho_Widget_Helper_Layout_Header();
$header->addItem(new Typecho_Widget_Helper_Layout('link',
array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => Typecho_Common::url('css/reset.source.css', $options->adminUrl))))
->addItem(new Typecho_Widget_Helper_Layout('link',
array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => Typecho_Common::url('css/grid.source.css', $options->adminUrl))))
->addItem(new Typecho_Widget_Helper_Layout('link',
array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => Typecho_Common::url('css/typecho.source.css', $options->adminUrl))))
->addItem($mootoolsCore)
->addItem($mootoolsMore)
->addItem($typechoUi);

/** 注册一个初始化插件 */
Typecho_Plugin::factory('admin/header.php')->header($header);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php $options->charset(); ?>" />
        <title><?php _e('%s - %s - Powered by Typecho', $menu->title, $options->title); ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <?php $header->render(); ?>
    </head>
    <body>
