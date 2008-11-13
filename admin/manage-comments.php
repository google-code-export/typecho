<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作：<a href="#">全选</a>，<a href="#">反选</a>，<a href="#">删除选中项</a></p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />            
                    <select name="status">
                        <option value=""><?php _e('所有页面'); ?></option>
                        <option value="published"<?php if(Typecho_Request::getParameter('status') == 'published'): ?> selected="true"<?php endif; ?>><?php _e('已发布'); ?></option>
                        <option value="draft"<?php if(Typecho_Request::getParameter('status') == 'draft'): ?> selected="true"<?php endif; ?>><?php _e('草稿'); ?></option>
                    </select>
                    
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="125"/>
                        <col width="125"/>
                        <col width="350"/>
                        <col width="250"/>
                        <col width="125"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th> </th>
                            <th><?php _e('作者'); ?></th>
                            <th><?php _e('日期'); ?></th>
                            <th><?php _e('内容'); ?></th>
                            <th><?php _e('文章'); ?></th>
                            <th><?php _e('状态'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Comments_Admin')->to($comments); ?>
                    	<?php if($comments->have()): ?>
                        <?php while($comments->next()): ?>
                        <tr<?php $comments->alt('', ' class="even"'); ?>>
                            <td><input type="checkbox" value="<?php $comments->coid(); ?>" name="coid[]"/></td>
                            <td><?php $comments->author(true); ?></td>
                            <td><?php $comments->dateWord(); ?></td>
                            <td><?php $comments->excerpt(20); ?></td>
                            <td><a href="<?php $comments->permalink(); ?>" target="_blank"><?php $comments->title(); ?></a></td>
                            <td>
                            <?php if('approved' == $comments->status):
                            _e('展现');
                            elseif('waiting' == $comments->status):
                            _e('待审核');
                            elseif('spam' == $comments->status):
                            _e('垃圾');
                            endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="5"><?php _e('没有任何评论'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            <?php if($comments->have()): ?>
            <div class="typecho-pager">
                <div class="typecho-pager-content">
                    <h5><?php _e('页面'); ?>:&nbsp;</h5>
                    <ul>
                        <?php $comments->pageNav(); ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
