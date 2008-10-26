<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if($notice->have() && in_array($notice->noticeType, array('success', 'notice', 'error'))): ?>
<div class="message <?php $notice->noticeType(); ?>" ondblclick="this.style.display='none'">
<ul>
    <?php $notice->lists(); ?>
</ul>
</div>
<?php endif; ?>
