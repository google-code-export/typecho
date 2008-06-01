<?php
/**
 * 链接数据提交
 * 
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */
 
/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/** 载入提交基类支持 **/
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Metas.php';

class DoLinkWidget extends MetasWidget
