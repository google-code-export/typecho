<?php
/**
 * 升级动作
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 升级组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 */
class Widget_Upgrade extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 当前内部版本号
     * 
     * @access private
     * @var string
     */
    private $_currentVersion;

    /**
     * 对升级包按版本进行排序
     * 
     * @access public
     * @param string $a a版本
     * @param string $b b版本
     * @return integer
     */
    public function sortPackage($a, $b)
    {
        return version_compare($a, $b, '>') ? 1 : -1;
    }
    
    /**
     * 过滤低版本的升级包
     * 
     * @access public
     * @param string $version 版本号
     * @return boolean
     */
    public function filterPackage($version)
    {
        return preg_match("|^[0-9]+\.[0-9]+\.[0-9]+$|", $version) &&
        version_compare($version, $this->_currentVersion, '>');
    }

    /**
     * 执行升级程序
     * 
     * @access public
     * @return void
     */
    public function upgrade()
    {
        list($prefix, $this->_currentVersion) = explode('/', $this->options->generator);
        $packages = array_map('basename', glob(__TYPECHO_ROOT_DIR__ . '/install/upgrade/*'));
        $packages = array_filter($packages, array($this, 'filterPackage'));
        usort($packages, array($this, 'sortPackage'));
        
        foreach ($packages as $package) {
            $file = __TYPECHO_ROOT_DIR__ . '/install/upgrade/' . $package . '/upgrade.php';
            if (is_file($file)) {
                /** 执行升级脚本 */
                try {
                    require_once $file;
                } catch (Typecho_Exception $e) {
                    $this->widget('Widget_Notice')->set($e->getMessage(), NULL, 'error');
                    $this->response->goBack();
                }
            }
        }
        
        /** 更新版本号 */
        $this->update(array('value' => 'Typecho ' . Typecho_Common::VERSION), 
        $this->db->sql()->where('name = ?', 'generator'));
        
        $this->widget('Widget_Notice')->set(_t("升级已经完成"), NULL, 'success');
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->onPost()->upgrade();
        $this->response->redirect($this->options->adminUrl);
    }
}
