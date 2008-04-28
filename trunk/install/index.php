<?php
header("content-Type: text/html; charset=UTF-8");
define('__VERSION__','Magike 1.2.0 Release');
define('__BUILD__','8.3.3');
define('__DIR__','.');
error_reporting(E_ALL);

function mgStripslashesDeep($value)
{
    return    is_array($value) ?
                array_map('mgStripslashesDeep', $value) :
                stripslashes($value);
}

//关闭魔术引号
if (get_magic_quotes_gpc()) 
{
	$_GET = mgStripslashesDeep($_GET);
	$_POST = mgStripslashesDeep($_POST);
	$_COOKIE = mgStripslashesDeep($_COOKIE);
	
	reset($_GET);
	reset($_POST);
	reset($_COOKIE);
}

function mgRmDir($path)
{
	$dirs = mgGetDir($path);
	
	if(false == mgUnLink($path)) 
	{
		return false;
	}
	
	foreach($dirs as $dir)
	{
	    $inpath = $path."/".$dir;
	    if(NULL == mgGetDir($inpath) && NULL == mgGetFile($inpath))
	    {
	    	if(false == rmdir($inpath))
    		{
    			return false;
    		}
	    }
	    else
	    {
	        if(false == mgRmDir($inpath))
	        {
    			return false;
    		}
	    }
	}
	
	return rmdir($path);
}

function mgTouchAll($inpath)
{
	$dir = explode("/",$inpath);
	
	foreach($dir as $key => $val)
	{
		$path = implode("/",$dir);
		if(false == mgTouch($path)) 
		{
			return false;
		}
		if(NULL != ($dirs = mgGetDir($path)))
		{
			foreach($dirs as $inkey => $inval)
			{
				if(mgTouchAll($path."/".$inval) == false) return false;
			}
		}
		if($inpath != $path)
		{
			array_pop($dir);
		}
		else break;
	}
	
	return true;
}

function mgTouch($inpath)
{
	$files = mgGetFile($inpath,true);
	
	if(NULL != $files)
	{
		foreach($files as $key => $val)
		{
		if(@touch($inpath."/".$val) == false) return false;
		}
	}
	
	return true;
}

function mgUnLink($inpath)
{
    if(is_file($inpath))
    {
        return unlink($inpath);
    }
    else if(is_dir($inpath))
    {
    	$files = mgGetFile($inpath,true);

		foreach($files as $file)
		{
		    if(false == unlink($inpath."/".$file)) 
		    {
		        return false;
		    }
		}
		
		return true;
    }
	else
	{
	    return false;
	}
}


//获取一个目录下的文件
function mgGetFile($inpath,$trim = false,$stamp = NULL)
{
	$file = array();

	if(!is_dir($inpath))
	{
		return $file;
	}

	$handle=opendir($inpath);
	if(NULL != $stamp)
	{
		$stamp = explode("|",$stamp);
	}

	while ($tmp = readdir($handle)) 
	{
		if(is_file($inpath."/".$tmp))
		{
		    $items = explode('.',$tmp);
		    $ext = array_pop($items);
		    
		    $filName = array(
		        $tmp,
		        implode('.',$items),
		        $ext
		    );
		    
			if($stamp != NULL && in_array($filName[2],$stamp))
			{
				$file[] = $trim ? $filName[0] : $filName[1];
			}
			else if($stamp == NULL)
			{
				$file[] = $trim ? $filName[0] : $filName[1];
			}
		}
	}
	closedir($handle);
	return $file;
}

//获取一个目录下的目录
function mgGetDir($inpath)
{
	$dir = array();
	
	if(!is_dir($inpath))
	{
		return $dir;
	}
	
	$handle=opendir($inpath);
	while ($tmp = readdir($handle))
	{
		if(is_dir($inpath."/".$tmp) && $tmp != ".." && $tmp != ".") 
		{
			$dir[] = $tmp;
		}
	}
	closedir($handle);
	return $dir;
}

function query($str,$error = true)
{
	$rows = explode(";",$str);
	
	foreach($rows as $row)
	{
		$row = trim($row);
		if($row)
		{
		    if($error)
		    {
			    $result = mysql_query($row) or die(mysql_error());
		    }
		    else
		    {
		        $result = mysql_query($row);
		    }
		}
	}
	
	return $result;
}

function checkMagikeVersion()
{
    $result = query("SELECT * FROM ".__DBPREFIX__."statics",false);
    $statics = array();
    if($result)
    {
        while($row = mysql_fetch_assoc($result))
        {
            $statics[$row['static_name']] = str_replace("'","''",$row['static_value']);
        }
    }
    else
    {
        return true;
    }
    
    if(!empty($statics['build_version']))
    {
        if(version_compare($statics['build_version'],'7.10.19','>'))
        {
            return true;
        }
    }
    
    return false;
}

function checkMagikeTables()
{
    $result = query("SHOW TABLES",false);
    $tables = array();
    
    if($result)
    {
        while($row = mysql_fetch_assoc($result))
        {
            $tables[] = array_pop($row);
        }
    }
    
    return in_array(__DBPREFIX__."statics",$tables);
}

function install_get_siteurl()
{
	$url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];	
	if(isset($_SERVER["QUERY_STRING"]))
	{
		$url = str_replace("?".$_SERVER["QUERY_STRING"],"",$url);
	}
	
	return dirname($url);
}

