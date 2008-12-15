<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01">

                <div class="typecho-post-option column-24">
                    <div class="typecho-post-area column-24">
                        <form action="" method="post">
                            <div class="column-18">
                                <label class="typecho-label"><?php _e('标题'); ?></label>
                                <p><input type="text" value="" class="text title" /></p>
                                <label class="typecho-label"><?php _e('内容'); ?></label>
                                <p><textarea cols="100" rows="15"></textarea></p>
                                <label class="typecho-label"><?php _e('标签'); ?></label>
                                <p><input type="text" value="" class="text" /></p>
                                <p class="submit">
                                    <button><?php _e('保存并继续编辑'); ?></button>
                                    <button><?php _e('发布这篇文章 &raquo;'); ?></button>
                                </p>
                            </div>
                            <div class="column-06">
                                <ul class="typecho-post-option">
                                    <li>
                                        <label class="typecho-label"><?php _e('日期'); ?></label>
                                        <p><input type="text" value="" class="mini" /></p>
                                        <p class="description"><?php _e('请选择一个发布日期'); ?></p>
                                    </li>
                                    <li>
                                        <label class="typecho-label"><?php _e('分类'); ?></label>
                                        <p>
                                            <ul>
                                                <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($category); ?>
                                                <?php while($category->next()): ?>
                                                <li><input type="checkbox" id="category-<?php $category->mid(); ?>" value="<?php $category->mid(); ?>" name="category[]" />
                                                <label for="category-<?php $category->mid(); ?>"><?php $category->name(); ?><label></li>
                                                <?php endwhile; ?>
                                            </ul>
                                        </p>
                                    </li>
                                    <li>
                                        <label class="typecho-label"><?php _e('缩略名'); ?></label>
                                        <p><input type="text" value="" class="mini" /></p>
                                        <p class="description"><?php _e('为这篇日志自定义链接地址, 有利于搜索引擎收录'); ?></p>
                                    </li>
                                </ul>
                            </div>
                        </form>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
