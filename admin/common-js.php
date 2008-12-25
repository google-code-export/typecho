<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/mootools-1.2.1-core-yc.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/mootools-1.2.1-more.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/typecho-ui.js'); ?>"></script>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var handle = new typechoGuid('typecho:guid', {offset: 1, type: 'mouse'});
            
            //增加淡出效果
            setTimeout(typechoMessage, 5000);
            typechoScroll('.typecho-option .error', '.typecho-option');
            typechoAutoDisableSubmit();
            typechoOpenLink(/^<?php echo preg_quote($options->adminUrl, '/'); ?>.*$/,
            /^<?php echo substr(preg_quote(Typecho_Common::url('s', $options->index), '/'), 0, -1); ?>[_a-zA-Z0-9\/]+\.(do|plugin).*$/);
            typechoTableListener('.typecho-list-table');
            typechoTableListener('.typecho-list-notable');
            typechoOperate('.typecho-list-table', 'selectNone');
            typechoOperate('.typecho-list-notable', 'selectNone');
            handle.reSet();
        });
    })();
</script>
