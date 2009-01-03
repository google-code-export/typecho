<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$panel = Typecho_Request::getParameter('panel');
$panelTable = unserialize($options->panelTable);

if (!isset($panelTable['file']) || !in_array(urlencode($panel), $panelTable['file'])) {
    throw new Typecho_Plugin_Exception(_t('页面不存在'), 404);
}
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <?php require_once $panel; ?>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
