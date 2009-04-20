<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.queue.js'); ?>"></script>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var _mediaBtn = $(document).getElement('span.media');
            _mediaBtn.addEvent('click', function () {
                Typecho.toggle('#upload-panel', this,
                '<?php _e('媒体库'); ?>', '<?php _e('媒体库'); ?>');
            });
        
            var fileDialogComplete = function (numFilesSelected, numFilesQueued) {
                try {
                    this.startUpload();
                } catch (ex)  {
                    this.debug(ex);
                }
            };
        
            var uploadStart = function (file) {
                var _el = new Element('div', {
                    'class' : 'typecho-post-attachment-item',
                    'id'    : file.id,
                    'text'  : file.name
                });
                
                if (!Typecho.toggleOpened || _mediaBtn != Typecho.toggleBtn) {
                    Typecho.toggle('#upload-panel', _mediaBtn,
                    '<?php _e('媒体库'); ?>', '<?php _e('媒体库'); ?>');
                }
                
                $(document).getElement('ul#upload-panel li').grab(_el);
            };
            
            var uploadSuccess = function (file, serverData) {
                console.log(serverData);
            };
            
            var uploadComplete = function (file) {
                console.dir(file);
            };
            
            var uploadError = function (file, errorCode, message) {
                console.log(message);
            };
            
            var uploadProgress = function (file, bytesLoaded, bytesTotal) {
                
            };
        
            var swfu, _size = $(document).getElement('.attach').getCoordinates(),
            settings = {
                flash_url : "<?php $options->adminUrl('javascript/swfupload/swfupload.swf'); ?>",
                upload_url: "<?php $options->index('Upload.do'); ?>",
                post_params: {"__typecho_uid" : "<?php echo Typecho_Request::getCookie('__typecho_uid'); ?>", 
                "__typecho_authCode" : "<?php echo Typecho_Request::getCookie('__typecho_authCode'); ?>"},
                file_size_limit : "100 MB",
                file_types : "*.*",
                file_types_description : "<?php _e('所有文件'); ?>",
                file_upload_limit : 100,
                file_queue_limit : 0,
                custom_settings : {
                    progressTarget : "fsUploadProgress"
                },
                debug: false,
                
                //Handle Settings
                file_dialog_complete_handler : fileDialogComplete,
                upload_start_handler : uploadStart,
                upload_progress_handler : uploadProgress,
                upload_success_handler : uploadSuccess,
                upload_complete_handler : uploadComplete,
                upload_error_handler : uploadError,
                
                // Button Settings
                button_placeholder_id : "swfu-placeholder",
                button_height: 20,
                button_text: '',
                button_text_style: '',
                button_text_left_padding: 14,
                button_text_top_padding: 0,
                button_width: _size.width,
                button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
                button_cursor: SWFUpload.CURSOR.HAND
			};

            $(document).getElement('#swfu').setStyles({'margin-left': - _size.width});
			swfu = new SWFUpload(settings);
        });
    })();
</script>
