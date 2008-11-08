<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-06 start-01 typecho-dashboard-nav">
                <h3 class="intro"><?php _e('欢迎使用 Typecho,您可以使用下面的链接开始您的 Blog 之旅:'); ?></h3>
            
                <div class="intro-link">
                    <ul>
                        <li><a href="#">撰写一篇新文章</a></li>
                        <li><a href="#">创建一个新页面</a></li>
                        <li><a href="#">等待审核的评论 (10)</a></li>
                        <li><a href="#">添加一个连接地址</a></li>
                        <li><a href="#">更换我的主题</a></li>
                        <li><a href="#">更新我的资料</a></li>
                        <li><a href="#">修改系统设置</a></li>
                    </ul>
                </div>
            
                <h3>Blog Stats</h3>
                <div class="status">
                	<?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                    <p><?php _e('目前有 <em>%s</em> 篇 Blog,并有 <em>%s</em> 条留言在已设定的 <em>%s</em> 个分类中.', 
                    $stat->myPublishedPostsNum, $stat->publishedCommentsNum, $stat->categoriesNum); ?></p>
                    <p><?php _e('最后登录: %s', Typecho_I18n::dateWord($user->logged  + $options->timezone, $options->gmtTime + $options->timezone)); ?></p>
                </div>
            </div>

            <div class="column-12 start-07 typecho-dashboard-main">
                <div class="section">
                    <h4><?php _e('最近发表的文章'); ?></h4>
                    <?php Typecho_Widget::widget('Widget_Contents_Post_Recent')->to($posts); ?>
                    <ul>
                    <?php while($posts->next()): ?>
                        <li><a href="<?php $posts->permalink(); ?>" class="title"><?php $posts->title(); ?></a> <?php _e('发布于'); ?>
                        <?php $posts->category(', '); ?> - <span class="date"><?php $posts->dateWord(); ?></span></li>
                    <?php endwhile; ?>
                    </ul>
                </div>
            	<div class="section">
                    <h4><?php _e('最新得到的回复'); ?></h4>
                    <ul>
                        <?php Typecho_Widget::widget('Widget_Comments_Recent')->to($comments); ?>
                        <?php while($comments->next()): ?>
                        <li><?php $comments->author(true); ?> <?php _e('发布于'); ?> <a href="<?php $comments->permalink(); ?>" class="title"><?php $comments->title(); ?></a> - <span class="date"><?php $comments->dateWord(); ?></span></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <div class="column-06 start-19 typecho-dashboard-nav">
                <div class="update-check">
                    <p class="current">您当前使用的版本是 <em>0.2</em></p>
                    <p class="latest"><a href="#">官方最新版本是 <em>0.2</em></a></p>
                </div>
                <h3>Typecho官方通告</h3>
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
