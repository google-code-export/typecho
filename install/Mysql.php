<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php if (defined('SAE_ACCESSKEY') && defined('SAE_SECRETKEY')): ?>
<?php //这里是专门为Sina App Engine做的判断 ?>
<li>
<label class="typecho-label"><?php _e('数据库地址'); ?></label>
<input type="text" class="text" name="dbHost" value="<?php _v('dbHost', 'm'.$_SERVER['HTTP_MYSQLPORT'].'.mysql.sae.sina.com.cn'); ?>" />
<p class="description"><?php _e('这里Sina App Engine自动分配的数据库地址，请保留默认设置'); ?></p>
</li>
<li>
<label class="typecho-label"><?php _e('数据库端口'); ?></label>
<input type="text" class="text" name="dbPort" value="<?php _v('dbPort', $_SERVER['HTTP_MYSQLPORT']); ?>" />
<p class="description"><?php _e('如果您不知道此选项的意义, 请保留默认设置'); ?></p>
</li>
<li>
<label class="typecho-label"><?php _e('数据库用户名'); ?></label>
<input type="text" class="text" name="dbUser" value="<?php _v('dbUser', SAE_ACCESSKEY); ?>" />
<p class="description"><?php _e('这里Sina App Engine自动分配的用户名，请保留默认设置'); ?></p>
</li>
<li>
<label class="typecho-label"><?php _e('数据库密码'); ?></label>
<input type="password" class="text" name="dbPassword" value="<?php _v('dbPassword', SAE_SECRETKEY); ?>" />
</li>
<li>
<label class="typecho-label"><?php _e('数据库名'); ?></label>
<input type="text" class="text" name="dbDatabase" value="<?php _v('dbDatabase', 'app_'.$_SERVER['HTTP_APPNAME']); ?>" />
<p class="description"><?php _e('请您指定数据库名称'); ?></p>
</li>
<?php //结束 ?>
<?php  else: ?>
<li>
<label class="typecho-label"><?php _e('数据库地址'); ?></label>
<input type="text" class="text" name="dbHost" value="<?php _v('dbHost', 'localhost'); ?>"/>
<p class="description"><?php _e('您可能会使用 "localhost"'); ?></p>
</li>
<li>
<label class="typecho-label"><?php _e('数据库端口'); ?></label>
<input type="text" class="text" name="dbPort" value="<?php _v('dbPort', '3306'); ?>"/>
<p class="description"><?php _e('如果您不知道此选项的意义, 请保留默认设置'); ?></p>
</li>
<li>
<label class="typecho-label"><?php _e('数据库用户名'); ?></label>
<input type="text" class="text" name="dbUser" value="<?php _v('dbUser', 'root'); ?>" />
<p class="description"><?php _e('您可能会使用 "root"'); ?></p>
</li>
<li>
<label class="typecho-label"><?php _e('数据库密码'); ?></label>
<input type="password" class="text" name="dbPassword" value="<?php _v('dbPassword'); ?>" />
</li>
<li>
<label class="typecho-label"><?php _e('数据库名'); ?></label>
<input type="text" class="text" name="dbDatabase" value="<?php _v('dbDatabase', 'typecho'); ?>" />
<p class="description"><?php _e('请您指定数据库名称'); ?></p>
</li>
<?php  endif; ?>


<input type="hidden" name="dbCharset" value="<?php _e('utf8'); ?>" />
