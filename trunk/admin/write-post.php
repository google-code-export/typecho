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

            <div class="typecho-post-option">
                <div class="typecho-post-area">
                    <form action="" method="post">
                        <label><?php _e('标题'); ?></label>
                        <p><input type="text" vlaue="" class="title" /></p>
                        <label><?php _e('内容'); ?></label>
                        <p><textarea cols="100" rows="15"></textarea></p>
                        <p class="submit">
                            <button><?php _e('保存'); ?></button>
                            <button><?php _e('发布'); ?></button>
                        </p>
                    </form>
                </div>                    
            </div>
            </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
