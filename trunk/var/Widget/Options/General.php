<?php
/**
 * 基本设置
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 基本设置组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options_General extends Widget_Abstract_Options implements Widget_Interface_Do
{
    public function checkApache($value)
    {
        
    }

    /**
     * 检测是否可以rewrite
     * 
     * @access public
     * @param string $value 是否打开rewrite
     * @return void
     */
    public function checkRewrite($value)
    {
        if ($value) {
            $this->user->pass('administrator');
        
            /** 首先直接请求远程地址验证 */
            $client = Typecho_Http_Client::get();
            
            if (!file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess')) {
                if (is_writeable(__TYPECHO_ROOT_DIR__)) {
                    $parsed = parse_url($this->options->siteUrl);
                    $basePath = empty($parsed['path']) ? '/' : $parsed['path'];
                    $basePath = rtrim($basePath, '/') . '/';
                    
                    file_put_contents(__TYPECHO_ROOT_DIR__ . '/.htaccess', "<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$basePath}
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ {$basePath}index.php/$1 [L]
</IfModule>");
                }
            }
            
            if ($client) {
                /** 发送一个rewrite地址请求 */
                $client->setData(array('do' => 'remoteCallback'))
                ->setHeader('User-Agent', $this->options->generator)
                ->send(Typecho_Common::url('Ajax.do', $this->options->siteUrl));
                
                if (200 == $client->getResponseStatus() && 'OK' == $client->getResponseBody()) {
                    return true;
                }
            }
            
            return false;
        }
        
        return true;
    }

    /**
     * 输出表单结构
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form
     */
    public function form()
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/Options/General.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 站点名称 */
        $title = new Typecho_Widget_Helper_Form_Element_Text('title', NULL, $this->options->title, _t('站点名称'), _t('站点的名称将显示在网页的标题处.'));
        $form->addInput($title);
        
        /** 站点描述 */
        $description = new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, $this->options->description, _t('站点描述'), _t('站点描述将显示在网页代码的头部.'));
        $form->addInput($description);
        
        /** 关键词 */
        $keywords = new Typecho_Widget_Helper_Form_Element_Text('keywords', NULL, $this->options->keywords, _t('关键词'), _t('请以半角逗号","分割多个关键字.'));
        $form->addInput($keywords);
        
        /** 时区 */
        $timezone = new Typecho_Widget_Helper_Form_Element_Select('timezone', array('28800' => _t('中华人民共和国')), $this->options->timezone, _t('时区'));
        $form->addInput($timezone);
        
        /** 是否使用地址重写功能 */
        $rewrite = new Typecho_Widget_Helper_Form_Element_Radio('rewrite', array('0' => _t('不启用'), '1' => _t('启用')),
        $this->options->rewrite, _t('是否使用地址重写功能'), _t('地址重写即rewrite功能是某些服务器软件提供的优化内部连接的功能.<br />
        打开此功能可以让你的链接看上去完全是静态地址.'));
        
        $errorStr = _t('无法启用重写功能, 请检查你的服务器设置');
        
        /** 如果是apache服务器, 可能存在无法写入.htaccess文件的现象 */
        if (((isset($_SERVER['SERVER_SOFTWARE']) && false !== strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache'))
        || function_exists('apache_get_version')) && !file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess')
        && !is_writeable(__TYPECHO_ROOT_DIR__)) {
            $errorStr .= _t('<br /><strong>我们检测到你使用了apache服务器, 但是程序无法在根目录创建.htaccess文件, 这可能是产生这个错误的原因.
            请调整你的目录权限, 或者手动创建一个.htaccess文件.</strong>');
        }
        
        $form->addInput($rewrite->addRule(array($this, 'checkRewrite'), $errorStr));
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('保存设置'));
        $form->addItem($submit);
        
        return $form;
    }
    
    /**
     * 执行更新动作
     * 
     * @access public
     * @return void
     */
    public function updateGeneralSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
        
        $settings = $this->request->from('title', 'description', 'keywords', 'timezone', 'rewrite');
        foreach ($settings as $name => $value) {
            $this->update(array('value' => $value), $this->db->sql()->where('name = ?', $name));
        }
        
        $this->widget('Widget_Notice')->set(_t("设置已经保存"), NULL, 'success');
        $this->response->goBack();
    }

    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->onPost()->updateGeneralSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
