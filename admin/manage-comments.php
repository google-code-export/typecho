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

                    	<?php Typecho_Widget::widget('Widget_Comments_Admin')->to($comments); ?>
                        
                        <ul class="typecho-list-notable">
                        <?php while($comments->next()): ?>
                        <li<?php $comments->alt('', ' class="even"'); ?>>

                            <div class="header">
                            <span class="column-21">
                            <input type="checkbox" value="<?php $comments->coid(); ?>" name="coid[]"/>
                            <strong><?php _e('%s | %s发表在', $comments->author, $comments->dateWord); ?></strong>
                            <a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a>
                            </span>
                            <span class="column-02 right">
                            <?php $comments->gravatar(32); ?>
                            </span>
                            </div>

                            <?php $comments->content(); ?>
                            
                            <div class="footer">
                            <span class="left">
                            <strong>IP:</strong> <?php $comments->ip(); ?>
                            <?php if($comments->mail): ?>
                             | <strong>MAIL:</strong> <a href="mailto:<?php $comments->mail(); ?>"><?php $comments->mail(); ?></a>
                            <?php endif; ?>
                            <?php if($comments->url): ?>
                             | <strong>URL:</strong> <a href="<?php $comments->url(); ?>"><?php $comments->url(); ?></a>
                            <?php endif; ?>
                            </span>
                            
                            <span class="right">
                            <?php if('approved' == $comments->status):
                            _e('展现');
                            elseif('waiting' == $comments->status):
                            _e('待审核');
                            elseif('spam' == $comments->status):
                            _e('垃圾');
                            endif; ?>
                            </span>
                            </div>
                        </li>
                        <?php endwhile; ?>
                        </ul>
                
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
