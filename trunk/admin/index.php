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
                <h3 class="intro"> 欢迎使用 Typecho，您可以使用下面的链接开始您的 Blog 之旅：</h3>
            
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
                    <p>目前有 <em>100</em> 篇 Blog，并有 <em>56600</em> 条留言在已设定的 <em>5</em> 个分类中。</p>
                    <p>最后登录：2008-09-07 星期日 </p>
                </div>
            </div>

            <div class="column-12 start-07 typecho-dashboard-main">
                <div class="section">
                    <h4>最近发表的文章</h4>
                    <ul>
                        <li><a href="#" class="title">杭州杭州院成立研究院成立</a> 发布于
                        <a href="#" class="category">开发相关</a>，<a href="#" class="category">新闻动态</a> - <span class="date">8月7日</span></li>
            			<li><a href="#" class="title">Some Big Sites Are Using Google Trends To Direct Editorial</a> 发布于
                        <a href="#" class="category">开发相关</a>，<a href="#" class="category">新闻动态</a> - <span class="date">8月7日</span></li>
            			<li><a href="#" class="title">上面的是随便在TechCrunch里找到的一个英长文标题测试</a> 发布于
                        <a href="#" class="category">开发相关</a>，<a href="#" class="category">新闻动态</a> - <span class="date">8月7日</span></li>
                    </ul>
                </div>
            	<div class="section">
                    <h4>最新得到的回复</h4>
                    <ul>
                        <?php for ($i = 0; $i < 5; $i++) { ?>
                        <li><a href="#">Micheal Scofield</a> 发表在 <a href="#" class="title">越狱4里的 Shut up 科技男</a> - <span class="date">8月7日</span></li>
                        <?php } ?>
            			<li><a href="#">Micheal Scofield</a> 发表在 <a href="#" class="title">Some Big Sites Are Using Google Trends To Direct Editorial</a> - <span class="date">8月7日</span></li>
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
<?php include 'copyright.php'; ?>
