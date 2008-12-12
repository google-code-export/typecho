<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/mootools-1.2.1-core-yc.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/mootools-1.2.1-more.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/typecho-ui.js'); ?>"></script>
<script type="text/javascript"> 
    (function () {
        window.addEvent('domready', function() {
            var handle = new typechoGuid('typecho:guid', {offset: 1, type: 'mouse'});
            var firstError = $(document).getElement('.typecho-option .error');
            
            //增加滚动效果
            if (firstError) {
                var errorFx = new Fx.Scroll(window).toElement(firstError.getParent('.typecho-option'));
            }
            
            //增加淡出效果
            setTimeout(typechoMessage, 5000);
            typechoTableListener('.typecho-list-table');
            typechoTableListener('.typecho-list-notable');
            handle.reSet();
        });
    })();
</script>
