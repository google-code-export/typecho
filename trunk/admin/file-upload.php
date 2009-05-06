<?php
include 'common.php';

$bodyClass = 'bright no-overflow';
include 'header.php';
?>

<?php Typecho_Widget::widget('Widget_Contents_Attachment_Admin', 'pageSize=10')->to($attachment); ?>

<style>
.upload-progress {
    font-size: 12px;
}

html {
    overflow-y: hidden;
}

.upload-progress-item {
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

.message {
    margin: 0 0 5px 0;
    width: 300px;
    padding: 2px 5px;
    float: right;
    text-align: center;
    font-size: 13px;
    line-height: 20px;
}
</style>

<div id="main-box">
<div class="typecho-list-operate">
<p class="search left">
    <a class="button left"><?php _e('上传文件'); ?> <small style="font-weight:normal">(<?php echo ini_get('upload_max_filesize'); ?>)</small></a>
    <span id="swfu"><span id="swfu-placeholder"></span></span>
</p>
<?php if($notice->have() && in_array($notice->noticeType, array('success', 'notice', 'error'))): ?>
<div class="right message <?php $notice->noticeType(); ?> popup typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
<ul>
    <?php $notice->lists(); ?>
</ul>
</div>
<?php endif; ?>
</div>

<ul class="upload-progress">
</ul>

<table class="typecho-list-table typecho-list-table-border" cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="40"/>
        <col width="510"/>
        <col width="100"/>
    </colgroup>
    <tbody>
        <?php if ($attachment->have()): ?>
        <?php while ($attachment->next()): ?>
        <tr class="<?php $attachment->alt(' even', ''); ?>">
            <td>
                <div class="thumb-box">
                <?php if ($attachment->attachment->isImage): ?>
                <img height="45" src="<?php $attachment->attachment->url(); ?>" alt="<?php $attachment->title(); ?>" />
                <?php else: ?>
                <?php endif; ?>
                </div>
            </td>
            <td><?php $attachment->title(); ?></td>
            <td>
                <div class="right">
                    <a class="hidden-by-mouse button" href="#" onclick="<?php if ($attachment->attachment->isImage){
                        echo "parent.insertImageToEditor('{$attachment->title}', '{$attachment->attachment->url}', '{$attachment->permalink}');";
                    } else {
                        echo "parent.insertLinkToEditor('{$attachment->title}', '{$attachment->attachment->url}', '{$attachment->permalink}');";
                    } ?>">插入</a>
                    <a lang="<?php _e('你确认要删除附件 %s 吗?', $attachment->title); ?>" class="hidden-by-mouse button operate-button-delete" href="<?php $options->index('Contents/Attachment/Edit.do?do=delete&cid=' . $attachment->cid); ?>">删除</a>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr class="even">
            <td colspan="3">
                <?php _e('没有附件, 点击上传按钮添加'); ?>
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if($attachment->have()): ?>
<div class="typecho-pager">
    <div class="typecho-pager-content">
        <h5><?php _e('页面'); ?>:&nbsp;</h5>
        <ul>
            <?php $attachment->pageNav(); ?>
        </ul>
    </div>
</div>
<?php endif; ?>
</div>

<?php include 'common-js.php'; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/swfupload/swfupload.queue.js'); ?>"></script>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var _inited = false;
        
            <?php if (0 !== strpos(Typecho_Request::getReferer(), Typecho_Common::url('file-upload.php', $options->adminUrl))): ?>
            //begin parent tabshow
            parent.$(parent.document).getElement('#upload-panel').addEvent('tabShow', function () {
            <?php endif; ?>
                if (_inited) {
                    return;
                }
                _inited = true;
            
                var refreshIframeHeight = function () {
                    parent.$(parent.document).getElement('#upload-panel iframe').setStyle('height', 
                    $(document).getElement('#main-box').getScrollSize().y + <?php echo $attachment->have() ? 10 : 20; ?>);
                };
                
                refreshIframeHeight();
            
                var fileDialogComplete = function (numFilesSelected, numFilesQueued) {
                    try {
                        this.startUpload();
                    } catch (ex)  {
                        this.debug(ex);
                    }
                };
            
                var uploadStart = function (file) {
                    var _el = new Element('div', {
                        'class' : 'upload-progress-item',
                        'id'    : file.id,
                        'text'  : file.name
                    });
                    
                    _el.inject($(document).getElement('ul.upload-progress'), 'top');
                    refreshIframeHeight();
                };
                
                var uploadSuccess = function (file, serverData) {
                    var _el = $(document).getElement('#' + file.id);
                    var _result = JSON.decode(serverData);
                    
                    _el.set('html', '<strong>' + file.name + '</strong> <small>' + Math.ceil(_result.size/1024) + 'KB</small>');
                    _el.set('tween', {duration: 1500});
                    
                    _el.setStyles({
                        'background-image' : 'none',
                        'background-color' : '#D3DBB3'
                    });
                    
                    _el.tween('background-color', '#D3DBB3', '#FFFFFF');
                    refreshIframeHeight();
                };
                
                var uploadComplete = function (file) {
                    //console.dir(file);
                    Typecho.location('<?php $options->adminUrl('file-upload.php?page=1'); ?>');
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
                    refreshIframeHeight();
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
                    "__typecho_authCode" : "<?php echo str_replace('"', '\"', Typecho_Request::getCookie('__typecho_authCode')); ?>"},
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
                
            <?php if (0 !== strpos(Typecho_Request::getReferer(), Typecho_Common::url('file-upload.php', $options->adminUrl))): ?>
            });
            //end parent tabshow
            <?php endif; ?>
        });
    })();
</script>
</body>
</html>
