<?php 
require_once 'common.php';
Typecho_API::factory('Widget_Contents_Page_Edit')->to($page);
require_once 'header.php';
require_once 'menu.php';

if($page->cid)
{
    $created = $page->created + $options->timezone;
}
else
{
    $created = $options->gmtTime + $options->timezone;
}
?>

	<div id="main" class="clearfix">
	<form method="post" action="<?php $options->index('/Contents/Page/Edit.do'); ?>" id="post" name="post">
        
        <div id="sidebar">
			<h3><?php _e('发布'); ?></h3>
			<div id="publishing">
				<p><label><?php _e('发布日期'); ?></label><input type="text" class="text" readonly="readonly" id="date" name="date" value="<?php echo date('Y-m-d', $created);?>" /></p>
				<p><label><?php _e('发布时间'); ?></label><input type="text" class="text" readonly="readonly" id="time" name="time" value="<?php echo date('g:i A', $created);?>" /></p>
			</div>

			<h3><?php _e('页面顺序'); ?></h3>
			<p><input type="text" id="meta" name="meta" style="width: 240px;" value="<?php echo $page->meta ? $page->meta : 0; ?>" /></p>

			<h3><?php _e('评论和引用'); ?></h3>
			<div id="allow_status">
                <ul id="permission_list">
				<li><input type="checkbox" id="allowComment" value="1" name="allowComment" <?php if($page->allow('comment') || (!$page->cid && $options->defaultAllowComment)): ?>checked="checked"<?php endif; ?> /><label for="allowComment"><?php _e('允许访问者对此文评论'); ?></label></li>
				<li><input type="checkbox" id="allowPing" value="1" name="allowPing" <?php if($page->allow('ping') || (!$page->cid && $options->defaultAllowPing)): ?>checked="checked"<?php endif; ?> /><label for="allowPing"><?php _e('允许其它网站向此文发送广播'); ?></label></li>
                </ul>
			</div>

			<h3><?php _e('密码保护'); ?></h3>
			<p><input type="text" name="password" id="password" style="width: 225px;" value="<?php $page->password(); ?>" /></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><?php _e('为这个页面分配一个密码，访问者需要输入密码才能阅读到日志内容。'); ?></p>

			<h3><?php _e('引用通告'); ?></h3>
			<p><textarea id="trackback" name="trackback" rows="5" cols="" style="width: 225px;"></textarea></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><?php _e('每行一个引用地址,用回车隔开'); ?></p>
		</div><!-- end #sidebar -->
        
		<div id="content">
            <h2><?php $menu->title(); ?></h2>
            
			<?php require_once 'notice.php'; ?>
            
			<h4><?php _e('标题'); ?></h4>
			<p><input id="title" type="text" name="title" onfocus="this.select();" value="<?php $page->title(); ?>" /></p>
			<h4><?php _e('内容'); ?></h4>
			<p><textarea id="text" name="text" style="height:300px" cols="40"><?php $page->text(); ?></textarea></p>
			<p style="text-align: right;" class="submit">
                <input type="submit" onclick="$('input[@name=draft]').val(1);" value="<?php _e('保存为草稿'); ?>" /> 
                <input type="submit" onclick="$('input[@name=draft]').val(1);$('input[@name=continue]').val(1);" value="<?php _e('保存并继续编辑'); ?>" /> 
                <input type="submit" value="<?php _e('发布'); ?>" />
                <input type="hidden" name="do" value="<?php echo ($page->cid ? 'update' : 'insert'); ?>" />
                <input type="hidden" name="cid" value="<?php $page->cid(); ?>" />
                <input type="hidden" name="draft" value="0" />
                <input type="hidden" name="continue" value="0" />
            </p>
			<h4><?php _e('缩略名(用于自定义链接)'); ?></h4>
			<p><input id="slug" type="text" name="slug" value="<?php $page->slug(); ?>" /></p>
		</div><!-- end #content -->

	</form>
	</div><!-- end #main -->
<script type="text/javascript" src="<?php $options->adminUrl('/js/jquery-ui-personalized-1.5.1.min.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('/js/jquery.clockpick.1.2.3.pack.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('/js/tiny_mce/tiny_mce.js'); ?>"></script>
<script type="text/javascript">
$("#date").datepicker({dateFormat: "yy-mm-dd"});
$("#time").clockpick({starthour: 0, endhour : 23});

tinyMCE.init({
mode : "exact",
elements : "text",
theme : "advanced",
skin : "o2k7",
plugins : "safari,inlinepopups",
theme_advanced_buttons1 : "bold,italic,underline,strikethrough, separator, forecolor,backcolor,fontselect,fontsizeselect",
theme_advanced_buttons2_add_before: "cut,copy,pastetext,separator",
theme_advanced_buttons2 : "undo,redo,separator,hr,link,unlink,image,flash,separator",
theme_advanced_buttons2_add :"code,emotions,charmap",
theme_advanced_buttons3 : "",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
relative_urls : false,
remove_script_host : false
});
</script>

<?php require_once 'footer.php'; ?>
