<?php
include 'common.php';
$title = _t('登录到%s - Powered by Typecho', $options->title);
include 'header.php';
?>
<div class="body body-950">
    <div class="container">
        <div class="column-07 start-09 typecho-login">
            <h2 class="logo-dark">typecho</h2>
            <form action="<?php $options->index('Login.do'); ?>" method="post">
                <fieldset>
                    <?php if(!$user->hasLogin()): ?>
                    <?php include 'notice.php'; ?>
                    <legend><?php _e('后台登录'); ?></legend>
                    <p><label for="name"><?php _e('用户名'); ?>:</label> <input type="text" name="name" class="text" /></p>
                    <p><label for="password"><?php _e('密码'); ?>:</label> <input type="password" name="password" class="text" /></p>
                    <p class="submit">
                    <label for="remember"><input type="checkbox" name="remember" class="checkbox" id="remember" /> <?php _e('记住我'); ?></label>
                    <button><?php _e('登录'); ?></button>
                    </p>
                    <?php else: ?>
                    <div class="message notice">
                        <ul>
                            <li><?php _e('您已经登录到%s', $options->title); ?></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </fieldset>
            </form>
            <div class="more-link">
                <p class="back-to-site">
                <a href="<?php $options->siteUrl(); ?>" class="important"><?php _e('&laquo; 返回%s', $options->title); ?></a>
                </p>
                <p class="forgot-password">
                <?php if(!$user->hasLogin()): ?>
                <a href="<?php $options->adminUrl('get-password.php'); ?>"><?php _e('忘记密码 &raquo;'); ?></a>
                <?php else: ?>
                <a href="<?php $options->adminUrl(); ?>"><?php _e('进入后台 &raquo;'); ?></a>
                <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
