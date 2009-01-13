<?php
/**
 * 编辑用户
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑用户组件
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Users_Profile extends Widget_Users_Edit implements Widget_Interface_Do
{
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 注册用户以上权限 */
        $this->user->pass('subscriber');
        $this->request->uid = $this->user->uid;
    }
    
    /**
     * 生成表单
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form
     */
    public function form()
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/Users/Profile.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 个人主页地址 */
        $url = new Typecho_Widget_Helper_Form_Element_Text('url', NULL, NULL, _t('个人主页地址'), _t('此用户的个人主页地址, 请用<strong>http://</strong>开头.'));
        $form->addInput($url);

        /** 电子邮箱地址 */
        $mail = new Typecho_Widget_Helper_Form_Element_Text('mail', NULL, NULL, _t('电子邮箱地址*'), _t('电子邮箱地址将作为此用户的主要联系方式.<br />
        请不要与系统中现有的电子邮箱地址重复.'));
        $form->addInput($mail);
        
        /** 用户昵称 */
        $screenName = new Typecho_Widget_Helper_Form_Element_Text('screenName', NULL, NULL, _t('昵称'), _t('用户昵称可以与用户名不同, 用于前台显示.<br />
        如果你将此项留空,将默认使用用户名.'));
        $form->addInput($screenName);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('更新我的档案'));
        $form->addItem($submit);
        
        $screenName->value($this->user->screenName);
        $url->value($this->user->url);
        $mail->value($this->user->mail);
        
        /** 给表单增加规则 */
        $screenName->addRule(array($this, 'screenNameExists'), _t('昵称已经存在'));
        $url->addRule('url', _t('个人主页地址格式错误'));
        $mail->addRule('required', _t('必须填写电子邮箱'));
        $mail->addRule(array($this, 'mailExists'), _t('电子邮箱地址已经存在'));
        $mail->addRule('email', _t('电子邮箱格式错误'));
        
        return $form;
    }
    
    /**
     * 输出表单结构
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form
     */
    public function optionsForm()
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/Options/Writing.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 编辑器大小 */
        $editorSize = new Typecho_Widget_Helper_Form_Element_Text('editorSize', NULL, $this->options->editorSize,
        _t('编辑器大小'), _t('所见即所得编辑器的大小.'));
        $form->addInput($editorSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 自动保存 */
        $autoSave = new Typecho_Widget_Helper_Form_Element_Radio('autoSave',
        array('0' => _t('关闭'), '1' => _t('打开')),
        $this->options->autoSave, _t('自动保存'), _t('自动保存功能可以更好地保护您的文章不会丢失.'));
        $form->addInput($autoSave);
        
        /** 默认允许评论 */
        $defaultAllowComment = new Typecho_Widget_Helper_Form_Element_Radio('defaultAllowComment',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->options->defaultAllowComment, _t('默认允许评论'));
        $form->addInput($defaultAllowComment);
        
        /** 默认允许广播 */
        $defaultAllowPing = new Typecho_Widget_Helper_Form_Element_Radio('defaultAllowPing',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->options->defaultAllowPing, _t('默认允许广播'));
        $form->addInput($defaultAllowPing);
        
        /** 默认允许聚合*/
        $defaultAllowFeed = new Typecho_Widget_Helper_Form_Element_Radio('defaultAllowFeed',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->options->defaultAllowFeed, _t('默认允许聚合'));
        $form->addInput($defaultAllowFeed);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('保存设置'));
        $form->addItem($submit);
        
        return $form;
    }
    
    /**
     * 生成表单
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form
     */
    public function passwordForm()
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/Users/Edit.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 用户密码 */
        $password = new Typecho_Widget_Helper_Form_Element_Password('password', NULL, NULL, _t('用户密码'), _t('为此用户分配一个密码.<br />
        建议使用特殊字符与字母的混编样式,以增加系统安全性.'));
        $form->addInput($password);
        
        /** 用户密码确认 */
        $confirm = new Typecho_Widget_Helper_Form_Element_Password('confirm', NULL, NULL, _t('用户密码确认'), _t('请确认你的密码, 与上面输入的密码保持一致.'));
        $form->addInput($confirm);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('更新密码'));
        $form->addItem($submit);
        
        $password->addRule('minLength', _t('为了保证账户安全, 请输入至少六位的密码'), 6);
        $confirm->addRule('confirm', _t('两次输入的密码不一致'), 'password');
        
        return $form;
    }
    
    /**
     * 更新用户
     * 
     * @access public
     * @return void
     */
    public function updateProfile()
    {
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
    
        /** 取出数据 */
        $user = $this->request->from('mail', 'screenName', 'url');
        $user['screenName'] = empty($user['screenName']) ? $user['name'] : $user['screenName'];
    
        /** 更新数据 */
        $this->update($user, $this->db->sql()->where('uid = ?', $this->user->uid));
        
        /** 设置高亮 */
        $this->widget('Widget_Notice')->highlight('user-' . $this->user->uid);
        
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t('您的档案已经更新'), NULL, 'success');
        
        /** 转向原页 */
        $this->response->goBack();
    }
    
    /**
     * 执行更新动作
     * 
     * @access public
     * @return void
     */
    public function updateOptions()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
    
        $settings = $this->request->from('editorSize', 'autoSave', 'defaultAllowComment', 'defaultAllowPing', 'defaultAllowFeed');
        foreach ($settings as $name => $value) {
            $this->update(array('value' => $value), $this->db->sql()->where('name = ?', $name));
        }

        $this->widget('Widget_Notice')->set(_t("设置已经保存"), NULL, 'success');
        $this->response->goBack();
    }
    
    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->onPost()->updateProfile();
        $this->response->redirect($this->options->adminUrl);
    }
}
