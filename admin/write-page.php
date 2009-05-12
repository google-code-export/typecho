<?php
include 'common.php';
include 'header.php';
include 'menu.php';
Typecho_Widget::widget('Widget_Contents_Page_Edit')->to($page);
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main typecho-post-option typecho-post-area">
            <form action="<?php $options->index('Contents/Page/Edit.do'); ?>" method="post" name="write_page">
                <div class="column-18 start-01">
                    <div class="column-18">
                        <label for="title" class="typecho-label"><?php _e('标题'); ?></label>
                        <p class="title"><input type="text" id="title" name="title" value="<?php $page->title(); ?>" class="text title" /></p>
                        <label for="text" class="typecho-label"><?php _e('内容'); ?></label>
                        <p><textarea style="height: <?php $options->editorSize(); ?>px" autocomplete="off" id="text" name="text"><?php echo htmlspecialchars($page->content); ?></textarea></p>
                        <?php Typecho_Plugin::factory('admin/write-page.php')->content($page); ?>
                        <p class="submit">
                            <span class="left">
                                <span class="advance close"><?php _e('展开高级选项'); ?></span>
                                <span class="attach"><?php _e('展开附件'); ?></span>
                            </span>
                            <span class="right">
                                <input type="hidden" name="cid" value="<?php $page->cid(); ?>" />
                                <input type="hidden" name="draft" value="0" />
                                <input type="hidden" name="do" value="<?php echo $page->have() ? 'update' : 'insert'; ?>" />
                                <button type="submit" id="btn-save"><?php _e('保存并继续编辑'); ?></button>
                                <button type="submit" id="btn-submit"><?php if(!$page->have() || 'draft' == $page->status): ?><?php _e('发布页面 &raquo;'); ?><?php else: ?><?php _e('更新页面 &raquo;'); ?><?php endif; ?></button>
                            </span>
                        </p>
                    </div>
                        
                    <ul id="advance-panel" class="typecho-post-option column-18">
                        <li class="column-18">
                            <div class="column-12">
                                <label for="order" class="typecho-label"><?php _e('页面顺序'); ?></label>
                                <p><input type="text" id="order" name="order" value="<?php $page->order(); ?>" class="mini" /></p>
                                <p class="description"><?php _e('为你的自定义页面设定一个序列值以后, 能够使得它们按此值从小到大排列'); ?></p>
                                <br />
                                <label for="template" class="typecho-label"><?php _e('自定义模板'); ?></label>
                                <p>
                                    <select name="template" id="template">
                                        <option value=""><?php _e('不选择'); ?></option>
                                        <?php foreach ($page->templates as $template): ?>
                                        <option value="<?php echo $template['value']; ?>"<?php if($template['value'] == $page->template): ?> selected="true"<?php endif; ?>><?php echo $template['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                                <p class="description"><?php _e('如果你为此页面选择了一个自定义模板, 系统将按照你选择的模板文件展现它'); ?></p>
                                <?php Typecho_Plugin::factory('admin/write-page.php')->advanceOptionLeft($page); ?>
                            </div>
                            <div class="column-06">
                                <label class="typecho-label"><?php _e('权限控制'); ?></label>
                                <ul>
                                    <li><input id="allowComment" name="allowComment" type="checkbox" value="1" <?php if($page->allow('comment')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowComment"><?php _e('允许评论'); ?></label></li>
                                    <li><input id="allowPing" name="allowPing" type="checkbox" value="1" <?php if($page->allow('ping')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowPing"><?php _e('允许被引用'); ?></label></li>
                                    <li><input id="allowFeed" name="allowFeed" type="checkbox" value="1" <?php if($page->allow('feed')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowFeed"><?php _e('允许在聚合中出现'); ?></label></li>
                                    <?php Typecho_Plugin::factory('admin/write-page.php')->advanceOptionRight($page); ?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                    <ul id="upload-panel" class="column-18">
                        <li class="column-18">
                            <iframe frameBorder="no" width="100%" src="<?php $options->adminUrl('file-upload.php'); ?>"></iframe>
                        </li>
                    </ul>
                </div>
                <div class="column-06 start-19">
                    <ul class="typecho-post-option">
                        <li>
                            <label for="date" class="typecho-label"><?php _e('日期'); ?></label>
                            <p>
                                <select name="month" id="month">
                                    <option value="1" <?php if (1 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('一月'); ?></option>
                                    <option value="2" <?php if (2 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('二月'); ?></option>
                                    <option value="3" <?php if (3 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('三月'); ?></option>
                                    <option value="4" <?php if (4 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('四月'); ?></option>
                                    <option value="5" <?php if (5 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('五月'); ?></option>
                                    <option value="6" <?php if (6 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('六月'); ?></option>
                                    <option value="7" <?php if (7 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('七月'); ?></option>
                                    <option value="8" <?php if (8 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('八月'); ?></option>
                                    <option value="9" <?php if (9 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('九月'); ?></option>
                                    <option value="10" <?php if (10 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('十月'); ?></option>
                                    <option value="11" <?php if (11 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('十一月'); ?></option>
                                    <option value="12" <?php if (12 == $page->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('十二月'); ?></option>
                                </select>
                                <input size="4" maxlength="4" type="text" name="day" id="day" value="<?php $page->date('d'); ?>" />
                                ,
                                <input size="4" maxlength="4" type="text" name="year" id="year" value="<?php $page->date('Y'); ?>" />
                                @
                                <input size="2" maxlength="2" type="text" name="hour" id="hour" value="<?php $page->date('H'); ?>" />
                                :
                                <input size="2" maxlength="2" type="text" name="min" id="min" value="<?php $page->date('i'); ?>" />
                            </p>
                            <p class="description"><?php _e('请选择一个发布日期'); ?></p>
                        </li>
                        <li>
                            <label for="slug" class="typecho-label"><?php _e('缩略名'); ?></label>
                            <p><input type="text" id="slug" name="slug" value="<?php $page->slug(); ?>" class="mini" /></p>
                            <p class="description"><?php _e('为这篇日志自定义链接地址, 有利于搜索引擎收录'); ?></p>
                        </li>
                        <?php Typecho_Plugin::factory('admin/write-page.php')->option($page); ?>
                        <?php if($page->have()): ?>
                        <li>
                            <label class="typecho-label"><?php _e('相关'); ?></label>
                            <p><?php _e('此页面的创建者是 <strong>%s</strong>', $page->author->screenName); ?></p>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
        
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
                $(document).getElement('input[name=draft]').set('value', 1);
            });
            
            $('btn-submit').addEvent('click', function (e) {
                this.getParent('span').addClass('loading');
                this.setProperty('disabled', true);
                $(document).getElement('input[name=draft]').set('value', 0);
            });
        });
    })();
</script>
<?php
Typecho_Plugin::factory('admin/write-page.php')->trigger($plugged)->richEditor($page);
if (!$plugged) {
    include 'tiny_mce.php';
}
Typecho_Plugin::factory('admin/write-page.php')->bottom($page);
include 'copyright.php';
?>
