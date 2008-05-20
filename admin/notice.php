<?php if(Typecho::widget('Notice')->have() && in_array(Typecho::widget('Notice')->noticeType, array('success', 'notice', 'error'))): ?>
<div class="<?php Typecho::widget('Notice')->noticeType(); ?>" ondblclick="this.style.display='none'">
<ul>
    <?php Typecho::widget('Notice')->lists(); ?>
</ul>
</div>
<?php endif; ?>
