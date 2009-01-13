<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-16 start-01 typecho-content-panel">
                <h4><?php $user->name(); ?><cite>(<?php $user->screenName(); ?>)</cite></h4>
                <p><?php _e('目前有 <em>%s</em> 篇 Blog,并有 <em>%s</em> 条关于你的评论在已设定的 <em>%s</em> 个分类中.', 
                $stat->myPublishedPostsNum, $stat->myPublishedCommentsNum, $stat->categoriesNum); ?></p>
                <p><?php _e('最后登录: %s', Typecho_I18n::dateWord($user->logged  + $options->timezone, $options->gmtTime + $options->timezone)); ?></p>
                <h3><?php _e('撰写设置'); ?></h3>
                <div class="typecho-option-focus typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                    <?php Typecho_Widget::widget('Widget_Users_Profile')->optionsForm()->render(); ?>
                </div>
                <h3><?php _e('设置密码'); ?></h3>
                <div class="typecho-option-focus typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                    <?php Typecho_Widget::widget('Widget_Users_Profile')->passwordForm()->render(); ?>
                </div>
            </div>
            <div class="column-08 start-17 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                <?php Typecho_Widget::widget('Widget_Users_Profile')->form()->render(); ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'common-js.php';
include 'copyright.php';
?>
