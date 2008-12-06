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
        $options = $this->widget('Widget_Options');
        $siteUrl = $options->siteUrl;
        $adminUrl = $options->adminUrl;
        
        foreach ($themes as $theme) {
            $themeFile = $theme . '/index.php';
            if (is_file($themeFile)) {
                $info = Typecho_Plugin::parseInfo($themeFile);
                $info['name'] = basename($theme);
                
                /** 支持png,jpg,gif三种截图格式,推荐比率为4:3 */
                switch (true) {
                    case is_file($themes . '/screen.png'):
                        $info['screen'] = Typecho_Common::url(trim(__TYPECHO_THEME_DIR__, '/') 
                        . '/' . $theme . '/screen.png', $siteUrl);
                        break;
                    case is_file($themes . '/screen.jpg'):
                        $info['screen'] = Typecho_Common::url(trim(__TYPECHO_THEME_DIR__, '/') 
                        . '/' . $theme . '/screen.jpg', $siteUrl);
                        break;
                    case is_file($themes . '/screen.gif'):
                        $info['screen'] = Typecho_Common::url(trim(__TYPECHO_THEME_DIR__, '/') 
                        . '/' . $theme . '/screen.gif', $siteUrl);
                        break;
                    default:
                        $info['screen'] = Typecho_Common::url('/images/noscreen.gif', $adminUrl);
                        break;
                }
                
                $this->push($info);
            }
        }
    }
}
