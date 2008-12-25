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
            <div class="column-06 start-01 typecho-dashboard-nav">
                <h3 class="intro"><?php _e('欢迎使用 Typecho,您可以使用下面的链接开始您的 Blog 之旅:'); ?></h3>
            
                <div class="intro-link">
                    <ul>
                        <li><a href="#"><?php _e('更新我的资料'); ?></a></li>
                        <?php if($user->pass('contributor', true)): ?>
                        <li><a href="<?php $options->adminUrl('write-post.php'); ?>"><?php _e('撰写一篇新文章'); ?></a></li>
                        <?php if($user->pass('editor', true) && 'on' == Typecho_Request::getParameter('__typecho_all_comments') && $stat->waitingCommentsNum > 0): ?> 
                            <li><a href="<?php $options->adminUrl('manage-comments.php?status=waiting'); ?>"><?php _e('等待审核的评论'); ?></a>
                            <span class="balloon"><?php $stat->waitingCommentsNum(); ?></span>
                            </li>
                        <?php elseif($stat->myWaitingCommentsNum > 0): ?>
                            <li><a href="<?php $options->adminUrl('manage-comments.php?status=waiting'); ?>"><?php _e('等待审核的评论'); ?></a>
                            <span class="balloon"><?php $stat->myWaitingCommentsNum(); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if($user->pass('editor', true) && 'on' == Typecho_Request::getParameter('__typecho_all_comments') && $stat->spamCommentsNum > 0): ?> 
                            <li><a href="<?php $options->adminUrl('manage-comments.php?status=spam'); ?>"><?php _e('垃圾评论'); ?></a>
                            <span class="balloon"><?php $stat->spamCommentsNum(); ?></span>
                            </li>
                        <?php elseif($stat->mySpamCommentsNum > 0): ?>
                            <li><a href="<?php $options->adminUrl('manage-comments.php?status=spam'); ?>"><?php _e('垃圾评论'); ?></a>
                            <span class="balloon"><?php $stat->mySpamCommentsNum(); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if($user->pass('editor', true)): ?>
                        <li><a href="<?php $options->adminUrl('write-page.php'); ?>"><?php _e('创建一个新页面'); ?></a></li>
                        <?php if($user->pass('administrator', true)): ?>
                        <li><a href="<?php $options->adminUrl('themes.php'); ?>"><?php _e('更换我的主题'); ?></a></li>
                        <li><a href="<?php $options->adminUrl('option-general.php'); ?>"><?php _e('修改系统设置'); ?></a></li>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            
                <h3><?php _e('统计信息'); ?></h3>
                <div class="status">
                    <p><?php _e('目前有 <em>%s</em> 篇 Blog,并有 <em>%s</em> 条关于你的评论在已设定的 <em>%s</em> 个分类中.', 
                    $stat->myPublishedPostsNum, $stat->myPublishedCommentsNum, $stat->categoriesNum); ?></p>
                    <p><?php _e('最后登录: %s', Typecho_I18n::dateWord($user->logged  + $options->timezone, $options->gmtTime + $options->timezone)); ?></p>
                </div>
            </div>

            <div class="column-12 start-07 typecho-dashboard-main">
                <div class="section">
                    <h4><?php _e('最近发表的文章'); ?></h4>
                    <?php Typecho_Widget::widget('Widget_Contents_Post_Recent')->to($posts); ?>
                    <ul>
                    <?php if($posts->have()): ?>
                    <?php while($posts->next()): ?>
                        <li><a href="<?php $posts->permalink(); ?>" class="title"><?php $posts->title(); ?></a> <?php _e('发布于'); ?>
                        <?php $posts->category(', '); ?> - <span class="date"><?php $posts->dateWord(); ?></span></li>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <li><em><?php _e('暂时没有文章'); ?></em></li>
                    <?php endif; ?>
                    </ul>
                </div>
            	<div class="section">
                    <h4><?php _e('最新得到的回复'); ?></h4>
                    <ul>
                        <?php Typecho_Widget::widget('Widget_Comments_Recent')->to($comments); ?>
                        <?php if($comments->have()): ?>
                        <?php while($comments->next()): ?>
                        <li><?php $comments->author(true); ?> <?php _e('发布于'); ?> <a href="<?php $comments->permalink(); ?>" class="title"><?php $comments->title(); ?></a> - <span class="date"><?php $comments->dateWord(); ?></span></li>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <li><em><?php _e('暂时没有回复'); ?></em></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="column-06 start-19 typecho-dashboard-nav">
                <div class="update-check">
                    <p class="current">您当前使用的版本是 <em>0.2</em></p>
                    <p class="latest"><a href="#">官方最新版本是 <em>0.2</em></a></p>
                </div>
                <h3>官方消息</h3>
                <div class="intro-link">
                    <ul>
                        <li><a href="#">Typecho杭州研究院成立</a> - <span class="date">8月7日</span></li>
                        <li><a href="#">欢迎Fen回归</a> - <span class="date">8月7日</span></li>
                        <li><a href="#">Typecho开始支持PostgreSQL</a> - <span class="date">8月7日</span></li>
                        <li><a href="#">功能需求与UI关心</a> - <span class="date">8月7日</span></li>
                        <li><a href="#">下阶段工作计划</a> - <span class="date">8月7日</span></li>
                        <li><a href="#">Some Big Sites Are Using Google Trends To Direct Editorial</a> - <span class="date">8月7日</span></li>
                    </ul>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
