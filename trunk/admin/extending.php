<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$panel = Typecho_Request::getParameter('panel');
$panelTable = unserialize($options->panelTable);
if (!in_array($panel, $panelTable)) {
    throw new Typecho_Plugin_Exception(_t('页面不存在'), 404);
}
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <?php require_once $panel; ?>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
