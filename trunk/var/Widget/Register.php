<?php
/**
 * 注册组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 */
class Widget_Register extends Widget_Abstract_Users implements Widget_Interface_Do
{
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        /** 如果已经登录 */
        if ($this->user->hasLogin() || !$this->options->allowRegister) {
            /** 直接返回 */
            $this->response->redirect($this->options->index);
        }
    
        /** 初始化验证类 */
        $validator = new Typecho_Validate();
        $validator->addRule('name', 'required', _t('必须填写用户名称'));
        $validator->addRule('name', array($this, 'nameExists'), _t('用户名已经存在'));
        $validator->addRule('mail', 'required', _t('必须填写电子邮箱'));
        $validator->addRule('mail', array($this, 'mailExists'), _t('电子邮箱地址已经存在'));
        $validator->addRule('mail', 'email', _t('电子邮箱格式错误'));
        
        /** 截获验证异常 */
        if ($error = $validator->run($this->request->from('name', 'password', 'mail', 'confirm'))) {
            Typecho_Cookie::set('__typecho_remember_name', $this->request->filter('strip_tags', 'trim', 'xss')->name);
            Typecho_Cookie::set('__typecho_remember_mail', $this->request->filter('strip_tags', 'trim', 'xss')->mail);
        
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error);
            $this->response->goBack();
        }
        
        $generatedPassword = Typecho_Common::randString(7);
        
        $dataStruct = array(
            'name'      =>  $this->request->filter('strip_tags', 'trim', 'xss')->name,
            'mail'      =>  $this->request->filter('strip_tags', 'trim', 'xss')->mail,
            'screenName'=>  $this->request->filter('strip_tags', 'trim', 'xss')->name,
            'password'  =>  Typecho_Common::hash($generatedPassword),
            'created'   =>  $this->options->gmtTime,
            'group'     =>  'subscriber'
        );
        
        $dataStruct = $this->plugin()->register($dataStruct);
        
        $insertId = $this->insert($dataStruct);
        $this->db->fetchRow($this->select()->where('uid = ?', $insertId)
        ->limit(1), array($this, 'push'));
        
        $this->plugin()->finishRegister($this);
        
        $this->user->login($this->request->name, $generatedPassword);
        
        Typecho_Cookie::delete('__typecho_first_run');
        Typecho_Cookie::delete('__typecho_remember_name');
        Typecho_Cookie::delete('__typecho_remember_mail');
        
        $this->widget('Widget_Notice')->set('message',
        _t('用户 <strong>%s</strong> 已经成功注册, 密码为 <strong>%s</strong>', $this->screenName, $generatedPassword), 'success');
        $this->response->goBack();
    }
}