function upgrade()
{
$siteurl = install_get_siteurl();
query("SET NAMES 'utf8'");

//删除无用表
query("DROP TABLE IF EXISTS `".__DBPREFIX__."user_group_mapping` ,
`".__DBPREFIX__."groups` , `".__DBPREFIX__."path_group_mapping`");

//改变数据结构
query(
"ALTER TABLE `".__DBPREFIX__."categories` CHANGE `id` `category_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."comments` CHANGE `id` `comment_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."comments` CHANGE `comment_homepage` `comment_homepage` varchar(128) default NULL;
ALTER TABLE `".__DBPREFIX__."comments` ADD `comment_parent` INT( 10 ) UNSIGNED NULL DEFAULT '0';
ALTER TABLE `".__DBPREFIX__."comment_filters` CHANGE `id` `comment_filter_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."files` CHANGE `id` `file_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."links` CHANGE `id` `link_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."link_categories` CHANGE `id` `link_category_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."menus` CHANGE `id` `menu_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."paths` CHANGE `id` `path_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."posts` CHANGE `id` `post_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."posts` ADD `post_template` VARCHAR( 32 ) NULL AFTER `post_user_name`;
ALTER TABLE `".__DBPREFIX__."posts` CHANGE `post_name` `post_name` VARCHAR( 128 ) NULL DEFAULT NULL;
ALTER TABLE `".__DBPREFIX__."post_tag_mapping` CHANGE `id` `post_tag_mapping_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."statics` CHANGE `id` `static_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."tags` CHANGE `id` `tag_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."users` CHANGE `id` `user_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `".__DBPREFIX__."categories` CHANGE `category_count` `category_count` INT( 10 ) UNSIGNED DEFAULT '0';
ALTER TABLE `".__DBPREFIX__."categories` DROP INDEX `comment_filter_type`;
ALTER TABLE `".__DBPREFIX__."comments` DROP INDEX `user_id`;
ALTER TABLE `".__DBPREFIX__."comments` ADD INDEX `post_id` ( `post_id` );
ALTER TABLE `".__DBPREFIX__."comments` ADD INDEX `comment_date` ( `comment_date` );
ALTER TABLE `".__DBPREFIX__."comments` ADD INDEX `user_id` int(10) unsigned default '0';
ALTER TABLE `".__DBPREFIX__."links` CHANGE `link_category_id` `link_category_id` int(10) unsigned default '0';
ALTER TABLE `".__DBPREFIX__."comments` ADD `user_id` int(10) unsigned default '0';
ALTER TABLE `".__DBPREFIX__."tags` CHANGE `tag_count` `tag_count` int(10) unsigned default '0';
ALTER TABLE `".__DBPREFIX__."users` ADD `user_post_num` int(10) unsigned default '0';
ALTER TABLE `".__DBPREFIX__."users` ADD `user_comment_num` int(10) unsigned default '0';
ALTER TABLE `".__DBPREFIX__."users` ADD `user_group` tinyint(4) default '0';
ALTER TABLE `".__DBPREFIX__."users` ADD FULLTEXT `user_name` ( `user_name` );
ALTER TABLE `".__DBPREFIX__."users` ADD UNIQUE `user_mail` ( `user_mail` );
ALTER TABLE `".__DBPREFIX__."users` DROP INDEX `user_name_2`;
ALTER TABLE `".__DBPREFIX__."users` DROP INDEX `user_name_3`;
ALTER TABLE `".__DBPREFIX__."comments` CHANGE `comment_publish` `comment_publish` ENUM( 'approved', 'spam', 'waiting' ) DEFAULT 'approved';
",false);

query("REPLACE INTO `".__DBPREFIX__."paths` (`path_id`, `path_name`, `path_action`, `path_file`, `path_cache`, `path_meta`, `path_group`) VALUES 
(12, '/admin/logout/', 'module_output', 'admin_logout', 0, 'logout', 4),
(22, '/admin/panel/dashboard/', 'template', '/{\$static_var.admin_template}/index.tpl', 0, 'admin_dashboard', 3),
(23, '/admin/panel/plugins/', 'template', '/{\$static_var.admin_template}/plugins.tpl', 0, 'admin_plugins', 0),
(41, '/admin/posts/file_api/', 'template', '/{\$static_var.admin_template}/file_api.tpl', 0, 'file_api', 2),
(123, '/[post_year=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'yearly_archives', 4),
(124, '/[post_year=%d]/[post_month=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'monthly_archives', 4),
(125, '/[post_year=%d]/[post_month=%d]/[post_day=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'daily_archives', 4),
(126, '/[post_name=%s]/', 'template', '/{\$static_var.template}/page.tpl', 0, 'pages', 4),
(127, '/post_comment/[post_id=%d]/', 'template', '/{\$static_var.template}/post_comment.tpl', 0, 'post_comment', 4),
(128, '/rss/archives/[post_id=%d]/', 'template', '/{\$static_var.xml_template}/rss_archives.tpl', 0, 'archives_rss', 4),
(133, '/category/[category_postname=%s]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'category', 4),
(134, '/category/[category_postname=%s]/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'category_page', 4),
(138, '/[post_year=%d]/page/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'yearly_archives_page', 4),
(139, '/[post_year=%d]/[post_month=%d]/page/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'monthly_archives_page', 4),
(140, '/[post_year=%d]/[post_month=%d]/[post_day=%d]/page/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'daily_archives_page', 4),
(141, '/rss/category/[category_postname=%s]/', 'template', '/{\$static_var.xml_template}/rss_category.tpl', 0, 'category_rss', 4),
(152, '/register/', 'template', '/{\$static_var.admin_template}/register.tpl', 0, 'register', 4),
(154, '/wlwmanifest.api', 'template', '/{\$static_var.xml_template}/wlwmanifest.tpl', 0, 'wlwmanifest', 4),
(155, '/rss/comments/', 'template', '/{\$static_var.xml_template}/rss_all_comments.tpl', 0, 'comments_rss', 4);

REPLACE INTO `".__DBPREFIX__."menus` (`menu_id`, `menu_name`, `path_id`, `menu_parent`) VALUES 
(1, 'General', 21, 0),
(2, 'Dashboard', 22, 1),
(3, 'Plugins', 23, 1),

(11, 'Contents', 31, 0),
(12, 'Write', 32, 11),
(13, 'Posts', 33, 11),
(14, 'Categories', 35, 11),
(15, 'Add Category', 36, 11),
(16, 'Files', 37, 11),

(21, 'Feedback', 51, 0),
(22, 'Comments', 52, 21),
(23, 'Comment Filters', 53, 21),
(24, 'Add Comment Filter', 54, 21),

(31, 'Blogroll', 61, 0),
(32, 'Links', 62, 31),
(33, 'Add Link', 63, 31),
(34, 'Link Categories', 64, 31),
(35, 'Add Link Category', 65, 31),

(41, 'Members', 71, 0),
(42, 'Profile', 72, 41),
(43, 'Users', 73, 41),
(44, 'Add User', 74, 41),

(51, 'Presentation', 81, 0),
(52, 'Skins', 82, 51),
(53, 'Skin Editor', 83, 51),

(61, 'Settings', 91, 0),
(62, 'Basic', 92, 61),
(63, 'Posts', 93, 61),
(64, 'Writing', 94, 61),
(65, 'Comments', 95, 61),
(66, 'Privileges', 96, 61),
(67, 'Mail', 97, 61),
(68, 'Permalink', 98, 61);");

$result = query("SELECT * FROM ".__DBPREFIX__."statics");
$statics = array();
while($row = mysql_fetch_assoc($result))
{
    $statics[$row['static_name']] = str_replace("'","''",$row['static_value']);
}

query("
-- 
-- 导出表中的数据 `".__DBPREFIX__."statics`
-- 

REPLACE INTO `".__DBPREFIX__."statics` (`static_id`, `static_name`, `static_value`) VALUES 
(1, 'template', '".$statics['template']."'),
(2, 'admin_template', 'admin'),
(3, 'xml_template', 'xml'),
(4, 'siteurl', '{$siteurl}'),
(5, 'version', '".__VERSION__."'),
(6, 'description', '".(isset($statics['description']) ? $statics['description'] : $statics['describe'])."'),
(7, 'blog_name', '{$statics['blog_name']}'),
(8, 'language', '{$statics['language']}'),
(9, 'charset', '{$statics['charset']}'),
(10, 'content_type', '{$statics['content_type']}'),
(11, 'index', '".((false === strpos($statics['index'],'/index.php')) ? $siteurl : $siteurl.'/index.php')."'),
(12, 'visitor_group', '".(isset($statics['visitor_group']) ? $statics['visitor_group'] : 2)."'),
(13, 'comment_date_format', '{$statics['comment_date_format']}'),
(14, 'time_zone', '{$statics['time_zone']}'),
(15, 'post_sub', '{$statics['post_sub']}'),
(16, 'post_page_num', '{$statics['post_page_num']}'),
(17, 'post_date_format', '{$statics['post_date_format']}'),
(18, 'count_posts', '".(isset($statics['count_posts']) ? $statics['count_posts'] : 0)."'),
(19, 'count_comments', '".(isset($statics['count_comments']) ? $statics['count_comments'] : 0)."'),
(20, 'keywords', '".(isset($statics['keywords']) ? $statics['keywords'] : 'Magike')."'),
(21, 'post_list_num', '{$statics['post_list_num']}'),
(22, 'comment_list_num', '{$statics['comment_list_num']}'),
(23, 'comment_email', '{$statics['comment_email']}'),
(24, 'write_editor_rows', '{$statics['write_editor_rows']}'),
(25, 'write_default_name', '{$statics['write_default_name']}'),
(26, 'write_default_category', '".(isset($statics['write_default_category']) ? $statics['write_default_category'] : 1)."'),
(27, 'write_auto_save', '".(isset($statics['write_auto_save']) ? $statics['write_auto_save'] : 0)."'),
(28, 'comment_check', '{$statics['comment_check']}'),
(29, 'user_allow_register', '{$statics['user_allow_register']}'),
(30, 'user_register_group', '3'),
(31, 'comment_email_notnull', '{$statics['comment_email_notnull']}'),
(32, 'comment_homepage_notnull', '{$statics['comment_homepage_notnull']}'),
(33, 'referer_denny', '{$statics['referer_denny']}'),
(34, 'smtp_host', '{$statics['smtp_host']}'),
(35, 'smtp_port', '{$statics['smtp_port']}'),
(36, 'smtp_user', '{$statics['smtp_user']}'),
(37, 'smtp_pass', '{$statics['smtp_pass']}'),
(38, 'smtp_auth', '{$statics['smtp_auth']}'),
(39, 'smtp_ssl', '{$statics['smtp_ssl']}'),
(40, 'default_allow_comment', '".(isset($statics['default_allow_comment']) ? $statics['default_allow_comment'] : 1)."'),
(41, 'default_allow_ping', '".(isset($statics['default_allow_ping']) ? $statics['default_allow_ping'] : 1)."'),
(42, 'write_editor_custom_tags', '".(isset($statics['write_editor_custom_tags']) ? $statics['write_editor_custom_tags'] : NULL)."'),
(43, 'build_version', '".__BUILD__."'),
(44, 'permalink_style', '".(isset($statics['permalink_style']) ? $statics['permalink_style'] : 'permalink.default.map')."'),
(45, 'callback','".(isset($statics['callback']) ? $statics['callback'] : 'code_tag')."'),
(46, 'feed_full_text','".(isset($statics['feed_full_text']) ? $statics['feed_full_text'] : 0)."'),
(47, 'register_notify','".(isset($statics['register_notify']) ? $statics['register_notify'] : 0)."')
");

//更新缓存项
$result = query("SELECT COUNT(post_id) AS num FROM `".__DBPREFIX__."posts`");
$rows = mysql_fetch_assoc($result);
query("UPDATE `".__DBPREFIX__."statics` SET static_value = '{$rows['num']}' WHERE static_name = 'count_posts'");

$result = query("SELECT COUNT(comment_id) AS num FROM `".__DBPREFIX__."comments` WHERE comment_publish = 'approved'");
$rows = mysql_fetch_assoc($result);
query("UPDATE `".__DBPREFIX__."statics` SET static_value = '{$rows['num']}' WHERE static_name = 'count_comments'");

$res = query("SELECT * FROM `".__DBPREFIX__."categories`");
while($row = mysql_fetch_assoc($res))
{
    $result = query("SELECT COUNT(post_id) AS num FROM `".__DBPREFIX__."posts` 
    WHERE category_id = {$row['category_id']} AND post_is_draft = 0 AND post_is_hidden = 0 AND post_is_page = 0");
    $rows = mysql_fetch_assoc($result);
    query("UPDATE `".__DBPREFIX__."categories` SET category_count = '{$rows['num']}' WHERE category_id = {$row['category_id']}");
}

$res = query("SELECT * FROM `".__DBPREFIX__."users`");
while($row = mysql_fetch_assoc($res))
{
    $result = query("SELECT COUNT(post_id) AS num FROM `".__DBPREFIX__."posts` 
    WHERE user_id = {$row['user_id']}");
    $rows = mysql_fetch_assoc($result);
    query("UPDATE `".__DBPREFIX__."users` SET user_post_num = '{$rows['num']}' WHERE user_id = {$row['user_id']}");
    
    $result = query("SELECT COUNT(comment_id) AS num FROM `".__DBPREFIX__."comments` 
    WHERE comment_user = '{$row['user_name']}'");
    $rows = mysql_fetch_assoc($result);
    query("UPDATE `".__DBPREFIX__."users` SET user_comment_num = '{$rows['num']}' WHERE user_id = {$row['user_id']}");
}

$res = query("SELECT * FROM `".__DBPREFIX__."posts`");
while($row = mysql_fetch_assoc($res))
{
	$url = NULL;
	//更正没有slug的文章
	if('' == $row['post_name'])
	{
		$url = " , post_name = '".urlencode($row['post_title']).'-'.$row['post_id']."'";
	}
    $result = query("SELECT COUNT(comment_id) AS num FROM `".__DBPREFIX__."comments` 
    WHERE post_id = {$row['post_id']} AND comment_publish = 'approved'");
    $rows = mysql_fetch_assoc($result);
    query("UPDATE `".__DBPREFIX__."posts` SET post_comment_num = '{$rows['num']}'{$url} WHERE post_id = {$row['post_id']}");
}

return true;
}

function install()
{
query("SET NAMES 'utf8'");

$siteurl = install_get_siteurl();
query("-- 
-- 表的结构 `".__DBPREFIX__."categories`
-- @id:分类的索引
-- @category_name:分类名称
-- @category_describe:分类描述
-- @category_sort:分类排序
-- @category_count:分类文章数目

-- 索引
-- id:主键,自增
-- category_sort:用于排序的索引,16位字符
-- category_postname:用于url寻址的索引

-- 信息
-- 字符集:utf8
-- 数据引擎:MyISAM
-- 

CREATE TABLE `".__DBPREFIX__."categories` (
  `category_id` int(10) unsigned NOT NULL auto_increment,
  `category_name` varchar(100) default NULL,
  `category_postname` varchar(100) default NULL,
  `category_describe` varchar(200) default NULL,
  `category_sort` int(10) unsigned default '0',
  `category_count` int(10) unsigned default '0',
  PRIMARY KEY  (`category_id`),
  KEY `category_sort` (`category_sort`),
  KEY `category_postname` (`category_postname`(16))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `".__DBPREFIX__."categories`
-- 

INSERT INTO `".__DBPREFIX__."categories` (`category_id`, `category_name`, `category_postname`, `category_describe`, `category_sort`, `category_count`) VALUES 
(1, '默认分类', 'default', '这是一个默认分类', 1, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."comment_filters`
-- 

CREATE TABLE `".__DBPREFIX__."comment_filters` (
  `comment_filter_id` int(10) unsigned NOT NULL auto_increment,
  `comment_filter_name` varchar(200) default NULL,
  `comment_filter_value` text,
  PRIMARY KEY  (`comment_filter_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."comments`
-- 

CREATE TABLE `".__DBPREFIX__."comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `comment_user` varchar(64) default NULL,
  `comment_date` int(11) unsigned default '0',
  `comment_email` varchar(64) default NULL,
  `comment_homepage` varchar(128) default NULL,
  `comment_agent` varchar(200) default NULL,
  `comment_ip` varchar(64) default '0.0.0.0',
  `comment_text` text,
  `comment_title` varchar(64) default NULL,
  `post_id` int(10) unsigned default '0',
  `user_id` int(10) unsigned default '0',
  `comment_type` enum('comment','pingback','trackback') default 'comment',
  `comment_publish` enum('approved','spam','waiting') default 'approved',
  `comment_parent` int(10) unsigned default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `post_id` (`post_id`),
  KEY `comment_date` (`comment_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `".__DBPREFIX__."comments`
-- 

INSERT INTO `".__DBPREFIX__."comments` (`comment_id`, `comment_user`, `comment_date`, `comment_email`, `comment_homepage`, `comment_agent`, `comment_ip`, `comment_text`, `comment_title`, `post_id`, `comment_type`, `comment_publish`, `comment_parent`) VALUES 
(1, 'magike', 1172842951, 'magike.net@gmail.com', 'http://www.magike.net', 'Magike/1.1.0', '127.0.0.1', '欢迎您选择Magike', NULL, 1, 'comment', 'approved', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."files`
-- 

CREATE TABLE `".__DBPREFIX__."files` (
  `file_id` int(10) NOT NULL auto_increment,
  `file_name` varchar(200) default NULL,
  `file_guid` varchar(32) default NULL,
  `file_type` varchar(16) default NULL,
  `file_size` varchar(20) default NULL,
  `file_time` int(11) default '0',
  `file_describe` varchar(200) default NULL,
  PRIMARY KEY  (`file_id`),
  KEY `file_time` (`file_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."link_categories`
-- 

CREATE TABLE `".__DBPREFIX__."link_categories` (
  `link_category_id` int(10) unsigned NOT NULL auto_increment,
  `link_category_name` varchar(100) default NULL,
  `link_category_describe` varchar(100) default NULL,
  `link_category_hide` tinyint(1) default '0',
  `link_category_sort` int(10) default '0',
  `link_category_linksort` enum('asc','desc','rand') default 'asc',
  PRIMARY KEY  (`link_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `".__DBPREFIX__."link_categories`
-- 

INSERT INTO `".__DBPREFIX__."link_categories` (`link_category_id`, `link_category_name`, `link_category_describe`, `link_category_hide`, `link_category_sort`, `link_category_linksort`) VALUES 
(1, 'blogroll', 'blogroll', 0, 1, 'rand');

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."links`
-- 

CREATE TABLE `".__DBPREFIX__."links` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `link_name` varchar(100) default NULL,
  `link_describe` varchar(200) default NULL,
  `link_url` varchar(100) default NULL,
  `link_image` varchar(100) default NULL,
  `link_category_id` int(10) unsigned default '0',
  PRIMARY KEY  (`link_id`),
  KEY `link_category_id` (`link_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `".__DBPREFIX__."links`
-- 

INSERT INTO `".__DBPREFIX__."links` (`link_id`, `link_name`, `link_describe`, `link_url`, `link_image`, `link_category_id`) VALUES 
(1, 'Magike', 'Magike官方站点', 'http://www.magike.net', '', 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."menus`
-- 

CREATE TABLE `".__DBPREFIX__."menus` (
  `menu_id` int(10) unsigned NOT NULL auto_increment,
  `menu_name` varchar(200) default NULL,
  `path_id` int(10) unsigned default '0',
  `menu_parent` int(10) unsigned default '0',
  PRIMARY KEY  (`menu_id`),
  KEY `path_id` (`path_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=500;

-- 
-- 导出表中的数据 `".__DBPREFIX__."menus`
-- 

INSERT INTO `".__DBPREFIX__."menus` (`menu_id`, `menu_name`, `path_id`, `menu_parent`) VALUES 
(1, 'General', 21, 0),
(2, 'Dashboard', 22, 1),
(3, 'Plugins', 23, 1),

(11, 'Contents', 31, 0),
(12, 'Write', 32, 11),
(13, 'Posts', 33, 11),
(14, 'Categories', 35, 11),
(15, 'Add Category', 36, 11),
(16, 'Files', 37, 11),

(21, 'Comments', 51, 0),
(22, 'Comments', 52, 21),
(23, 'Comment Filters', 53, 21),
(24, 'Add Comment Filter', 54, 21),

(31, 'Links', 61, 0),
(32, 'Links', 62, 31),
(33, 'Add Link', 63, 31),
(34, 'Link Categories', 64, 31),
(35, 'Add Link Category', 65, 31),

(41, 'Users', 71, 0),
(42, 'Porfile', 72, 41),
(43, 'Users', 73, 41),
(44, 'Add User', 74, 41),

(51, 'Presentation', 81, 0),
(52, 'Skins', 82, 51),
(53, 'Skin Editor', 83, 51),

(61, 'Settings', 91, 0),
(62, 'Basic', 92, 61),
(63, 'Posts', 93, 61),
(64, 'Writing', 94, 61),
(65, 'Comments', 95, 61),
(66, 'Permissions', 96, 61),
(67, 'Mail', 97, 61),
(68, 'Permalink', 98, 61);

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."paths`
-- 

CREATE TABLE `".__DBPREFIX__."paths` (
  `path_id` int(10) unsigned NOT NULL auto_increment,
  `path_name` varchar(128) default NULL,
  `path_action` varchar(20) default NULL,
  `path_file` varchar(64) default NULL,
  `path_cache` int(11) default '0',
  `path_meta` varchar(32) default NULL,
  `path_group` tinyint(4) default '3',
  PRIMARY KEY  (`path_id`),
  UNIQUE KEY `pt_name` (`path_name`),
  UNIQUE KEY `path_meta` (`path_meta`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=500;

-- 
-- 导出表中的数据 `".__DBPREFIX__."paths`
-- 

INSERT INTO `".__DBPREFIX__."paths` (`path_id`, `path_name`, `path_action`, `path_file`, `path_cache`, `path_meta`, `path_group`) VALUES 
-- 必要路径
(1, '/', 'template', '/{\$static_var.template}/index.tpl', 0, 'index', 4),
(2, '/.exception', 'template', '/{\$static_var.template}/exception.tpl', 0, 'exception', 4),
(3, '/validator/', 'json_output', 'validator', 0, 'validator', 4),

-- 后台入口
(11, '/admin/login/', 'template', '/{\$static_var.admin_template}/login.tpl', 0, 'login', 4),
(12, '/admin/logout/', 'module_output', 'admin_logout', 0, 'logout', 4),
(13, '/admin/', 'template', '/{\$static_var.admin_template}/index.tpl', 0, 'admin', 3),

-- 后台主面板
(21, '/admin/panel/', 'template', '/{\$static_var.admin_template}/index.tpl', 0, 'admin_panel', 3),
(22, '/admin/panel/dashboard/', 'template', '/{\$static_var.admin_template}/index.tpl', 0, 'admin_dashboard', 3),
(23, '/admin/panel/plugins/', 'template', '/{\$static_var.admin_template}/plugins.tpl', 0, 'admin_plugins', 0),

-- 后台内容管理
(31, '/admin/posts/', 'template', '/{\$static_var.admin_template}/write.tpl', 0, 'admin_posts', 2),
(32, '/admin/posts/write/', 'template', '/{\$static_var.admin_template}/write.tpl', 0, 'admin_write', 2),
(33, '/admin/posts/all/', 'template', '/{\$static_var.admin_template}/posts_list.tpl', 0, 'admin_posts_list', 2),
(34, '/admin/posts/all/search/', 'template', '/{\$static_var.admin_template}/posts_search_list.tpl', 0, 'admin_search_posts', 2),
(35, '/admin/posts/categories_list/', 'template', '/{\$static_var.admin_template}/categories_list.tpl', 0, 'admin_categories', 0),
(36, '/admin/posts/category/', 'template', '/{\$static_var.admin_template}/add_category.tpl', 0, 'admin_set_category', 0),
(37, '/admin/posts/files_list/', 'template', '/{\$static_var.admin_template}/files_list.tpl', 0, 'admin_files_list', 1),
(38, '/admin/posts/upload/', 'template', '/{\$static_var.admin_template}/upload.tpl', 0, 'admin_upload_files', 2),
(39, '/admin/posts/auto_save/', 'json_output', 'post_input', 0, 'admin_write_auto_save', 2),
(40, '/admin/posts/tags_search/', 'json_output', 'tags_search', 0, 'admin_tags_search', 2),
(41, '/admin/posts/file_api/', 'template', '/{\$static_var.admin_template}/file_api.tpl', 0, 'file_api', 2),

-- 后台评论管理
(51, '/admin/comments/', 'template', '/{\$static_var.admin_template}/comments.tpl', 0, 'admin_comments', 2),
(52, '/admin/comments/all/', 'template', '/{\$static_var.admin_template}/comments.tpl', 0, 'admin_comments_list', 2),
(53, '/admin/comments/filters_list/', 'template', '/{\$static_var.admin_template}/comment_filters.tpl', 0, 'admin_filters', 0),
(54, '/admin/comments/filter/', 'template', '/{\$static_var.admin_template}/add_comment_filter.tpl', 0, 'admin_set_filter', 0),

-- 后台链接管理
(61, '/admin/links/', 'template', '/{\$static_var.admin_template}/links.tpl', 0, 'admin_links', 0),
(62, '/admin/links/link_list/', 'template', '/{\$static_var.admin_template}/links.tpl', 0, 'admin_links_list', 0),
(63, '/admin/links/link/', 'template', '/{\$static_var.admin_template}/add_link.tpl', 0, 'admin_set_link', 0),
(64, '/admin/links/link_categories_list/', 'template', '/{\$static_var.admin_template}/link_categories_list.tpl', 0, 'admin_link_categories', 0),
(65, '/admin/links/link_category/', 'template', '/{\$static_var.admin_template}/add_link_category.tpl', 0, 'admin_set_link_category', 0),

-- 后台用户管理
(71, '/admin/users/', 'template', '/{\$static_var.admin_template}/my_profile.tpl', 0, 'admin_users', 3),
(72, '/admin/users/my_profile/', 'template', '/{\$static_var.admin_template}/my_profile.tpl', 0, 'admin_profile', 3),
(73, '/admin/users/users_list/', 'template', '/{\$static_var.admin_template}/users.tpl', 0, 'admin_users_list', 0),
(74, '/admin/users/user/', 'template', '/{\$static_var.admin_template}/add_user.tpl', 0, 'admin_set_user', 0),

-- 后台外观管理
(81, '/admin/skins/', 'template', '/{\$static_var.admin_template}/skins.tpl', 0, 'admin_sinks', 0),
(82, '/admin/skins/skins_list/', 'template', '/{\$static_var.admin_template}/skins.tpl', 0, 'admin_skins_list', 0),
(83, '/admin/skins/skin/', 'template', '/{\$static_var.admin_template}/add_skin.tpl', 0, 'admin_set_skin', 0),

-- 后台设置
(91, '/admin/settings/', 'template', '/{\$static_var.admin_template}/setting_public.tpl', 0, 'admin_setting', 0),
(92, '/admin/settings/setting_public/', 'template', '/{\$static_var.admin_template}/setting_public.tpl', 0, 'admin_setting_public', 0),
(93, '/admin/settings/setting_post/', 'template', '/{\$static_var.admin_template}/setting_post.tpl', 0, 'admin_setting_post', 0),
(94, '/admin/settings/setting_write/', 'template', '/{\$static_var.admin_template}/setting_write.tpl', 0, 'admin_setting_write', 0),
(95, '/admin/settings/setting_comment/', 'template', '/{\$static_var.admin_template}/setting_comment.tpl', 0, 'admin_setting_comment', 0),
(96, '/admin/settings/setting_user/', 'template', '/{\$static_var.admin_template}/setting_user.tpl', 0, 'admin_setting_user', 0),
(97, '/admin/settings/setting_mail/', 'template', '/{\$static_var.admin_template}/setting_mail.tpl', 0, 'admin_setting_mail', 0),
(98, '/admin/settings/setting_permalink/', 'template', '/{\$static_var.admin_template}/setting_permalink.tpl', 0, 'admin_setting_permalink', 0),

(111, '/view_thumb/[file_id=%d]/[file_name=%p]', 'module_output', 'thumbnail_output?width=590&height=390', 0, 'view_thumbnail', 4),
(112, '/res/[file_id=%d]/[file_name=%p]', 'module_output', 'file_output', 0, 'file_output', 4),
(113, '/thumb/[file_id=%d]/[file_name=%p]', 'module_output', 'thumbnail_output', 0, 'thumbnail', 4),

(121, '/archives/[post_id=%d]/', 'template', '/{\$static_var.template}/archive.tpl', 0, 'archives', 4),
(122, '/archives/[post_id=%d]/trackback/', 'template', '/{\$static_var.xml_template}/trackback.tpl', 0, 'trackbacks', 4),
(123, '/[post_year=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'yearly_archives', 4),
(124, '/[post_year=%d]/[post_month=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'monthly_archives', 4),
(125, '/[post_year=%d]/[post_month=%d]/[post_day=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'daily_archives', 4),
(126, '/[post_name=%s]/', 'template', '/{\$static_var.template}/page.tpl', 0, 'pages', 4),
(127, '/post_comment/[post_id=%d]/', 'template', '/{\$static_var.template}/post_comment.tpl', 0, 'post_comment', 4),
(128, '/rss/archives/[post_id=%d]/', 'template', '/{\$static_var.xml_template}/rss_archives.tpl', 0, 'archives_rss', 4),

(131, '/page/[page=%d]/', 'template', '/{\$static_var.template}/index.tpl', 0, 'archives_page', 4),
(132, '/tags/', 'template', '/{\$static_var.template}/tags.tpl', 0, 'tags', 4),
(133, '/category/[category_postname=%s]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'category', 4),
(134, '/category/[category_postname=%s]/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'category_page', 4),
(135, '/tags/[tag_name=%s]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'tag_archives', 4),
(136, '/tags/[tag_name=%s]/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'tag_archives_page', 4),
(137, '/search/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'search_archives', 4),
(138, '/[post_year=%d]/page/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'yearly_archives_page', 4),
(139, '/[post_year=%d]/[post_month=%d]/page/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'monthly_archives_page', 4),
(140, '/[post_year=%d]/[post_month=%d]/[post_day=%d]/page/[page=%d]/', 'template', '/{\$static_var.template}/posts.tpl', 0, 'daily_archives_page', 4),
(141, '/rss/category/[category_postname=%s]/', 'template', '/{\$static_var.xml_template}/rss_category.tpl', 0, 'category_rss', 4),

(151, '/rss/', 'template', '/{\$static_var.xml_template}/rss_all_posts.tpl', 0, 'rss', 4),
(152, '/register/', 'template', '/{\$static_var.admin_template}/register.tpl', 0, 'register', 4),
(153, '/xmlrpc.api', 'module_output', 'xmlrpc_post', 0, 'xmlrpc', 4),
(154, '/wlwmanifest.api', 'template', '/{\$static_var.xml_template}/wlwmanifest.tpl', 0, 'wlwmanifest', 4),
(155, '/rss/comments/', 'template', '/{\$static_var.xml_template}/rss_all_comments.tpl', 0, 'comments_rss', 4);

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."post_tag_mapping`
-- 

CREATE TABLE `".__DBPREFIX__."post_tag_mapping` (
  `post_tag_mapping_id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned default '0',
  `tag_id` int(10) unsigned default '0',
  PRIMARY KEY  (`post_tag_mapping_id`),
  KEY `post_id` (`post_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."posts`
-- 

CREATE TABLE `".__DBPREFIX__."posts` (
  `post_id` int(10) unsigned NOT NULL auto_increment,
  `post_title` varchar(200) default NULL,
  `post_name` varchar(128) default NULL,
  `post_time` int(11) unsigned default '0',
  `post_edit_time` int(11) unsigned default '0',
  `post_tags` varchar(200) default NULL,
  `post_password` varchar(20) default NULL,
  `post_content` text,
  `category_id` int(10) unsigned default '0',
  `user_id` int(10) unsigned default '0',
  `post_user_name` varchar(64) default NULL,
  `post_template` varchar(32) default NULL,
  `post_comment_num` int(10) unsigned default '0',
  `post_allow_ping` tinyint(1) default '1',
  `post_allow_comment` tinyint(1) default '1',
  `post_allow_feed` tinyint(1) default '1',
  `post_is_draft` tinyint(1) default '0',
  `post_is_hidden` tinyint(1) default '0',
  `post_is_page` tinyint(1) default '0',
  PRIMARY KEY  (`post_id`),
  KEY `category_id` (`category_id`),
  KEY `post_time` (`post_time`),
  KEY `post_name` (`post_name`(20)),
  KEY `post_tags` (`post_tags`(20))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `".__DBPREFIX__."posts`
-- 

INSERT INTO `".__DBPREFIX__."posts` (`post_id`, `post_title`, `post_name`, `post_time`, `post_edit_time`, `post_tags`, `post_password`, `post_content`, `category_id`, `user_id`, `post_user_name`, `post_comment_num`, `post_allow_ping`, `post_allow_comment`, `post_allow_feed`, `post_is_draft`, `post_is_hidden`, `post_is_page`) VALUES 
(1, '欢迎使用Magike', 'hello_world', 1172842951, 1187427077, '', '', '<p>如果您看到这篇文章,表示您的blog已经安装成功.</p>', 1, 1, 'admin', 1, 1, 1, 1, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."statics`
-- 

CREATE TABLE `".__DBPREFIX__."statics` (
  `static_id` int(10) unsigned NOT NULL auto_increment,
  `static_name` varchar(200) default NULL,
  `static_value` text,
  PRIMARY KEY  (`static_id`),
  UNIQUE KEY `st_name` (`static_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=500 ;

-- 
-- 导出表中的数据 `".__DBPREFIX__."statics`
-- 

INSERT INTO `".__DBPREFIX__."statics` (`static_id`, `static_name`, `static_value`) VALUES 
(1, 'template', 'default'),
(2, 'admin_template', 'admin'),
(3, 'xml_template', 'xml'),
(4, 'siteurl', '{$siteurl}'),
(5, 'version', '".__VERSION__."'),
(6, 'description', 'Just a blog'),
(7, 'blog_name', 'Magike Blog'),
(8, 'language', 'zh_cn_utf8'),
(9, 'charset', 'UTF-8'),
(10, 'content_type', 'text/html'),
(11, 'index', '{$siteurl}/index.php'),
(12, 'visitor_group', '2'),
(13, 'comment_date_format', 'M,jS,Y'),
(14, 'time_zone', '28800'),
(15, 'post_sub', '0'),
(16, 'post_page_num', '5'),
(17, 'post_date_format', 'Y年m月d日'),
(18, 'count_posts', '1'),
(19, 'count_comments', '1'),
(20, 'keywords', 'Magike'),
(21, 'post_list_num', '10'),
(22, 'comment_list_num', '10'),
(23, 'comment_email', '0'),
(24, 'write_editor_rows', '16'),
(25, 'write_default_name', 'nickname'),
(26, 'write_default_category', '1'),
(27, 'write_auto_save', '0'),
(28, 'comment_check', '0'),
(29, 'user_allow_register', '0'),
(30, 'user_register_group', '3'),
(31, 'comment_email_notnull', '1'),
(32, 'comment_homepage_notnull', '0'),
(33, 'referer_denny', '0'),
(34, 'smtp_host', ''),
(35, 'smtp_port', '25'),
(36, 'smtp_user', ''),
(37, 'smtp_pass', ''),
(38, 'smtp_auth', '1'),
(39, 'smtp_ssl', '1'),
(40, 'default_allow_comment', '1'),
(41, 'default_allow_ping', '1'),
(42, 'write_editor_custom_tags', ''),
(43, 'build_version', '".__BUILD__."'),
(44, 'permalink_style', 'permalink.default.map'),
(45, 'callback','code_tag'),
(46, 'feed_full_text','0'),
(47, 'register_notify','0');

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."tags`
-- 

CREATE TABLE `".__DBPREFIX__."tags` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `tag_name` varchar(32) default NULL,
  `tag_count` int(10) unsigned default '0',
  PRIMARY KEY  (`tag_id`),
  FULLTEXT KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- 表的结构 `".__DBPREFIX__."users`
-- 

CREATE TABLE `".__DBPREFIX__."users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(64) default NULL,
  `user_firstname` varchar(64) default NULL,
  `user_lastname` varchar(64) default NULL,
  `user_password` varchar(64) default NULL,
  `user_mail` varchar(64) default NULL,
  `user_url` varchar(64) default NULL,
  `user_nick` varchar(64) default NULL,
  `user_about` text,
  `user_post_num` int(10) unsigned default '0',
  `user_comment_num` int(10) unsigned default '0',
  `user_register` datetime default '0000-00-00 00:00:00',
  `user_group` tinyint(4) default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_mail` (`user_mail`),
  FULLTEXT KEY `user_name` (`user_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `".__DBPREFIX__."users`
-- 

INSERT INTO `".__DBPREFIX__."users` (`user_id`, `user_name`, `user_firstname`, `user_lastname`, `user_password`, `user_mail`, `user_url`, `user_nick`, `user_about`, `user_post_num`, `user_comment_num`, `user_register`, `user_group`) VALUES 
(1, 'admin', 'magike', NULL, '827ccb0eea8a706c4c34a16891f84e7b', 'webmaster@example.com', '{$siteurl}', 'magike_nick', NULL, 1, 0, now(), 0);
");

return true;
}

function unzipPackage()
{
if(file_exists('./index.php'))
{
    unlink('./index.php');
}

if(file_exists('./license.txt'))
{
    unlink('./license.txt');
}

if(is_dir('./data/cache'))
{
	mgRmDir('./data/cache');
}

if(is_dir('./data/compile'))
{
	mgRmDir('./data/compile');
}

if(is_dir('./data/runtime'))
{
	mgRmDir('./data/runtime');
}

if(is_dir('./core'))
{
	mgRmDir('./core');
}

if(is_dir('./templates/admin'))
{
	mgRmDir('./templates/admin');
}

$url = parse_url(install_get_siteurl());
$newTemplate = str_replace('.','',$url['host']).time();
if(is_dir('./templates/default'))
{
    rename('./templates/default','./templates/'.$newTemplate);
}

if(is_dir('./templates/xml'))
{
	mgRmDir('./templates/xml');
}

if(is_dir('./module'))
{
	mgRmDir('./module');
}

if(is_dir('./model'))
{
	mgRmDir('./model');
}

if(is_dir('./language'))
{
	mgRmDir('./language');
}

$zip = new PclZip('source.zip');
if (($list = $zip->listContent()) == 0) {die("解压错误: ".$zip->errorInfo(true));  }
$basedir = '.';
for ($i=0; $i<sizeof($list); $i++) 
{
	if ($list[$i]['folder']=='1') 
	{
		$fold++;
		$dirs[$fold] = $list[$i]['stored_filename'];
		$dirname = $list[$i]['stored_filename'];
		$dirname = substr($dirname,0,strlen($dirname)-1);
		if(!is_dir($basedir.'/'.$dirname))
		{
			mkdir($basedir.'/'.$dirname); 
		}
	}
	@chmod($basedir.'/'.$dirname,0777);
	$tot_comp += $list[$i]['compressed_size'];
	$tot_uncomp += $list[$i]['size'];
}

$zip->extract('');
unlink('source.zip');
mgTouchAll('.');
}

if(!empty($_POST['do']) && 'config' == $_POST['do'])
{
    $config = "<?php
/**********************************
 * Created on: 2006-12-2
 * File Name : config.php
 * Copyright : Magike Group
 * License   : GNU General Public License 2.0
 *********************************/

define('__DBOBJECT__','magike_mysql');
define('__DBHOST__','{$_POST['host']}');
define('__DBUSER__','{$_POST['user']}');
define('__DBPASS__','{$_POST['password']}');
define('__DBNAME__','{$_POST['dbname']}');
define('__DBPREFIX__','{$_POST['one']}');
?>";
    
    file_put_contents('./config.php',$config);
}

$is_writeable = is_writeable('.');
$is_configured = file_exists('./config.php');
$is_cached = is_writeable('./data') || $is_writeable;
$is_php_enable = version_compare(phpversion(),'5.0.0','>');
$is_db_enable = true;
$is_table_enable = true;
$is_db_version = true;
$is_magike_version = true;
$is_update = false;
$is_uploaded = file_exists('./core/core.config.php');

$is_safemode = $is_configured & $is_cached & $is_uploaded;
$is_update_safemode = false;

if($is_configured)
{
    require('./config.php');
    if($dblink=@mysql_connect(__DBHOST__, __DBUSER__,__DBPASS__))
    {
        $is_db_enable = true;
    	if($db = @mysql_select_db(__DBNAME__, $dblink))
    	{
    		$is_table_enable = true;
    		$res = mysql_query('SELECT VERSION() AS version');
    		$row = mysql_fetch_assoc($res);
    		$dbVersion = array_shift(explode('-',$row['version']));
    		$is_db_version = version_compare($dbVersion,'4.1.0','>');
    		if($is_db_version)
    		{
    		    $is_magike_version = checkMagikeVersion();
                $is_update = checkMagikeTables() & $is_uploaded;
    		}
    	}
    	else
    	{
    	    $is_table_enable = false;
    	}
    }
    else
    {
        $is_db_enable = false;
    }
}

if($is_update)
{
    require('./core/core.config.php');
    if(defined('__VALID_BUILD__'))
    {
        if(version_compare(__VALID_BUILD__,__BUILD__,'='))
        {
            $is_update_safemode = true;
        }
    }
}

$is_source_enable = $is_update_safemode | $is_safemode | file_exists('source.zip');
$enable = $is_configured & $is_cached & $is_php_enable & $is_db_enable & $is_table_enable & $is_db_version & $is_magike_version & $is_source_enable;
$do = 'index';

if(!empty($_GET['do']))
{
    switch($_GET['do'])
    {
        case 'init-database':
            install();
            $do = 'init-database';
            break;
        case 'update-database':
            upgrade();
            $do = 'update-database';
            break;
        case 'init-package':
            unzipPackage();
            install();
            $do = 'init-package';
            break;
        case 'update-package':
            unzipPackage();
            upgrade();
            $do = 'update-package';
            break;
        case 'config':
            $do = 'config';
            break;
        default:
            break;
    }
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title><?php echo __VERSION__; ?>安装配置程序</title>
	<style>
		html
        {
        	min-height: 100%;
        }
        
		body
		{
			border-top:4px solid #222;
			background:#FFF;
			padding:0;
			margin:0;
			font-family:Georgia,Times,serif;
			background:url(http://www.magike.org/top.gif) right top repeat-x;
		}
	
		a
		{
			color:#222;
			font-weight:bold;
		}
		
		a:hover
		{
			text-decoration:none;
		}
		
		#banner
		{
			margin:10px;
			padding:10px 10px 0 10px;
		}
		#notice ul,#safemode ul
		{
			margin:5px 20px;
			padding:0;
		}
		#element ul.rows
		{
			height:350px;
			overflow:auto;
			background:#EEE;
			border:1px solid #CCC;
			padding:10px;
			font-size:9pt;
			font-weight:normal;
		}
		#element{margin:0 auto;padding:10px;width:500px;color:#383D44;line-height:24px;}
		#element h1{font-size:14pt;text-align:right;text-align:center;
		padding-right:10px;line-height:70px;height:70px;border-bottom:1px solid #383D44;font-weight:normal}
		#element h2{font-size:12pt;color:#495A67;padding:0;margin:0;border-bottom:1px solid #DDD;}
		#notice,#safemode{background:#DD0000;padding:5px;color:#FFF;margin-bottom:10px;border:1px solid #333;font-size:9pt}
		#safemode{background:#FFFFAA;color:#222;border:1px solid #AAA}
		ul{margin:0;padding:0;list-style:none;font-weight:bold}
		ul input{margin-right:10px}
		ul cite{color:#AAA;font-size:10pt;font-weight:normal;font-style:normal;margin-left:10px}
		#notice ul li a{color:#FFF}
		input.btn{border:1px solid #999;background:#EEE;font-size:12pt;font-weight:bold;padding:5px;color:#333}
		input.text{border:1px solid #999;margin-left:10px;width:200px;font-size:12pt;padding:2px}
	</style>
</head>
<body>
	<div id="element">
		<h1><img src="http://www.typecho.org/wlogo.png" /></h1>
		<?php if('index' == $do){ ?>
		<?php if(!$enable){ ?>
		<div id="notice">
		<ul>
			<?php if(!$is_configured){ ?>
			<?php if($is_writeable){ ?>
			<li>没有找到配置文件,<a href="<?php echo install_get_siteurl()."/setup.php?do=config" ?>">点击这里创建配置文件</a></li>
			<?php }else{ ?>
			<li>没有找到配置文件,请将安装目录设置为可写或者使用<a href="http://wiki.magike.org/doku.php?id=safemode">安全安装模式</a></li>
			<?php } ?>
			<?php } ?>
			<?php if($is_configured && !$is_cached){ ?><li>缓存目录不可写,请检查"data"目录是否存在或者其权限是否可写</li><?php } ?>
			<?php if(!$is_php_enable){ ?><li>PHP版本太低,请升级到PHP 5.0.0或以上的版本</li><?php } ?>
			<?php if(!$is_db_enable){ ?>
			<?php if($is_writeable){ ?>
			<li>数据库连接失败,请检查<a href="<?php echo install_get_siteurl()."/setup.php?do=config" ?>">config.php的配置</a></li>
			<?php }else{ ?>
			<li>数据库连接失败,请检查config.php的配置</li>
			<?php } ?>
			<?php } ?>
			<?php if(!$is_table_enable){ ?><li>数据库"<?php echo __DBNAME__; ?>"不存在,请先建立此数据库</li><?php } ?>
			<?php if(!$is_db_version){ ?><li>数据库版本太低,请升级到Mysql 4.1.0或以上的版本</li><?php } ?>
			<?php if(!$is_source_enable){ ?><li>没有找到合适的安装源,请上传source.zip文件或者使用<a href="http://wiki.magike.org/doku.php?id=safemode">安全安装模式</a></li><?php } ?>
			<?php if(!$is_magike_version){ ?><li>您的Magike版本过低,请先升级至<a href="http://code.google.com/p/magike/downloads/detail?name=install.7.11.7-1.1.0Release.zip&can=2&q=#makechanges">Magike 1.1.0</a>或<a href="http://code.google.com/p/magike/downloads/list">更高版本</a></li><?php } ?>
		</ul>
		</div>
		<?php } ?>
		<?php if($enable && ($is_update || $is_safemode)){ ?>
		<div id="safemode">
		<ul>
			<?php if($is_safemode && !$is_update){ ?><li>正在使用安全安装模式,此模式仅创建数据库结构</li><?php } ?>
			<?php if($is_update){ ?>
			<?php if($is_update_safemode){ ?>
			<li>正在使用安全升级模式,此模式仅升级数据库结构</li>
			<?php }else{ ?>
			<li>升级模式</li>
			<?php } ?>
			<?php } ?>
		</ul>
		</div>
		<?php } ?>
    	<ul class="rows">
    		<li><h2>版权信息</h2>
    		<p>Magike Blog是一款开源免费的博客程序.您可以在
    		<a href="http://www.opensource.org/licenses/gpl-license.php" target="__blank">GPL协议</a>允许的范围内使用此产品.</p>
    		<p>您可以在该协议授权的范围内使用或修改此软件.此协议的约束范围并不包括在该软件中使用的第三方库资源.所有在本软件中使用的第三方资源
    著作权归原作者所有,其版权协议也继承自原有协议.</p>
    		</li>
    		<li><h2>支持及免责条款</h2>
    		<p>您可以在<a href="http://www.magike.org" target="__blank">Magike官方网站</a>或者<a href="http://forum.magike.org" target="__blank">Magike开发社区</a>获得持续的技术支持.</p>
    		<p>我们并不保证使用该程序不存在任何风险,对使用该程序可能造成的损失不承担任何责任.但是我们会对可能存在的风险进行持续的跟踪评估,并尽量减少您的损失.</p>
    		</li>
    		<li><h2>致谢</h2>
    		<p>对所有在Magike开发过程中给予我们支持和帮助的朋友表示感谢.对在Magike测试过程中辛勤劳动的测试人员表示感谢.对以下在本软件中使用的第三方资源的原作者表示感谢:</p>
    		<p>
    			<strong>Silk图标</strong>,作者:<a href="http://www.famfamfam.com"  target="_blank">Mark James</a>,使用范围:后台部分图标及默认模板部分图标<br />
    			<strong>PHPMailer库</strong>,作者:<a href="http://phpmailer.sourceforge.net"  target="_blank">Chris Ryan</a>,使用范围:第三方类库<br />
    			<strong>XML-RPC库</strong>,作者:<a href="http://scripts.incutio.com/xmlrpc/"  target="_blank">Incutio Ltd</a>,使用范围:第三方类库<br />
    			<strong>ServicesJson库</strong>,作者:<a href="http://pear.php.net/pepr/pepr-proposal-show.php?id=198"  target="__blank">Michal Migurski,Matt Knapp,Brett Stimmerman</a>,使用范围:第三方类库<br />
    			<strong>jQuery库</strong>,作者:<a href="http://www.jquery.com"  target="_blank">jQuery team</a>,使用范围:javascript框架<br />
    			<strong>Gettext库</strong>,作者:<a href="http://pear.php.net/package/File_Gettext"  target="_blank">Michael Wallner</a>,使用范围:国际化语言文件读取库<br />
    			<strong>NetIDNA库</strong>,作者:<a href="http://pear.php.net/package/Net_IDNA/download" target="_blank">Markus Nix,Matthias Sommerfeld</a>,使用范围:第三方类库
                     <strong>管理后台语义标准化</strong>,作者:<a href="http://www.awflasher.com" target="_blank">awflasher</a>,使用范围:后台语言源
              </p>
    		</li>
    	</ul>
		<?php if($enable){ ?>
		<p>
			<?php if($is_update){ ?>
    			<?php if($is_update_safemode){ ?>
    			<h2 style="border:none"><a href="<?php echo install_get_siteurl()."/setup.php?do=update-database" ?>">立刻开始安全升级&raquo;</a></h2>
    			<?php }else{ ?>
    			<h2 style="border:none"><a href="<?php echo install_get_siteurl()."/setup.php?do=update-package" ?>">立刻开始升级&raquo;</a></h2>
    			<?php } ?>
			<?php }else{ ?>
    			<?php if($is_safemode){ ?>
    			<h2 style="border:none"><a href="<?php echo install_get_siteurl()."/setup.php?do=init-database" ?>">立刻开始安全安装&raquo;</a></h2>
    			<?php }else{ ?>
    			<h2 style="border:none"><a href="<?php echo install_get_siteurl()."/setup.php?do=init-package" ?>">立刻开始安装&raquo;</a></h2>
    			<?php } ?>
			<?php } ?>
		</p>
		<?php } ?>
		<?php } ?>
		<?php if('config' == $do){ ?>
		<form method="post" action="<?php echo install_get_siteurl()."/setup.php" ?>">
		<p style="line-height:30px">
    		<span>数据库主机:</span><input type="text" class="text" name="host" value="<?php echo defined('__DBHOST__') ? __DBHOST__ : 'localhost';  ?>"/><br/>
    		<span>数据库用户:</span><input type="text" class="text" name="user" value="<?php echo defined('__DBUSER__') ? __DBUSER__ : 'root';  ?>"/><br/>
    		<span>数据库密码:</span><input type="text" class="text" name="password" value="<?php echo defined('__DBPASS__') ? __DBPASS__ : '';  ?>" /><br/>
    		<span>数据库名称:</span><input type="text" class="text" name="dbname" value="<?php echo defined('__DBNAME__') ? __DBNAME__ : 'test';  ?>" /><br/>
    		<span>数据表前缀:</span><input type="text" class="text" name="one" value="<?php echo defined('__DBPREFIX__') ? __DBPREFIX__ : 'mg_';  ?>"/><br/>
    		<input type="submit" value="创建配置文件&raquo;" class="btn" /><input type="hidden" name="do" value="config" />
		</p>
		</form>
		<?php } ?>
		<?php if('update-database' == $do || 'update-package' == $do){ ?>
		<h2>升级完成</h2>
		<p>
		请立即删除<strong>setup.php</strong>,<a href="<?php echo install_get_siteurl()."/" ?>">点击这里进入首页</a>
		</p>
		<?php } ?>
		<?php if('init-database' == $do || 'init-package' == $do){ ?>
		<h2>安装完成</h2>
		<p>
		请立即删除<strong>setup.php</strong>,<a href="<?php echo install_get_siteurl()."/" ?>">点击这里进入首页</a><br />
		默认的用户名为<strong>admin</strong>,密码是<strong>12345</strong>.建议您立即修改这些信息.
		</p>
		<?php } ?>
	</div>
</body>
</html>