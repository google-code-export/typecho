<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
        
            $(document).getElements('.typecho-date').each(function (item) {
                item.setProperty('name', '_' + item.getProperty('name'));
                item.addEvent('change', function () {

                    $(document).getElements('.typecho-date').each(function (_item) {
                        var name = _item.name;

                        if (0 == name.indexOf('_')) {
                            _item.setProperty('name', name.slice(1));
                        }
                    });
                    
                });
                
                item.removeProperty('disabled');
            });
        
            /** 绑定按钮 */
            $(document).getElement('span.advance').addEvent('click', function () {
                Typecho.toggle('#advance-panel', this,
                '<?php _e('收起高级选项'); ?>', '<?php _e('展开高级选项'); ?>');
            });
            
            $(document).getElement('span.attach').addEvent('click', function () {
                Typecho.toggle('#upload-panel', this,
                '<?php _e('收起附件'); ?>', '<?php _e('展开附件'); ?>');
            });
            
            $('btn-save').removeProperty('disabled');
            $('btn-submit').removeProperty('disabled');
            
            $('btn-save').addEvent('click', function (e) {
                this.getParent('span').addClass('loading');
                this.setProperty('disabled', true);
                $(document).getElement('input[name=do]').set('value', 'save');
                $(document).getElement('.typecho-post-area form').submit();
            });
            
            $('btn-submit').addEvent('click', function (e) {
                this.getParent('span').addClass('loading');
                this.setProperty('disabled', true);
                $(document).getElement('input[name=do]').set('value', 'publish');
                $(document).getElement('.typecho-post-area form').submit();
            });
        });
    })();
</script>
