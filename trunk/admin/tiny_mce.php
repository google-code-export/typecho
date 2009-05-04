<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/tiny_mce/tiny_mce.js'); ?>"></script>
<script type="text/javascript">
    (function () {
        Typecho.tinyMCE('text', '<?php $options->index('Ajax.do'); ?>',
        '<?php _e('编辑器'); ?>', '<?php _e('代码'); ?>', '<?php echo ($options->useRichEditor ? 'vw' : 'cw'); ?>');
    })();
    
    var insertImageToEditor = function (title, url, link) {
        if (Typecho.isRichEditor()) {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false,
            '<a href="' + link + '" title="' + title + '"><img src="' + url + '" alt="' + title + '" /></a>');
            new Fx.Scroll(window).toElement($(document).getElement('.mceEditor'));
        } else {
            Typecho.textareaAdd('#text', '<a href="' + link + '" title="' + title + '"><img src="' + url + '" alt="' + title + '" /></a>', '');
            new Fx.Scroll(window).toElement($(document).getElement('textarea#text'));
        }
    };
    
    var insertLinkToEditor = function (title, url, link) {
        if (Typecho.isRichEditor()) {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="' + url + '" title="' + title + '">' + title + '</a>');
            new Fx.Scroll(window).toElement($(document).getElement('.mceEditor'));
        } else {
            Typecho.textareaAdd('#text', '<a href="' + url + '" title="' + title + '">' + title + '</a>', '');
            new Fx.Scroll(window).toElement($(document).getElement('textarea#text'));
        }
    };
</script>
