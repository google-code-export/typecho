<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<style>
.tag-list, .column-06 {
    overflow: visible;
    z-index: 0;
}
</style>

<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main manage-metas">
                <div class="column-18 start-01 tag-list">
                    <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud', 'sort=mid&desc=0')->to($tags); ?>
                    <h2><?php _e('标签列表'); ?></h2>
                    <div class="items">
                    <?php while($tags->next()): ?><span class="<?php $tags->split('size-1', 'size-2', 'size-3', 'size-4', 'size-5'); ?>"><?php $tags->name(); ?></span><?php endwhile; ?>
                    </div>
                </div>
                <div class="column-06 start-19">
                    <?php Typecho_Widget::widget('Widget_Metas_Category_List')->to($categories); ?>
                    <ul class="category-list">
                        <h2><?php _e('分类列表'); ?></h2>
                        <li><a href="#"><?php $categories->name(); ?></a></li>
                    </ul>
                </div>
        </div>
    </div>
</div>

<?php include 'common-js.php'; ?>
<script type="text/javascript">
    /** 拖拽 */
    $(document).getElements('.manage-metas .tag-list .items span').each(function (item) {
        var top = item.getTop();
        var left = item.getLeft();
        
        var typechoDrag = new Drag.Move(item, {
            onSnap: function(el){
                el.setStyle('position', 'absolute');
                el.addClass('move');
            },
            onComplete: function(el){
                el.removeClass('move');
                el.setStyle('top', '');
                el.setStyle('left', '');
                el.setStyle('position', '');
            }
        });
        item.setStyle('top', '');
        item.setStyle('left', '');
        item.setStyle('position', '');
        
        typechoDrag.addEvent('onBeforeStart', function () {
            this.element.setStyle('position', 'absolute');
            this.element.addClass('move');
            
            var _position = this.element.getCoordinates(this.element.getParent('.tag-list'));
            
            this.element.setStyle('top', _position.top);
            this.element.setStyle('left', _position.left);
        });
    });
    
    var tagListItems = $(document).getElement('.manage-metas .tag-list .items');
    tagListItems.setStyle('height', tagListItems.getStyle('height'));
</script>
<?php include 'copyright.php'; ?>
