<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
$comments = Typecho_Widget::widget('Widget_Comments_Admin');
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <ul class="typecho-option-tabs">
                    <li<?php if(!isset($request->status) || 'approved' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php'
                    . (isset($request->cid) ? '?cid=' . $request->cid : '')); ?>"><?php _e('已通过'); ?></a></li>
                    <li<?php if('waiting' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=waiting'
                    . (isset($request->cid) ? '&cid=' . $request->cid : '')); ?>"><?php _e('待审核'); ?>
                    <?php if('on' != $request->get('__typecho_all_comments') && $stat->myWaitingCommentsNum > 0 && !isset($request->cid)): ?> 
                        <span class="balloon"><?php $stat->myWaitingCommentsNum(); ?></span>
                    <?php elseif('on' == $request->get('__typecho_all_comments') && $stat->waitingCommentsNum > 0 && !isset($request->cid)): ?>
                        <span class="balloon"><?php $stat->waitingCommentsNum(); ?></span>
                    <?php elseif(isset($request->cid) && $stat->currentWaitingCommentsNum > 0): ?>
                        <span class="balloon"><?php $stat->currentWaitingCommentsNum(); ?></span>
                    <?php endif; ?>
                    </a></li>
                    <li<?php if('spam' == $request->get('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-comments.php?status=spam'
                    . (isset($request->cid) ? '&cid=' . $request->cid : '')); ?>"><?php _e('垃圾'); ?>
                    <?php if('on' != $request->get('__typecho_all_comments') && $stat->mySpamCommentsNum > 0 && !isset($request->cid)): ?> 
                        <span class="balloon"><?php $stat->mySpamCommentsNum(); ?></span>
                    <?php elseif('on' == $request->get('__typecho_all_comments') && $stat->spamCommentsNum > 0 && !isset($request->cid)): ?>
                        <span class="balloon"><?php $stat->spamCommentsNum(); ?></span>
                    <?php elseif(isset($request->cid) && $stat->currentSpamCommentsNum > 0): ?>
                        <span class="balloon"><?php $stat->currentSpamCommentsNum(); ?></span>
                    <?php endif; ?>
                    </a></li>
                    <?php if($user->pass('editor', true) && !isset($request->cid)): ?>
                        <li class="right<?php if('on' == $request->get('__typecho_all_comments')): ?> current<?php endif; ?>"><a href="<?php echo $request->makeUriByRequest('__typecho_all_comments=on'); ?>"><?php _e('所有'); ?></a></li>
                        <li class="right<?php if('on' != $request->get('__typecho_all_comments')): ?> current<?php endif; ?>"><a href="<?php echo $request->makeUriByRequest('__typecho_all_comments=off'); ?>"><?php _e('我的'); ?></a></li>
                    <?php endif; ?>
                </ul>
            
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                    <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                    <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                    <?php _e('选中项') ?>:
                    <span rel="approved" class="operate-button typecho-table-select-submit"><?php _e('通过'); ?></span>, 
                    <span rel="waiting" class="operate-button typecho-table-select-submit"><?php _e('待审核'); ?></span>, 
                    <span rel="spam" class="operate-button typecho-table-select-submit"><?php _e('标记垃圾'); ?></span>, 
                    <span rel="delete" lang="<?php _e('你确认要删除这些评论吗?'); ?>" class="operate-button operate-delete typecho-table-select-submit"><?php _e('删除'); ?></span><?php if('spam' == $request->get('status')): ?>, 
                    <span rel="delete-spam" lang="<?php _e('你确认要删除所有垃圾评论吗?'); ?>" class="operate-button operate-delete typecho-table-select-submit"><?php _e('删除所有垃圾评论'); ?></span>
                    <?php endif; ?>
                    </p>
                    <p class="search">
                    <?php if ('' != $request->keywords || '' != $request->category): ?>
                    <a href="<?php $options->adminUrl('manage-comments.php' 
                    . (isset($request->status) || isset($request->cid) ? '?' .
                    (isset($request->status) ? 'status=' . htmlspecialchars($request->get('status')) : '') .
                    (isset($request->cid) ? (isset($request->status) ? '&' : '') . 'cid=' . htmlspecialchars($request->get('cid')) : '') : '')); ?>"><?php _e('&laquo; 取消筛选'); ?></a>
                    <?php endif; ?>
                    <input type="text" value="<?php '' != $request->keywords ? print(htmlspecialchars($request->keywords)) : _e('请输入关键字'); ?>"<?php if ('' == $request->keywords): ?> onclick="value='';name='keywords';" <?php else: ?> name="keywords"<?php endif; ?>/>
                    <?php if(isset($request->status)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($request->get('status')); ?>" name="status" />
                    <?php endif; ?>
                    <?php if(isset($request->cid)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($request->get('cid')); ?>" name="cid" />
                    <?php endif; ?>
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>

                <form method="post" name="manage_comments" class="operate-form" action="<?php $options->index('/action/comments-edit'); ?>">
                    <ul class="typecho-list-notable clearfix">
                    <?php if($comments->have()): ?>
                    <?php while($comments->next()): ?>
                    <li class="column-24<?php $comments->alt(' even', ''); ?>" id="<?php $comments->theId(); ?>">
                        <div class="column-01 center">
                            <input type="checkbox" value="<?php $comments->coid(); ?>" name="coid[]"/>
                        </div>
                        <div class="column-23 comment-body">
                            <div class="content">
                                <div class="comment-avatar">
                                    <?php $comments->gravatar(); ?>
                                </div>
                            
                                <div class="comment-meta">
                                    <span class="<?php $comments->type(); ?>"></span>
                                    <span class="comment-author"><?php $comments->author(true); ?></span>
                                    <?php if($comments->mail): ?>
                                     | 
                                    <a href="mailto:<?php $comments->mail(); ?>"><?php $comments->mail(); ?></a>
                                    <?php endif; ?>
                                    <?php if($comments->ip): ?>
                                     | 
                                    <?php $comments->ip(); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="comment-content">
                                    <?php $comments->content(); ?>
                                </div>
                            </div>
                            
                            <div class="line">
                                <div class="left hidden-by-mouse">
                                    <?php if('approved' == $comments->status): ?>
                                    <span class="weak"><?php _e('通过'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $options->index('/action/comments-edit?do=approved&coid=' . $comments->coid); ?>" class="ajax"><?php _e('通过'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <?php if('waiting' == $comments->status): ?>
                                    <span class="weak"><?php _e('待审核'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $options->index('/action/comments-edit?do=waiting&coid=' . $comments->coid); ?>" class="ajax"><?php _e('待审核'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <?php if('spam' == $comments->status): ?>
                                    <span class="weak"><?php _e('垃圾'); ?></span>
                                    <?php else: ?>
                                    <a href="<?php $options->index('/action/comments-edit?do=spam&coid=' . $comments->coid); ?>" class="ajax"><?php _e('垃圾'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <a href="#<?php $comments->theId(); ?>" rel="<?php $options->index('/action/comments-edit?do=get&coid=' . $comments->coid); ?>" class="ajax operate-edit"><?php _e('编辑'); ?></a>
                                    <?php if('approved' == $comments->status && 'comment' == $comments->type): ?>
                                     | 
                                    <a href="#<?php $comments->theId(); ?>" rel="<?php $options->index('/action/comments-edit?do=reply&coid=' . $comments->coid); ?>" class="ajax operate-reply"><?php _e('回复'); ?></a>
                                    <?php endif; ?>
                                     | 
                                    <a lang="<?php _e('你确认要删除%s的评论吗?', htmlspecialchars($comments->author)); ?>" href="<?php $options->index('/action/comments-edit?do=delete&coid=' . $comments->coid); ?>" class="ajax operate-delete"><?php _e('删除'); ?></a>
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
                    <?php if(isset($request->cid)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($request->get('cid')); ?>" name="cid" />
                    <?php endif; ?>
                </form>
                
            <?php if($comments->have()): ?>
            <div class="typecho-pager">
                <div class="typecho-pager-content">
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
<?php
include 'copyright.php';
include 'common-js.php';
?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
        
            $(document).getElements('.typecho-list-notable li .operate-edit').addEvent('click', function () {
                
                var form = this.getParent('li').getElement('.comment-form');
                var request;
                
                if (form) {
                    
                    if (request) {
                        request.cancel();
                    }
                    
                    form.destroy();
                    this.getParent('li').getElement('.content').setStyle('display', '');
                    this.clicked = false;
                    
                } else {
                    if ('undefined' == typeof(this.clicked) || !this.clicked) {
                        this.clicked = true;
                        this.getParent('.line').addClass('loading');
                        
                        request = new Request.JSON({
                            url: this.getProperty('rel'),
                            
                            onComplete: (function () {
                                this.clicked = false;
                            }).bind(this),
                            
                            onSuccess: (function (json) {
                            
                                if (json.success) {
                                    var coid = this.getParent('li').getElement('input[type=checkbox]').get('value');
                                    
                                    var form = new Element('div', {
                                        'class': 'comment-form',
                                    
                                        'html': '<label for="author-' + coid + '"><?php _e('名称'); ?></label>' +
                                        '<input type="text" class="text" name="author" id="author-' + coid + '" />' +
                                        '<label for="mail"><?php _e('电子邮件'); ?></label>' +
                                        '<input type="text" class="text" name="mail" id="mail-' + coid + '" />' +
                                        '<label for="url"><?php _e('个人主页'); ?></label>' +
                                        '<input type="text" class="text" name="url" id="url-' + coid + '" />' +
                                        '<textarea name="text" id="text-' + coid + '"></textarea>' +
                                        '<p><button id="submit-' + coid + '"><?php _e('保存评论'); ?></button>' +
                                        '<input type="hidden" name="coid" id="coid-' + coid + '" /></p>'
                                    
                                    });
                                    
                                    form.getElement('input[name=author]').set('value', json.comment.author);
                                    form.getElement('input[name=mail]').set('value', json.comment.mail);
                                    form.getElement('input[name=url]').set('value', json.comment.url);
                                    form.getElement('input[name=coid]').set('value', coid);
                                    form.getElement('textarea[name=text]').set('value', json.comment.text);
                                    
                                    this.getParent('li').getElement('.content').setStyle('display', 'none');
                                    form.inject(this.getParent('li').getElement('.line'), 'before');
                                    form.getElement('#submit-' + coid).addEvent('click', (function () {
                                        var query = this.getParent('li').getElement('.comment-form').toQueryString();
                                        
                                        var sRequest = new Request.JSON({
                                            url: this.getProperty('rel').replace('do=get', 'do=edit'),
                                            
                                            onComplete: (function () {
                                                var li = this.getParent('li');
                                            
                                                li.getElement('.content').setStyle('display', '');
                                                li.getElement('.comment-form').destroy();
                                                var myFx = new Fx.Tween(li);
                                                
                                                var bg = li.getStyle('background-color');
                                                if (!bg || 'transparent' == bg) {
                                                    bg = '#F7FBE9';
                                                }
                                                
                                                myFx.addEvent('complete', (function () {
                                                    this.setStyle('background-color', '');
                                                }).bind(li));
                                                
                                                myFx.start('background-color', '#AACB36', bg);
                                            }).bind(this),
                                            
                                            onSuccess: (function (json) {
                                                if (json.success) {
                                                    
                                                    var commentMeta = '';
                                                    commentMeta += '<span class="' + json.comment.type + '"></span> ';
                                                    
                                                    if (json.comment.url) {
                                                        commentMeta += '<a target="_blank" href="' + json.comment.url + '">' + json.comment.author + '</a> | ';
                                                    } else {
                                                        commentMeta += json.comment.author + ' | ';
                                                    }
                                                    
                                                    if (json.comment.mail) {
                                                        commentMeta += '<a href="mailto:' + json.comment.mail + '">' + json.comment.mail + '</a> | ';
                                                    }
                                                    
                                                    commentMeta += json.comment.ip;
                                                    
                                                    this.getParent('li').getElement('.comment-meta').set('html', commentMeta);
                                                    this.getParent('li').getElement('.comment-content').set('html', json.comment.content);
                                                }
                                            }).bind(this)
                                        }).send(query + '&do=edit');
                                        
                                        return false;
                                        
                                    }).bind(this));
                                    
                                    this.getParent('.line').removeClass('loading');
                                } else {
                                    alert(json.message);
                                }
                                
                            }).bind(this)
                        }).send();
                    }
                    
                }
                
                return false;
            });
            
            $(document).getElements('.typecho-list-notable li .operate-reply').addEvent('click', function () {
            
                var form = this.getParent('li').getElement('.reply-form');
                var request;
                
                if (form) {
                    
                    if (request) {
                        request.cancel();
                    }
                    
                    form.destroy();
                    this.clicked = false;
                    
                } else {
                    if (('undefined' == typeof(this.clicked) || !this.clicked)
                        && ('undefined' == typeof(this.replied) || !this.replied)) {
                        this.clicked = true;
                        
                        var coid = this.getParent('li').getElement('input[type=checkbox]').get('value');

                        var form = new Element('div', {
                            'class': 'reply-form',
                        
                            'html': '<textarea name="text"></textarea>' +
                            '<p><button id="reply-' + coid + '"><?php _e('回复评论'); ?></button></p>'
                        
                        });
                        
                        form.inject(this.getParent('li').getElement('.line'), 'after');
                        
                        var ta = form.getElement('textarea'), rg = ta.getSelectedRange(),
                        instStr = '<a href="#' + this.getParent('li').get('id') + '">@' 
                            + this.getParent('li').getElement('.comment-author').get('text') + "</a>\n";
                        
                        ta.set('value', instStr);
                        ta.focus();
                        ta.selectRange(instStr.length + 1, instStr.length + 1);
                        
                        form.getElement('#reply-' + coid).addEvent('click', (function () {
                            if ('' == this.getParent('li').getElement('.reply-form textarea[name=text]').get('value')) {
                                alert('<?php _e('必须填写内容'); ?>');
                                return false;
                            }
                        
                            var query = this.getParent('li').getElement('.reply-form').toQueryString();
                            
                            var sRequest = new Request.JSON({
                                url: this.getProperty('rel'),
                                
                                onComplete: (function () {
                                    var li = this.getParent('li');
                                    li.getElement('.reply-form').destroy();
                                    li.removeClass('hover');
                                    li.getElement('.operate-reply').clicked = false;
                                }).bind(this),
                                
                                onSuccess: (function (json) {
                                    if (json.success) {
                                        var li = this.getParent('li');
                                        
                                        var msg = new Element('div', {
                                            'class': 'reply-message',
                                        
                                            'html': json.comment.content
                                        
                                        });
                                        
                                        li.getElement('.operate-reply').set('html', '<?php _e('取消回复'); ?>');
                                        li.getElement('.operate-reply').replied = true;
                                        li.getElement('.operate-reply').child = json.comment.coid;
                                        msg.inject(li.getElement('.line'), 'after');
                                    }
                                }).bind(this)
                            }).send(query);
                            
                            return false;
                            
                        }).bind(this));
                    } else if (this.replied) {
                        this.getParent('.line').addClass('loading');
                    
                        var sRequest = new Request.JSON({
                            url: '<?php $options->index('/action/comments-edit?do=delete'); ?>',
                            
                            onComplete: (function () {
                                var li = this.getParent('li');
                                li.getElement('.operate-reply').clicked = false;
                                this.getParent('.line').removeClass('loading');
                            }).bind(this),
                            
                            onSuccess: (function (json) {
                                if (json.success) {
                                    var li = this.getParent('li');
                                    
                                    li.getElement('.operate-reply').set('html', '<?php _e('回复'); ?>');
                                    li.getElement('.operate-reply').replied = false;
                                    li.getElement('.operate-reply').set('child', 0);
                                    li.getElement('.reply-message').destroy();
                                }
                            }).bind(this)
                        }).send('coid=' + this.child);
                    }
                    
                }
                
                return false;
            });
        
        });
    })();
</script>
<?php
include 'footer.php';
?>
