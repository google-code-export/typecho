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
                    <p class="operate">
                    <a class="button" href="<?php $options->adminUrl('user.php'); ?>"><?php _e('新增用户'); ?></a>
                    <?php _e('操作'); ?>: 
                    <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                    <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>,&nbsp;&nbsp;&nbsp;
                    <?php _e('选中项'); ?>: 
                    <span rel="delete" lang="<?php _e('你确认要删除这些用户吗?'); ?>" class="operate-button operate-delete typecho-table-select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_users" class="operate-form" action="<?php $options->index('/action/users-edit'); ?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="150"/>
                        <col width="150"/>
                        <col width="30"/>
                        <col width="300"/>
                        <col width="165"/>
                        <col width="70"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th><?php _e('用户名'); ?></th>
                            <th><?php _e('昵称'); ?></th>
                            <th> </th>
                            <th><?php _e('电子邮件'); ?></th>
                            <th><?php _e('用户组'); ?></th>
                            <th class="typecho-radius-topright"><?php _e('文章'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Users_Admin')->to($users); ?>
                        <?php while($users->next()): ?>
                        <tr<?php $users->alt(' class="even"', ''); ?> id="user-<?php $users->uid(); ?>">
                            <td><input type="checkbox" value="<?php $users->uid(); ?>" name="uid[]"/></td>
                            <td><a href="<?php $options->adminUrl('user.php?uid=' . $users->uid); ?>"><?php $users->name(); ?></a></td>
                            <td><?php $users->screenName(); ?></td>
                            <td>
                            <a class="right hidden-by-mouse" href="<?php $users->permalink(); ?>"><img src="<?php $options->adminUrl('images/view.gif'); ?>" title="<?php _e('浏览 %s', $users->screenName); ?>" width="16" height="16" alt="view" /></a>
                            </td>
                            <td><?php if($users->mail): ?><a href="mailto:<?php $users->mail(); ?>"><?php $users->mail(); ?></a><?php else: _e('暂无'); endif; ?></td>
                            <td><?php switch ($users->group) {
                                case 'administrator':
                                    _e('管理员');
                                    break;
                                case 'editor':
                                    _e('编辑');
                                    break;
                                case 'contributor':
                                    _e('贡献者');
                                    break;
                                case 'subscriber':
                                    _e('关注者');
                                    break;
                                case 'visitor':
                                    _e('访问者');
                                    break;
                                default:
                                    break;
                            } ?></td>
                            <td><a href="<?php $options->adminUrl('manage-posts.php?uid=' . $users->uid); ?>" class="balloon-button left size-<?php echo Typecho_Common::splitByCount($users->postsNum, 1, 10, 20, 50, 100); ?>"><?php $users->postsNum(); ?></a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
                
            <?php if($users->have()): ?>
            <div class="typecho-pager">
                <div class="typecho-pager-content">
                    <h5><?php _e('页面'); ?>:&nbsp;</h5>
                    <ul>
                        <?php $users->pageNav(); ?>
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
