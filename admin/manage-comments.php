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
                <ul class="typecho-option-tabs">
                    <li<?php if(!Typecho_Request::isSetParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php'); ?>">所有</a></li>
                    <li<?php if('approved' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=approved'); ?>">展现</a></li>
                    <li<?php if('waiting' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=waiting'); ?>">待审核</a></li>
                    <li<?php if('spam' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=spam'); ?>">垃圾</a></li>
                </ul>
            
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作：<a href="#">全选</a>，<a href="#">反选</a>，<a href="#">删除选中项</a></p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                    <?php if(Typecho_Request::isSetParameter('status')): ?>
                        <input type="hidden" value="<?php echo Typecho_Request::getParameter('status'); ?>" name="status" />
                    <?php endif; ?>
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>

                    	<?php Typecho_Widget::widget('Widget_Comments_Admin')->to($comments); ?>
                        
                        <ul class="typecho-list-notable clearfix">
                        <?php while($comments->next()): ?>
                        <li class="column-24<?php $comments->alt(' even', ''); ?>">
                            <div class="column-01 center">
                                <input type="checkbox" value="<?php $comments->coid(); ?>" name="coid[]"/>
                            </div>
                            <div class="column-02 center">
                                <?php $comments->gravatar(); ?>
                            </div>
                            <div class="column-21">
                                <?php $comments->author(); ?>
                                <?php if($comments->mail): ?>
                                 | 
                                <a href="mailto:<?php $comments->mail(); ?>"><?php $comments->mail(); ?></a>
                                <?php endif; ?>
                                <?php if($comments->ip): ?>
                                 | 
                                <?php $comments->ip(); ?>
                                <?php endif; ?>
                                <?php $comments->content(); ?>
                                <div class="right">
                                <?php $comments->dateWord(); ?>
                                &nbsp;&nbsp;
                                <a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a>
                                </div>
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
