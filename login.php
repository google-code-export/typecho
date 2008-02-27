<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: index.php 20 2008-02-24 09:59:53Z magike.net $
 */

/** 载入项目配置文件 **/
require 'includes/config.php';

/** 载入组件库 **/
require 'component/Options.php';

class LoginController extends TypechoController
{
    protected function renderResponse()
    {
        $options = new OptionsComponent();
        require 'var/admin/login.php';
    }
}

new LoginController();
