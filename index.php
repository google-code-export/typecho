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
require 'includes/config.php';

/** 载入组件库 **/
//require 'component/Archives.php';
//require 'component/Content.php';
//require 'component/Widget.php';
require 'component/Options.php';
//require 'component/Users.php';

class IndexController extends TypechoController
{   
    public function renderResponse()
    {
        $options = new OptionsComponent();
        //$widget = new WidgetComponent($options);
        //$archives = new ArchivesComponent($options);
        //$user = new UserComponent($options);
        //$content = new ContentComponent($options);
    }
}

new IndexController();
