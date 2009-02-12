<?php
include 'common.php';
include 'header.php';
include 'menu.php';
Typecho_Widget::widget('Widget_Contents_Post_Edit')->to($post);
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main typecho-post-option typecho-post-area">
            <form action="<?php $options->index('Contents/Post/Edit.do'); ?>" method="post" name="write_post">
                <div class="column-18 start-01">
                    <div class="column-18">
                        <label for="title" class="typecho-label"><?php _e('标题'); ?></label>
                        <p><input type="text" id="title" name="title" value="<?php $post->title(); ?>" class="text title" /></p>
                        <label for="text" class="typecho-label"><?php _e('内容'); ?></label>
                        <p><textarea id="text" name="text"><?php echo htmlspecialchars($post->content); ?></textarea></p>
                        <label for="tags" class="typecho-label"><?php _e('标签'); ?></label>
                        <p><input id="tags" name="tags" type="text" value="<?php $post->tags(',', false); ?>" class="text" /></p>
                        <?php Typecho_Plugin::factory('admin/write-post.php')->content($post); ?>
                        <p class="submit">
                            <span class="left">
                                <span class="advance close"><?php _e('展开高级选项'); ?></span>
                            </span>
                            <span class="right">
                                <input type="hidden" name="cid" value="<?php $post->cid(); ?>" />
                                <input type="hidden" name="draft" value="0" />
                                <input type="hidden" name="do" value="<?php echo $post->have() ? 'update' : 'insert'; ?>" />
                                <button type="submit" id="btn-save"><?php _e('保存并继续编辑'); ?></button>
                                <button type="submit" id="btn-submit"><?php if(!$post->have() || 'draft' == $post->status): ?><?php _e('发布这篇文章 &raquo;'); ?><?php else: ?><?php _e('更新这篇文章 &raquo;'); ?><?php endif; ?></button>
                            </span>
                        </p>
                    </div>
                    <ul id="advance-panel" class="typecho-post-option column-18">
                        <li class="column-18">
                            <div class="column-12">
                                    <label for="password" class="typecho-label"><?php _e('密码'); ?></label>
                                    <p><input type="text" id="password" name="password" value="<?php $post->password(); ?>" class="mini" /></p>
                                    <p class="description"><?php _e('为这篇日志分配一个密码, 访问者需要输入密码才能阅读到日志的内容'); ?></p>
                                    <br />
                                    <label for="trackback" class="typecho-label"><?php _e('引用通告'); ?></label>
                                    <textarea id="trackback" name="trackback"></textarea>
                                    <p class="description"><?php _e('每一行一个引用地址, 用回车隔开'); ?></p>
                                    <?php Typecho_Plugin::factory('admin/write-post.php')->advanceOptionLeft($post); ?>
                            </div>
                            <div class="column-06">
                                <label class="typecho-label"><?php _e('权限控制'); ?></label>
                                <ul>
                                    <li><input id="allowComment" name="allowComment" type="checkbox" value="1" <?php if($post->allow('comment')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowComment"><?php _e('允许评论'); ?></label></li>
                                    <li><input id="allowPing" name="allowPing" type="checkbox" value="1" <?php if($post->allow('ping')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowPing"><?php _e('允许被引用'); ?></label></li>
                                    <li><input id="allowFeed" name="allowFeed" type="checkbox" value="1" <?php if($post->allow('feed')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowFeed"><?php _e('允许在聚合中出现'); ?></label></li>
                                    <?php Typecho_Plugin::factory('admin/write-post.php')->advanceOptionRight($post); ?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="column-06 start-19">
                    <ul class="typecho-post-option">
                        <li>
                            <label for="date" class="typecho-label"><?php _e('日期'); ?></label>
                            <p>
                            <input type="text" readonly class="date" name="date" id="date" value="<?php $post->date('Y/m/d'); ?>" />
                             <strong>@</strong> 
                            <input type="text" class="time" name="hour" id="hour" value="<?php $post->date('H'); ?>" />
                             <strong>:</strong> 
                            <input type="text" class="time" name="min" id="min" value="<?php $post->date('i'); ?>" />
                            </p>
                            <p class="description"><?php _e('请选择一个发布日期'); ?></p>
                        </li>
                        <li>
                            <label class="typecho-label"><?php _e('分类'); ?></label>
                            <p>
                                <ul>
                                    <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($category); ?>
                                    <?php
                                    if ($post->have()) {
                                        $categories = Typecho_Common::arrayFlatten($post->categories, 'mid');
                                    } else {
                                        $categories = array($options->defaultCategory);
                                    }
                                    ?>
                                    <?php while($category->next()): ?>
                                    <li><input type="checkbox" id="category-<?php $category->mid(); ?>" value="<?php $category->mid(); ?>" name="category[]" <?php if(in_array($category->mid, $categories)): ?>checked="true"<?php endif; ?>/>
                                    <label for="category-<?php $category->mid(); ?>"><?php $category->name(); ?></label></li>
                                    <?php endwhile; ?>
                                </ul>
                            </p>
                        </li>
                        <li>
                            <label for="slug" class="typecho-label"><?php _e('缩略名'); ?></label>
                            <p><input type="text" id="slug" name="slug" value="<?php $post->slug(); ?>" class="mini" /></p>
                            <p class="description"><?php _e('为这篇日志自定义链接地址, 有利于搜索引擎收录'); ?></p>
                        </li>
                        <?php Typecho_Plugin::factory('admin/write-post.php')->option($post); ?>
                        <?php if($post->have()): ?>
                        <li>
                            <label class="typecho-label"><?php _e('相关'); ?></label>
                            <p><?php _e('此文的作者是 <strong>%s</strong>', $post->author->screenName); ?></p>
                            <?php Typecho_Widget::widget('Widget_Contents_Related_Author', 
                            "limit=3&cid={$post->cid}&author={$post->author->uid}&type={$post->type}")->to($related); ?>
                            <?php if($related->have()): ?>
                            <ul class="related">
                                <?php while($related->next()): ?>
                                <li><a href="<?php $options->adminUrl('write-post.php?cid=' . $related->cid); ?>"><?php $related->title(); ?></a></li>
                                <?php endwhile; ?>
                            </ul>
                            <?php endif; ?>
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
<?php include 'common-js.php'; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/tiny_mce/tiny_mce.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/tiny_mce/langs.php'); ?>"></script>
<script type="text/javascript">
    (function () {
        /** 绑定按钮 */
        $(document).getElement('span.advance').addEvent('click', function () {
            Typecho.toggle('#advance-panel', this,
            '<?php _e('收起高级选项'); ?>', '<?php _e('展开高级选项'); ?>');
        });
        
        $('btn-save').addEvent('click', function () {
            $(document).getElement('input[name=draft]').set('value', 1);
        });
        
        $('btn-submit').addEvent('click', function () {
            $(document).getElement('input[name=draft]').set('value', 0);
        });
        
        /** 初始化日历 */
        window.addEvent('domready', function() {
            Calendar.setup(
                {
                    inputField : "date",
                    ifFormat : "%Y-%m-%d %H:%M",
                    showsTime: true,
                    button : "date"
                }
            );
        });

        Typecho.tinyMCE('text');
    })();
</script>
<?php
Typecho_Plugin::factory('admin/write-post.php')->bottom($post);
include 'copyright.php';
?>
