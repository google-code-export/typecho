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
            <form action="<?php $options->index('/action/contents-post-edit'); ?>" method="post" name="write_post">
                <div class="column-18 suffix" id="test">
                    <div class="column-18">
                        <label for="title" class="typecho-label"><?php _e('标题'); ?>
                        <?php if ($post->draft && $post->draft['cid'] != $post->cid): ?>
                        <?php $postModifyDate = new Typecho_Date($post->draft['modified']); ?>
                        <cite><?php _e('当前正在编辑的是保存于%s的草稿, 你可以<a href="%s">删除它</a>', $postModifyDate->word(), 
                        Typecho_Common::url('/action/contents-post-edit?do=deleteDraft&cid=' . $post->cid, $options->index)); ?></cite>
                        <?php endif; ?>
                        </label>
                        <p class="title"><input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post->title); ?>" class="text title" /></p>
                        <label for="text" class="typecho-label"><?php _e('内容'); ?><cite id="auto-save-message"></cite></label>
                        <p><textarea style="height: <?php $options->editorSize(); ?>px" autocomplete="off" id="text" name="text"><?php echo htmlspecialchars($post->text); ?></textarea></p>
                        <label for="tags" class="typecho-label"><?php _e('标签'); ?></label>
                        <p><input id="tags" name="tags" type="text" value="<?php $post->tags(',', false); ?>" class="text" /></p>
                        <?php Typecho_Plugin::factory('admin/write-post.php')->content($post); ?>
                        <p class="submit">
                            <span class="left">
                                <span class="advance close"><?php _e('展开高级选项'); ?></span>
                                <span class="attach"><?php _e('展开附件'); ?></span>
                            </span>
                            <span class="right">
                                <input type="hidden" name="cid" value="<?php $post->cid(); ?>" />
                                <input type="hidden" name="do" value="publish" />
                                <button type="button" id="btn-save"><?php _e('保存草稿'); ?></button>
                                <button type="button" id="btn-submit"><?php _e('发布文章 &raquo;'); ?></button>
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
                    <ul id="upload-panel" class="column-18">
                        <li class="column-18">
                            <?php include 'file-upload.php'; ?>
                        </li>
                    </ul>
                </div>
                <div class="column-06">
                    <ul class="typecho-post-option">
                        <li>
                            <label for="date" class="typecho-label"><?php _e('日期'); ?></label>
                            <p>
                                <select disabled class="typecho-date" name="month" id="month">
                                    <option value="1" <?php if (1 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('一月'); ?></option>
                                    <option value="2" <?php if (2 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('二月'); ?></option>
                                    <option value="3" <?php if (3 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('三月'); ?></option>
                                    <option value="4" <?php if (4 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('四月'); ?></option>
                                    <option value="5" <?php if (5 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('五月'); ?></option>
                                    <option value="6" <?php if (6 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('六月'); ?></option>
                                    <option value="7" <?php if (7 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('七月'); ?></option>
                                    <option value="8" <?php if (8 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('八月'); ?></option>
                                    <option value="9" <?php if (9 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('九月'); ?></option>
                                    <option value="10" <?php if (10 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('十月'); ?></option>
                                    <option value="11" <?php if (11 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('十一月'); ?></option>
                                    <option value="12" <?php if (12 == $post->date->format('n')): ?>selected="true"<?php endif; ?>><?php _e('十二月'); ?></option>
                                </select>
                                <input disabled class="typecho-date" size="4" maxlength="4" type="text" name="day" id="day" value="<?php $post->date('d'); ?>" />
                                ,
                                <input disabled class="typecho-date" size="4" maxlength="4" type="text" name="year" id="year" value="<?php $post->date('Y'); ?>" />
                                @
                                <input disabled class="typecho-date" size="2" maxlength="2" type="text" name="hour" id="hour" value="<?php $post->date('H'); ?>" />
                                :
                                <input disabled class="typecho-date" size="2" maxlength="2" type="text" name="min" id="min" value="<?php $post->date('i'); ?>" />
                            </p>
                            <p class="description"><?php _e('请选择一个发布日期'); ?></p>
                        </li>
                        <li>
                            <label class="typecho-label"><?php _e('分类'); ?></label>
                            <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($category); ?>
                            <ul<?php if ($category->length > 8): ?> style="height: 264px"<?php endif; ?>>
                                <?php
                                if ($post->have()) {
                                    $categories = Typecho_Common::arrayFlatten($post->categories, 'mid');
                                } else {
                                    $categories = array();
                                }
                                ?>
                                <?php while($category->next()): ?>
                                <li><input type="checkbox" id="category-<?php $category->mid(); ?>" value="<?php $category->mid(); ?>" name="category[]" <?php if(in_array($category->mid, $categories)): ?>checked="true"<?php endif; ?>/>
                                <label for="category-<?php $category->mid(); ?>"><?php $category->name(); ?></label></li>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                        <li>
                            <label for="slug" class="typecho-label"><?php _e('缩略名'); ?></label>
                            <p><input type="text" id="slug" name="slug" value="<?php $post->slug(); ?>" class="mini" /></p>
                            <p class="description"><?php _e('为这篇日志自定义链接地址, 有利于搜索引擎收录'); ?></p>
                        </li>
                        <?php Typecho_Plugin::factory('admin/write-post.php')->option($post); ?>
                        <?php if($post->have()): ?>
                        <?php $modified = new Typecho_Date($post->modified); ?>
                        <li>
                            <label class="typecho-label"><?php _e('本文由 <a href="%s">%s</a> 撰写',
                                Typecho_Common::url('manage-posts.php?uid=' . $post->author->uid, $options->adminUrl), $post->author->screenName); ?></label>
                            <p class="description"><?php _e('最后修改于 %s', $modified->word()); ?></p>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>

<?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud', 'sort=count&desc=1&limit=200')->to($tags); ?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            /** 标签自动完成 */
            var _tags = [<?php while ($tags->next()) { echo '"' . str_replace('"', '\"', $tags->name) . '"'
            . ($tags->sequence != $tags->length ? ',' : NULL); } ?>];
            
            /** 自动完成 */
            Typecho.autoComplete('#tags', _tags);
        });
    })();
</script>

<?php
include 'write-js.php';

Typecho_Plugin::factory('admin/write-post.php')->trigger($plugged)->richEditor($post);
if (!$plugged) {
    include 'editor-js.php';
}
Typecho_Plugin::factory('admin/write-post.php')->bottom($post);
include 'file-upload-js.php';
include 'footer.php';
?>
