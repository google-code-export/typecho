<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main manage-metas">
                <div class="column-16 start-01">
                    <ul class="typecho-option-tabs">
                        <li<?php if(!Typecho_Request::isSetParameter('type') || 'category' == Typecho_Request::getParameter('type')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-metas.php'); ?>"><?php _e('分类'); ?></a></li>
                        <li<?php if('tag' == Typecho_Request::getParameter('type')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-metas.php?type=tag'); ?>"><?php _e('标签'); ?></a></li>
                    </ul>
                    
                    <?php if(!Typecho_Request::isSetParameter('type') || 'category' == Typecho_Request::getParameter('type')): ?>
                    <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($categories); ?>
                    <form method="post" name="manage_categories" class="operate-form" action="<?php $options->index('Metas/Category/Edit.do'); ?>">
                    <div class="typecho-list-operate">
                        <p class="operate"><?php _e('操作'); ?>: 
                            <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                            <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                            <?php _e('选中项'); ?>: 
                            <span rel="delete" class="operate-button typecho-table-select-submit"><?php _e('删除'); ?></span>, 
                            <span rel="merge" class="operate-button typecho-table-select-submit"><?php _e('合并到'); ?></span>
                            <select name="merge">
                                <?php $categories->parse('<option value="{mid}">{name}</option>'); ?>
                            </select>
                        </p>
                    </div>
                    
                    <table class="typecho-list-table draggable">
                        <colgroup>
                            <col width="25"/>
                            <col width="130"/>
                            <col width="50"/>
                            <col width="130"/>
                            <col width="170"/>
                            <col width="65"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="typecho-radius-topleft"> </th>
                                <th><?php _e('名称'); ?></th>
                                <th> </th>
                                <th><?php _e('缩略名'); ?></th>
                                <th><?php _e('描述'); ?></th>
                                <th class="typecho-radius-topright"><?php _e('文章数'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($categories->have()): ?>
                            <?php while ($categories->next()): ?>
                            <tr<?php $categories->alt(' class="even"', ''); ?> id="<?php $categories->theId(); ?>">
                                <td><input type="checkbox" value="<?php $categories->mid(); ?>" name="mid[]"/></td>
                                <td><a href="<?php echo Typecho_Request::uri('mid=' . $categories->mid); ?>"><?php $categories->name(); ?></a></td>
                                <td>
                                <?php if ($options->defaultCategory == $categories->mid): ?>
                                <span class="right description"><?php _e('默认'); ?></span>
                                <?php else: ?>
                                <a class="right hidden-by-mouse" href="<?php $options->index('Metas/Category/Edit.do?do=default&mid=' . $categories->mid); ?>"><?php _e('默认'); ?></a>
                                <?php endif; ?>
                                </td>
                                <td><?php $categories->slug(); ?></td>
                                <td><?php $categories->description(); ?></td>
                                <td><a class="balloon-button left size-<?php echo Typecho_Common::splitByCount($categories->count, 1, 10, 20, 50, 100); ?>" href="<?php $categories->permalink(); ?>"><?php $categories->count(); ?></a></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr class="even">
                                <td colspan="5"><h6 class="typecho-list-table-title"><?php _e('没有任何分类'); ?></h6></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="do" value="delete" />
                    </form>
                    <?php else: ?>
                    <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud', 'sort=mid&desc=0')->to($tags); ?>
                    <form method="post" name="manage_tags" class="operate-form" action="<?php $options->index('Metas/Tag/Edit.do'); ?>">
                    <div class="typecho-list-operate">
                        <p class="operate"><?php _e('操作'); ?>: 
                            <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                            <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                            <?php _e('选中项'); ?>: 
                            <span rel="delete" class="operate-button typecho-table-select-submit"><?php _e('删除'); ?></span>, 
                            <span rel="merge" class="operate-button typecho-table-select-submit"><?php _e('合并到'); ?></span> 
                            <input type="text" name="merge" />
                        </p>
                    </div>
                    
                    <ul class="typecho-list-notable tag-list clearfix typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                        <?php if($tags->have()): ?>
                        <?php while ($tags->next()): ?>
                        <li class="size-<?php $tags->split(5, 10, 20, 30); ?>" id="<?php $tags->theId(); ?>">
                        <input type="checkbox" value="<?php $tags->mid(); ?>" name="mid[]"/>
                        <span rel="<?php echo Typecho_Request::uri('mid=' . $tags->mid); ?>"><?php $tags->name(); ?></span>
                        </li>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <h6 class="typecho-list-table-title"><?php _e('没有任何标签'); ?></h6>
                        <?php endif; ?>
                    </ul>
                    <input type="hidden" name="do" value="delete" />
                    </form>
                    <?php endif; ?>
                    
                </div>
                <div class="column-08 start-17 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                    <?php if(!Typecho_Request::isSetParameter('type') || 'category' == Typecho_Request::getParameter('type')): ?>
                        <?php Typecho_Widget::widget('Widget_Metas_Category_Edit')->form()->render(); ?>
                    <?php else: ?>
                        <?php Typecho_Widget::widget('Widget_Metas_Tag_Edit')->form()->render(); ?>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var _selection;
            
            if ('tr' == typechoTable.table._childTag) {
                typechoTable.dragStop = function (obj, result) {
                    var _r = new Request.JSON({
                        url: '<?php $options->index('Metas/Category/Edit.do'); ?>'
                    }).send(result + '&do=sort');
                };
            } else {
                typechoTable.checked = function (input, item) {
                    if (!_selection) {
                        _selection = document.createElement('div');
                        $(_selection).addClass('tag-selection');
                        $(_selection).addClass('clearfix');
                        $(document).getElement('.typecho-mini-panel form')
                        .insertBefore(_selection, $(document).getElement('.typecho-mini-panel form #typecho-option-item-name'));
                    }
                    
                    var _href = item.getElement('span').getProperty('rel');
                    var _text = item.getElement('span').get('text');
                    var _a = document.createElement('a');
                    $(_a).addClass('button');
                    $(_a).setProperty('href', _href);
                    $(_a).set('text', _text);
                    _selection.appendChild(_a);
                    item.checkedElement = _a;
                };
                
                typechoTable.unchecked = function (input, item) {
                    if (item.checkedElement) {
                        $(item.checkedElement).destroy();
                    }
                    
                    if (!$(_selection).getElement('a')) {
                        _selection.destroy();
                        _selection = null;
                    }
                };
            }
        });
    })();
</script>
<?php include 'copyright.php'; ?>
