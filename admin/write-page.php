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
                        <label for="title" class="typecho-label"><?php _e('标题'); ?></label>
                        <p><input type="text" id="title" name="title" value="<?php $page->title(); ?>" class="text title" /></p>
                        <label for="text" class="typecho-label"><?php _e('内容'); ?></label>
                        <p><textarea id="text" name="text"><?php $page->content(); ?></textarea></p>
                        <p class="submit">
                            <span class="right">
                                <input type="hidden" name="cid" value="<?php $page->cid(); ?>" />
                                <input type="hidden" name="draft" value="0" />
                                <input type="hidden" name="do" value="<?php echo $page->have() ? 'update' : 'insert'; ?>" />
                                <button type="submit" onclick="typechoSubmit('form[name=write_page]', 'input[name=draft]', '1');"><?php _e('保存并继续编辑'); ?></button>
                                <button type="submit" onclick="typechoSubmit('form[name=write_page]', 'input[name=draft]', '0');"><?php if(!$page->have() || 'draft' == $page->status): ?><?php _e('发布页面 &raquo;'); ?><?php else: ?><?php _e('更新页面 &raquo;'); ?><?php endif; ?></button>
                            </span>
                        </p>
                </div>
                <div class="column-06 start-19">
                    <ul class="typecho-post-option">
                        <li>
                            <label for="date" class="typecho-label"><?php _e('日期'); ?></label>
                            <p><input type="text" readonly="readonly" class="date" name="date" id="date" value="<?php $page->date('Y-m-d H:i'); ?>" class="mini" /></p>
                            <p class="description"><?php _e('请选择一个发布日期'); ?></p>
                        </li>
                        <li>
                            <label for="slug" class="typecho-label"><?php _e('缩略名'); ?></label>
                            <p><input type="text" id="slug" name="slug" value="<?php $page->slug(); ?>" class="mini" /></p>
                            <p class="description"><?php _e('为这篇日志自定义链接地址, 有利于搜索引擎收录'); ?></p>
                        </li>
                        <li>
                            <label for="slug" class="typecho-label"><?php _e('页面顺序'); ?></label>
                            <p><input type="text" id="meta" name="meta" value="<?php $page->meta(); ?>" class="mini" /></p>
                        </li>
                        <li>
                            <label class="typecho-label"><?php _e('权限控制'); ?></label>
                            <ul>
                                <li><input id="allowComment" name="allowComment" type="checkbox" value="1" <?php if($page->allow('comment')): ?>checked="true"<?php endif; ?> />
                                <label for="allowComment"><?php _e('允许评论'); ?></label></li>
                                <li><input id="allowPing" name="allowPing" type="checkbox" value="1" <?php if($page->allow('ping')): ?>checked="true"<?php endif; ?> />
                                <label for="allowPing"><?php _e('允许被引用'); ?></label></li>
                                <li><input id="allowFeed" name="allowFeed" type="checkbox" value="1" <?php if($page->allow('feed')): ?>checked="true"<?php endif; ?> />
                                <label for="allowFeed"><?php _e('允许在聚合中出现'); ?></label></li>
                            </ul>
                        </li>
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
<style type="text/css">@import url(<?php $options->adminUrl('javascript/jscalendar-1.0/calendar-win2k-1.css'); ?>);</style>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/jscalendar-1.0/calendar_stripped.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/jscalendar-1.0/lang.php'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/jscalendar-1.0/calendar-setup_stripped.js'); ?>"></script>
<script type="text/javascript">
    Calendar.setup(
        {
            inputField : "date",
            ifFormat : "%Y-%m-%d %H:%M",
            showsTime: true,
            button : "date"
        }
    );
</script>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
