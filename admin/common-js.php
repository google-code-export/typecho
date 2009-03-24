<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var handle = new Typecho.guid('typecho:guid', {offset: 1, type: 'mouse'});
            
            //增加高亮效果
            Typecho.highlight('<?php echo $notice->highlight; ?>');
            
            //增加淡出效果
            Typecho.message('.popup');
            Typecho.scroll('.typecho-option .error', '.typecho-option');
            Typecho.autoDisableSubmit();
            Typecho.openLink(/^<?php echo preg_quote($options->adminUrl, '/'); ?>.*$/,
            /^<?php echo substr(preg_quote(Typecho_Common::url('s', $options->index), '/'), 0, -1); ?>[_a-zA-Z0-9\/]+\.(do|plugin).*$/);
            Typecho.Table.init('.typecho-list-table');
            Typecho.Table.init('.typecho-list-notable');
            handle.reSet();
        });
    })();
</script>
