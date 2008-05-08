<?php if(Typecho::widget('Notice')->have()): ?>
<div class="<?php Typecho::widget('Notice')->noticeType(); ?>">
<ul>
    <?php Typecho::widget('Notice')->lists(); ?>
</ul>
</div>
<?php endif; ?>
