<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                        <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                        <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                        <?php _e('选中项'); ?>: 
                        <span rel="delete" lang="<?php _e('你确认要删除这些文章吗?'); ?>" class="operate-button operate-delete typecho-table-select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <?php if ('' != $request->keywords || '' != $request->category): ?>
                    <a href="<?php $options->adminUrl('manage-posts.php' . (isset($request->uid) ? '?uid=' . htmlspecialchars($request->get('uid')) : '')); ?>"><?php _e('&laquo; 取消筛选'); ?></a>
                    <?php endif; ?>
                    <input type="text" value="<?php '' != $request->keywords ? print(htmlspecialchars($request->keywords)) : _e('请输入关键字'); ?>"<?php if ('' == $request->keywords): ?> onclick="value='';name='keywords';" <?php else: ?> name="keywords"<?php endif; ?>/>
                    <select name="category">
                    	<option value=""><?php _e('所有分类'); ?></option>
                    	<?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($category); ?>
                    	<?php while($category->next()): ?>
                    	<option value="<?php $category->mid(); ?>"<?php if($request->get('category') == $category->mid): ?> selected="true"<?php endif; ?>><?php $category->name(); ?></option>
                    	<?php endwhile; ?>
                    </select>
                    <button type="submit"><?php _e('筛选'); ?></button>
                    <?php if(isset($request->uid)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($request->get('uid')); ?>" name="uid" />
                    <?php endif; ?>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_posts" class="operate-form" action="<?php $options->index('/action/contents-post-edit'); ?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="260"/>
                        <col width="60"/>
                        <col width="30"/>
                        <col width="110"/>
                        <col width="205"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th><?php _e('标题'); ?></th>
                            <th> </th>
                            <th> </th>
                            <th><?php _e('作者'); ?></th>
                            <th><?php _e('分类'); ?></th>
                            <th class="typecho-radius-topright"><?php _e('日期'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Contents_Post_Admin')->to($posts); ?>
                    	<?php if($posts->have()): ?>
                        <?php while($posts->next()): ?>
                        <tr<?php $posts->alt(' class="even"', ''); ?> id="<?php $posts->theId(); ?>">
                            <td><input type="checkbox" value="<?php $posts->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('manage-comments.php?cid=' . $posts->cid); ?>" class="balloon-button right size-<?php echo Typecho_Common::splitByCount($posts->commentsNum, 1, 10, 20, 50, 100); ?>"><?php $posts->commentsNum(); ?></a></td>
                            <td<?php if ('draft' != $posts->status && 'waiting' != $posts->status): ?> colspan="2"<?php endif; ?>>
                            <a href="<?php $options->adminUrl('write-post.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a>
                            <?php if ('draft' == $posts->status || 'waiting' == $posts->status): ?>
                            </td>
                            <td>
                            <span class="balloon right"><?php 'draft' == $posts->status ? _e('草稿') : _e('待审核'); ?></span>
                            <?php endif; ?></td>
                            <td>
                            <?php if ('publish' == $posts->status): ?>
                            <a class="right hidden-by-mouse" href="<?php $posts->permalink(); ?>"><img src="<?php $options->adminUrl('images/view.gif'); ?>" title="<?php _e('浏览 %s', $posts->title); ?>" width="16" height="16" alt="view" /></a>
                            <?php endif; ?>
                            </td>
                            <td><a href="<?php $options->adminUrl('manage-posts.php?uid=' . $posts->author->uid); ?>"><?php $posts->author(); ?></a></td>
                            <td><?php $categories = $posts->categories; $length = count($categories); ?>
                            <?php foreach ($categories as $key => $val): ?>
                                <?php echo '<a href="';
                                $options->adminUrl('manage-posts.php?category=' . $val['mid']
                                . (isset($request->uid) ? '&uid=' . $request->uid : '')
                                . (isset($request->status) ? '&status=' . $request->status : ''));
                                echo '">' . $val['name'] . '</a>' . ($key < $length - 1 ? ', ' : ''); ?>
                            <?php endforeach; ?>
                            </td>
                            <td>
                            <?php if ($posts->hasSaved): ?>
                            <span class="description">
                            <?php $modifyDate = new Typecho_Date($posts->modified); ?>
                            <?php _e('保存于 %s', $modifyDate->word()); ?>
                            </span>
                            <?php else: ?>
                            <?php $posts->dateWord(); ?>
                            <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="8"><h6 class="typecho-list-table-title"><?php _e('没有任何文章'); ?></h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            
            <?php if($posts->have()): ?>
            <div class="typecho-pager">
                <div class="typecho-pager-content">
                    <ul>
                        <?php $posts->pageNav(); ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
