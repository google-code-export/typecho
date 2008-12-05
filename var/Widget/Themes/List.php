<?php
/**
 * 风格列表
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 风格列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Themes_List extends Typecho_Widget
{
    /**
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function init()
    {
        $themes = glob(__TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/*');
        foreach ($themes as $theme) {
            $themeFile = $theme . '/index.php';
            if (is_file($themeFile)) {
                $info = Typecho_Plugin::parseInfo($themeFile);
                $info['name'] = basename($theme);
                $this->push($info);
            }
        }
    }
}