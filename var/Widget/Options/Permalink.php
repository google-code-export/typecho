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
class Widget_Options_Permalink extends Widget_Abstract_Options implements Widget_Interface_Do
{
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
        } else if (file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess')) {
            @unlink(__TYPECHO_ROOT_DIR__ . '/.htaccess');
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
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/Options/Permalink.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 是否使用地址重写功能 */
        $rewrite = new Typecho_Widget_Helper_Form_Element_Radio('rewrite', array('0' => _t('不启用'), '1' => _t('启用')),
        $this->options->rewrite, _t('是否使用地址重写功能'), _t('地址重写即rewrite功能是某些服务器软件提供的优化内部连接的功能.<br />
        打开此功能可以让你的链接看上去完全是静态地址.'));
        
        $errorStr = _t('无法启用重写功能, 请检查你的服务器设置');
        
        /** 如果是apache服务器, 可能存在无法写入.htaccess文件的现象 */
        if (((isset($_SERVER['SERVER_SOFTWARE']) && false !== strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache'))
        || function_exists('apache_get_version')) && !file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess')
        && !is_writeable(__TYPECHO_ROOT_DIR__)) {
            $errorStr .= '<br /><strong>' . _t('我们检测到你使用了apache服务器, 但是程序无法在根目录创建.htaccess文件, 这可能是产生这个错误的原因.
            请调整你的目录权限, 或者手动创建一个.htaccess文件.') . '</strong>';
        }
        
        $form->addInput($rewrite->addRule(array($this, 'checkRewrite'), $errorStr));
        
        /** 自定义文章路径 */
        $postPatternValue = $this->options->routingTable['post']['url'];
        $postPattern = new Typecho_Widget_Helper_Form_Element_Select('postPattern',
        array('/archives/[cid:digital]/' => '/archives/{post_id}/', 
        '/archives/[slug].html' => '/archives/{post_slug}.html', 
        '/[year:digital:4]/[month:digital:2]/[day:digital:2]/[slug].html' => '/archives/{year}/{month}/{day}/{slug}.html',
        '/[category]/[slug].html' => '/{category}/{post_slug}.html'),
        $postPatternValue, _t('自定义文章路径'), _t('选择一种合适的文章静态路径风格, 使得你的网站链接更加友好.<br />
        一旦你选择了某种链接风格请不要轻易修改它.'));
        $form->addInput($postPattern);
        
        /** 独立页面后缀名 */
        $pageSuffixValue = false !== ($pos = strrpos($this->options->routingTable['page']['url'], '.')) ?
        substr($this->options->routingTable['page']['url'], $pos) : '/';
        $pageSuffix = new Typecho_Widget_Helper_Form_Element_Radio('pageSuffix', 
        array('/' => '<strong>' . _t('无') . '</strong>', '.html' => '<strong>html</strong>',
        '.htm' => '<strong>htm</strong>', '.php' => '<strong>php</strong>'), $pageSuffixValue,
        _t('独立页面后缀名'), _t('给独立页面设置一种文件后缀名, 使得它看起来像
        <br /><strong>%s</strong>',
        Typecho_Common::url('example.html', $this->options->index)));
        $form->addInput($pageSuffix);
        
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
    public function updatePermalinkSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
        
        $settings = $this->request->from('rewrite');
        if (isset($this->request->postPattern) && isset($this->request->pageSuffix)) {
            $routingTable = $this->options->routingTable;
            $routingTable['post']['url'] = $this->request->postPattern;
            
            $pageValue = false !== ($pos = strrpos($routingTable['page']['url'], '.')) ?
            substr($routingTable['page']['url'], 0, $pos) : rtrim($routingTable['page']['url'], '/');
            $routingTable['page']['url'] = $pageValue . $this->request->pageSuffix;

            if (isset($routingTable[0])) {
                unset($routingTable[0]);
            }
            
            $settings['routingTable'] = serialize($routingTable);
        }
        
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
        $this->onPost()->updatePermalinkSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
