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
                    <li<?php if(!Typecho_Request::isSetParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php'); ?>"><?php _e('所有'); ?></a></li>
                    <li<?php if('waiting' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=waiting'); ?>"><?php _e('待审核'); ?></a></li>
                    <li<?php if('spam' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=spam'); ?>"><?php _e('垃圾'); ?></a></li>
                </ul>
            
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                    <span onclick="typechoOperate('.typecho-list-notable', 'selectAll');" class="operate-button select-all"><?php _e('全选'); ?></span>, 
                    <span onclick="typechoOperate('.typecho-list-notable', 'selectNone');" class="operate-button select-reverse"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                    <?php _e('选中项') ?>:
                    <span onclick="typechoSubmit('form[name=manage_comments]', 'input[name=do]', 'approved');" class="operate-button select-submit"><?php _e('通过'); ?></span>, 
                    <span onclick="typechoSubmit('form[name=manage_comments]', 'input[name=do]', 'waiting');" class="operate-button select-submit"><?php _e('待审核'); ?></span>, 
                    <span onclick="typechoSubmit('form[name=manage_comments]', 'input[name=do]', 'spam');" class="operate-button select-submit"><?php _e('标记垃圾'); ?></span>, 
                    <span onclick="typechoSubmit('form[name=manage_comments]', 'input[name=do]', 'delete');" class="operate-button select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                    <?php if(Typecho_Request::isSetParameter('status')): ?>
                        <input type="hidden" value="<?php echo Typecho_Request::getParameter('status'); ?>" name="status" />
                    <?php endif; ?>
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>

                <form method="post" name="manage_comments" class="operate-form" action="<?php $options->index('Comments/Edit.do'); ?>">
                    <?php Typecho_Widget::widget('Widget_Comments_Admin')->to($comments); ?>
                    
                    <ul class="typecho-list-notable clearfix">
                    <?php if($comments->have()): ?>
                    <?php while($comments->next()): ?>
                    <li class="column-24<?php $comments->alt(' even', ''); ?>">
                        <div class="column-01 center">
                            <input type="checkbox" value="<?php $comments->coid(); ?>" name="coid[]"/>
                        </div>
                        <div class="column-02 center">
                            <?php $comments->gravatar(); ?>
                        </div>
                        <div class="column-21">
                            <?php $comments->author(true); ?>
                            <?php if($comments->mail): ?>
                             | 
                            <a href="mailto:<?php $comments->mail(); ?>"><?php $comments->mail(); ?></a>
                            <?php endif; ?>
                            <?php if($comments->ip): ?>
                             | 
                            <?php $comments->ip(); ?>
                            <?php endif; ?>
                            <?php $comments->content(); ?>
                            <div class="line">
                                <div class="left">
                                    <?php if('approved' == $comments->status): ?>
                                    <span class="weak"><?php _e('通过'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $options->index('Comments/Edit.do?do=approved&coid=' . $comments->coid); ?>" class="ajax"><?php _e('通过'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <?php if('waiting' == $comments->status): ?>
                                    <span class="weak"><?php _e('待审核'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $options->index('Comments/Edit.do?do=waiting&coid=' . $comments->coid); ?>" class="ajax"><?php _e('待审核'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <?php if('spam' == $comments->status): ?>
                                    <span class="weak"><?php _e('垃圾'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $options->index('Comments/Edit.do?do=spam&coid=' . $comments->coid); ?>" class="ajax"><?php _e('垃圾'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <a href="<?php $options->index('Comments/Edit.do?do=delete&coid=' . $comments->coid); ?>" class="ajax"><?php _e('删除'); ?></a>
                                </div>
                                <div class="right">
                                    <?php $comments->dateWord(); ?>
                                    &nbsp;&nbsp;
                                    <a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <li class="even">
                        <h6 class="typecho-list-table-title"><?php _e('没有评论') ?></h6>
                    </li>
                    <?php endif; ?>
                    </ul>
                    <input type="hidden" name="do" value="delete" />
                </form>
                
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
