<?php
include 'common.php';

if (!Typecho_Request::isAjax()):
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-22 start-02">
<?php endif; ?>
                <?php Typecho_Widget::widget('Widget_Plugins_Config')->config()->render(); ?>
<?php if (!Typecho_Request::isAjax()): ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'common-js.php';
include 'copyright.php';
endif;
?>
