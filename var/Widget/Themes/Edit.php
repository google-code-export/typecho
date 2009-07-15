<?php
/**
 * 编辑风格
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑风格组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Themes_Edit extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 更换外观
     * 
     * @access public
     * @param string $theme 外观名称
     * @return void
     */
    public function changeTheme($theme)
    {
        $theme = trim($theme, './');
        if (is_dir(__TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/' . $theme)) {
            $this->update(array('value' => $theme), $this->db->sql()->where('name = ?', 'theme'));
            $this->widget('Widget_Notice')->highlight('theme-' . $theme);
            $this->widget('Widget_Notice')->set(_t("外观已经改变"), NULL, 'success');
            $this->response->goBack();
        } else {
            throw new Typecho_Widget_Exception(_t('您选择的风格不存在'));
        }
    }
    
    /**
     * 编辑外观文件
     * 
     * @access public
     * @param string $theme 外观名称
     * @param string $file 文件名
     * @return void
     */
    public function editThemeFile($theme, $file)
    {
        $path = __TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/' . trim($theme, './') . '/' . trim($file, './');
        
        if (file_exists($path) && is_writeable($path)) {
            $handle = fopen($path, 'wb');
            if ($handle && fwrite($handle, $this->request->content)) {
                fclose($handle);
                $this->widget('Widget_Notice')->set(_t("文件 %s 的更改已经保存", $file), NULL, 'success');
            } else {
                $this->widget('Widget_Notice')->set(_t("文件 %s 无法被写入", $file), NULL, 'error');
            }
            $this->response->goBack();
        } else {
            throw new Typecho_Widget_Exception(_t('您编辑的文件不存在'));
        }
    }
    
    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        /** 需要管理员权限 */
        $this->user->pass('administrator');
        $this->on($this->request->is('change'))->changeTheme($this->request->change);
        $this->on($this->request->is('edit&theme'))->editThemeFile($this->request->theme, $this->request->edit);
        $this->response->redirect($this->options->adminUrl);
    }
}
