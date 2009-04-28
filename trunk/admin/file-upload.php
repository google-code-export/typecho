<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.queue.js'); ?>"></script>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var _imgExt = ['jpg', 'gif', 'png', 'bmp', 'tiff'];
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
                
                _el.inject($(document).getElement('ul#upload-panel li'), 'top');
            };
            
            var uploadSuccess = function (file, serverData) {
                var _el = $(document).getElement('#' + file.id);
                var _result = JSON.decode(serverData);
                
                _el.set('text', serverData);
                _el.set('tween', {duration: 1500});
                
                _el.setStyles({
                    'background-image' : 'none',
                    'background-color' : '#D3DBB3'
                });
                
                _el.tween('background-color', '#D3DBB3', '#F7FBE9');
            };
            
            var uploadComplete = function (file) {
                console.dir(file);
            };
            
            var uploadError = function (file, errorCode, message) {
                console.log(message);
            };
            
            var uploadProgress = function (file, bytesLoaded, bytesTotal) {
                var _el = $(document).getElement('#' + file.id);
                var percent = Math.ceil((1 - (bytesLoaded / bytesTotal)) * _el.getSize().x);
                _el.setStyle('background-position', '-' + percent + 'px 0');
            };
        
            var swfu, _size = $(document).getElement('.attach').getCoordinates(),
            settings = {
                flash_url : "<?php $options->adminUrl('javascript/swfupload/swfupload.swf'); ?>",
                upload_url: "<?php $options->index('Upload.do'); ?>",
                post_params: {"__typecho_uid" : "<?php echo Typecho_Request::getCookie('__typecho_uid'); ?>", 
                "__typecho_authCode" : "<?php echo Typecho_Request::getCookie('__typecho_authCode'); ?>"},
                file_size_limit : "<?php $val = trim(ini_get('upload_max_filesize'));
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    echo $val;
                ?> byte",
                file_types : "<?php echo $options->attachmentTypes(); ?>",
                file_types_description : "<?php _e('所有文件'); ?>",
                file_upload_limit : 100,
                file_queue_limit : 0,
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
