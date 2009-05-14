<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php
if (isset($post) || isset($page)) {
    $cid = isset($post) ? $post->cid : $page->cid;
    Typecho_Widget::widget('Widget_Contents_Attachment_Related', 'cid=' . $cid)->to($attachment);
}
?>

<style>
.upload-progress {
    font-size: 12px;
}

#upload-panel ul li.upload-progress-item {
	background-image: url(<?php $options->adminUrl('images/progress.gif'); ?>);
	background-repeat: repeat-y;
	background-position: -1000px 0;
    background-color: #fff;
    padding: 5px;
    margin-bottom: 5px;
    border: 1px solid #C1CD94;
    
	-moz-border-radius-topleft: 2px;
	-moz-border-radius-topright: 2px;
	-moz-border-radius-bottomleft: 2px;
	-moz-border-radius-bottomright: 2px;
	-webkit-border-top-left-radius: 2px;
	-webkit-border-top-right-radius: 2px;
	-webkit-border-bottom-left-radius: 2px;
	-webkit-border-bottom-right-radius: 2px;
	
	/* hope IE support border radius, God save me! */
	border-top-left-radius: 2px;
	border-top-right-radius: 2px;
	border-bottom-left-radius: 2px;
	border-bottom-right-radius: 2px;
}

.upload-progress-item strong {
    float: left;
}

.upload-progress-item small {
    float: right;
    font-size: 8pt;
}

.upload-progress-item small .insert, .upload-progress-item small .delete {
    cursor: pointer;
    text-decoration: underline;
}

.upload-progress-item small .insert {
    color: #00AA00;
}

.upload-progress-item small .delete {
    color: #CC0000;
}
</style>

<div class="typecho-list-operate">
<p class="search left">
    <a class="button left"><?php _e('上传文件'); ?> <small style="font-weight:normal">(<?php echo ini_get('upload_max_filesize'); ?>)</small></a>
    <span id="swfu"><span id="swfu-placeholder"></span></span>
</p>
</div>

<ul class="upload-progress">
<?php while ($attachment->next()): ?>
    <li class="upload-progress-item clearfix">
        <strong>
            <?php $attachment->title(); ?>
            <input type="hidden" name="attachment[]" value="<?php $attachment->cid(); ?>" />
        </strong>
        <small>
            <span class="insert" onclick="<?php if ($attachment->attachment->isImage){
                        echo "insertImageToEditor('{$attachment->title}', '{$attachment->attachment->url}', '{$attachment->permalink}');";
                    } else {
                        echo "insertLinkToEditor('{$attachment->title}', '{$attachment->attachment->url}', '{$attachment->permalink}');";
                    } ?>"><?php _e('插入'); ?></span>
            ,
            <span class="delete" onclick="deleteAttachment(<?php $attachment->cid(); ?>, this);"><?php _e('删除'); ?></span>
        </small>
    </li>
<?php endwhile; ?>
</ul>

<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.queue.js'); ?>"></script>
<script type="text/javascript">
    var deleteAttachment = function (cid, el) {
        new Request.JSON({
            method : 'post',
            url : '<?php $options->index('Contents/Attachment/Edit.do'); ?>',
            onComplete : function (result) {
                if (200 == result.code) {
                    $(el).getParent('li').destroy();
                } 
            }
        }).send('do=delete&cid=' + cid);
    };

    (function () {

        window.addEvent('domready', function() {
            var _inited = false;
            
            //begin parent tabshow
            $(document).getElement('#upload-panel').addEvent('tabShow', function () {
            
                if (_inited) {
                    return;
                }
                _inited = true;
            
                var fileDialogComplete = function (numFilesSelected, numFilesQueued) {
                    try {
                        this.startUpload();
                    } catch (ex)  {
                        this.debug(ex);
                    }
                };
            
                var uploadStart = function (file) {
                    var _el = new Element('li', {
                        'class' : 'upload-progress-item clearfix',
                        'id'    : file.id,
                        'text'  : file.name
                    });
                    
                    _el.inject($(document).getElement('ul.upload-progress'), 'top');
                };
                
                var uploadSuccess = function (file, serverData) {
                    var _el = $(document).getElement('#' + file.id);
                    var _result = JSON.decode(serverData);
                    
                    _el.set('html', '<strong>' + file.name + 
                    '<input type="hidden" name="attachment[]" value="' + _result.cid + '" /></strong>' + 
                    '<small><span class="insert"><?php _e('插入'); ?></span>' +
                    ', <span class="delete"><?php _e('删除'); ?></span></small>');
                    _el.set('tween', {duration: 1500});
                    
                    _el.setStyles({
                        'background-image' : 'none',
                        'background-color' : '#D3DBB3'
                    });
                    
                    _el.tween('background-color', '#D3DBB3', '#FFFFFF');
                    
                    var _insertBtn = _el.getElement('.insert');
                    if (_result.isImage) {
                        _insertBtn.addEvent('click', function () {
                            insertImageToEditor(_result.title, _result.url, _result.permalink);
                        });
                    } else {
                        _insertBtn.addEvent('click', function () {
                            insertLinkToEditor(_result.title, _result.url, _result.permalink);
                        });
                    }
                    
                    var _deleteBtn = _el.getElement('.delete');
                    _deleteBtn.addEvent('click', function () {
                        deleteAttachment(_result.cid, this);
                    });
                };
                
                var uploadComplete = function (file) {
                    //console.dir(file);
                };
                
                var uploadError = function (file, errorCode, message) {
                    var _el = $(document).getElement('#' + file.id);
                    var _fx = new Fx.Tween(_el, {duration: 3000});
                    
                    _el.set('html', '<strong>' + file.name + ' <?php _e('上传失败'); ?></strong>');
                    _el.setStyles({
                        'background-image' : 'none',
                        'color'            : '#FFFFFF',
                        'background-color' : '#CC0000'
                    });
                    
                    _fx.addEvent('complete', function () {
                        _el.destory();
                    });
                    
                    _fx.start('background-color', '#CC0000', '#F7FBE9');
                };
                
                var uploadProgress = function (file, bytesLoaded, bytesTotal) {
                    var _el = $(document).getElement('#' + file.id);
                    var percent = Math.ceil((1 - (bytesLoaded / bytesTotal)) * _el.getSize().x);
                    _el.setStyle('background-position', '-' + percent + 'px 0');
                };
            
                var swfu, _size = $(document).getElement('.typecho-list-operate a.button').getCoordinates(),
                settings = {
                    flash_url : "<?php $options->adminUrl('javascript/swfupload/swfupload.swf'); ?>",
                    upload_url: "<?php $options->index('Upload.do'); ?>",
                    post_params: {"__typecho_uid" : "<?php echo Typecho_Request::getCookie('__typecho_uid'); ?>", 
                    "__typecho_authCode" : "<?php echo str_replace(array('"', "\\"), array('\"', "\\\\"), Typecho_Request::getCookie('__typecho_authCode')); ?>"},
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
                    queue_complete_handler : uploadComplete,
                    upload_error_handler : uploadError,
                    
                    // Button Settings
                    button_placeholder_id : "swfu-placeholder",
                    button_height: 25,
                    button_text: '',
                    button_text_style: '',
                    button_text_left_padding: 14,
                    button_text_top_padding: 0,
                    button_width: _size.width,
                    button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
                    button_cursor: SWFUpload.CURSOR.HAND
                };

                swfu = new SWFUpload(settings);
                
            });
            //end parent tabshow
        });
    })();
</script>
