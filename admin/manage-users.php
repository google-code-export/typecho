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
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="150"/>
                        <col width="150"/>
                        <col width="225"/>
                        <col width="250"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th> </th>
                            <th><?php _e('用户名'); ?></th>
                            <th><?php _e('昵称'); ?></th>
                            <th><?php _e('个人主页'); ?></th>
                            <th><?php _e('电子邮件'); ?></th>
                            <th><?php _e('用户组'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Users_Admin')->to($users); ?>
                        <?php while($users->next()): ?>
                        <tr<?php $users->alt(' class="even"', ''); ?>>
                            <td><input type="checkbox" value="<?php $users->uid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('option-user.php?uid=' . $users->uid); ?>"><?php $users->name(); ?></a></td>
                            <td><?php $users->screenName(); ?></td>
                            <td><?php if($users->url): ?><a href="<?php $users->url(); ?>"><?php $users->domainPath(); ?></a><?php else: _e('暂无'); endif; ?></td>
                            <td><?php $users->mail(); ?></td>
                            <td><?php $users->group(); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
