<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入项目配置文件 **/
require 'inlucdes/config.php';

/** 载入组件库 **/
require 'components/Archives.php';
require 'components/Content.php';
require 'components/Widget.php';
require 'components/Options.php';
require 'components/Users.php';

class IndexController extends TypechoController
{   
    public function renderResponse()
    {
        $options = new OptionsComponent(); 
        $widget = new WidgetComponent($options);
        $archives = new ArchivesComponent($options);
        $user = new UserComponent($options);
        $content = new ContentComponent($options);
    }
}
