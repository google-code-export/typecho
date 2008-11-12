<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div class="typecho-head-guid">
    <dl id="typecho:guid">
        <?php $menu->output(); ?>
    </dl>
    <p class="operate"><?php _e('欢迎'); ?>, <a href="#" class="author important"><?php $user->screenName(); ?></a>
            <a class="exit" href="<?php $options->index('Logout.do'); ?>" title="<?php _e('登出'); ?>"><?php _e('登出'); ?></a></p>
</div>
