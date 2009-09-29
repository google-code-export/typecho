<?php
include 'common.php';
include 'header.php';
include 'menu.php';

Typecho_Widget::widget('Widget_Contents_Attachment_Edit')->to($attachment);
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-16 suffix">
                <div class="typecho-attachment-photo-box">
                    <?php if ($attachment->attachment->isImage): ?>
                    <img src="<?php $attachment->attachment->url(); ?>" alt="<?php $attachment->attachment->name(); ?>" />
                    <?php endif; ?>
                    
                    <div class="description">
                        <ul>
                            <?php $mime = Typecho_Common::mimeIconType($attachment->attachment->mime); ?>
                            <li><span class="typecho-mime typecho-mime-<?php echo $mime; ?>"></span><strong><?php $attachment->attachment->name(); ?></strong> <small><?php echo number_format(ceil($attachment->attachment->size / 1024)); ?> Kb</small></li>
                            <li><input type="text" readonly class="text" value="<?php $attachment->attachment->url(); ?>" /></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="column-08 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                <?php $attachment->form()->render(); ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            
            $(document).getElement('.typecho-attachment-photo-box .description input').addEvent('click', function () {
                this.select();
            });
        
        });
    })();
</script>
<?php
include 'footer.php';
?>
