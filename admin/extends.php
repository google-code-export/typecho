<?php
/**
 * 后台扩展面板,开发者可以通过插件系统扩展此面板功能
 * 
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

require_once 'common.php';
require_once 'header.php';
require_once 'menu.php';

/** 调用插件 */
_p(__FILE__, 'Layout')->display();

require_once 'footer.php';
