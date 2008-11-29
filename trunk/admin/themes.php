<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01">
<ul class="typecho-thumb-list">
    <?php for ($i = 0; $i < 12; $i++) { ?>
    <li>
    <h4 class="title"><a href="#">模板的名称</a></h4>
    <p class="thumb"><a href="#"><img
        src="http://img.mall.taobaocdn.com/malli/product/seller/2135/i4/571/401/T1pJ4aXgphlZBXXXXX.jpg"
        width="400" height="300" /></a></p>
    <p class="desption">Typecho 默认的皮肤，鄙视 70 买了个 iPod 还带套套</p>
    <?php if ($i == 6) { ?>
    <p class="current">当前模板</p>
    <?php } ?>
    </li>
    <?php } ?>
</ul>
            </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
