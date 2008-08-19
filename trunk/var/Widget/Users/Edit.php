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
class Widget_Users_Edit extends Widget_Abstract_Users implements Widget_Interface_Action_Widget
{
    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    
        /** 编辑以上权限 */
        Typecho_API::factory('Widget_Users_Current')->pass('administrator');
    }
    
    /**
     * 判断用户是否存在
     * 
     * @access public
     * @param integer $uid 用户主键
     * @return boolean
     */
    public function userExists($uid)
    {
        $user = $this->db->fetchRow($this->db->sql()->select('table.users')
        ->where('`uid` = ?', $uid)->limit(1));
        
        return $user ? true : false;
    }
    
    /**
     * 判断用户名称是否存在
     * 
     * @access public
     * @param string $name 用户名称
     * @return boolean
     */
    public function nameExists($name)
    {
        $select = $this->db->sql()->select('table.users')
        ->where('`name` = ?', $name)
        ->limit(1);
        
        if(Typecho_Request::getParameter('uid'))
        {
            $select->where('`uid` <> ?', Typecho_Request::getParameter('uid'));
        }

        $user = $this->db->fetchRow($select);
        return $user ? false : true;
    }
    
    /**
     * 判断电子邮件是否存在
     * 
     * @access public
     * @param string $mail 电子邮件
     * @return boolean
     */
    public function mailExists($mail)
    {
        $select = $this->db->sql()->select('table.users')
        ->where('`mail` = ?', $mail)
        ->limit(1);
        
        if(Typecho_Request::getParameter('uid'))
        {
            $select->where('`uid` <> ?', Typecho_Request::getParameter('uid'));
        }

        $user = $this->db->fetchRow($select);
        return $user ? false : true;
    }
    
    /**
     * 判断用户昵称是否存在
     * 
     * @access public
     * @param string $screenName 昵称
     * @return boolean
     */
    public function screenNameExists($screenName)
    {
        $select = $this->db->sql()->select('table.users')
        ->where('`screenName` = ?', $screenName)
        ->limit(1);
        
        if(Typecho_Request::getParameter('uid'))
        {
            $select->where('`uid` <> ?', Typecho_Request::getParameter('uid'));
        }
    
        $user = $this->db->fetchRow($select);
        return $user ? false : true;
    }
    
    /**
     * 生成表单
     * 
     * @access public
     * @param string $action 表单动作
     * @return Typecho_Widget_Helper_Form
     */
    public function form($action = NULL)
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Users/Edit.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 创建标题 */
        $title = new Typecho_Widget_Helper_Layout('h4');
        $form->addItem($title->setAttribute('id', 'edit'));
        
        /** 用户名称 */
        $name = new Typecho_Widget_Helper_Form_Text('name', NULL, _t('用户名*'), _t('此用户名将作为用户登录时所用的名称.<br />
        请不要与系统中现有的用户名重复.'));
        $name->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($name);

        /** 电子邮箱地址 */
        $mail = new Typecho_Widget_Helper_Form_Text('mail', NULL, _t('电子邮箱地址*'), _t('电子邮箱地址将作为此用户的主要联系方式.<br />
        请不要与系统中现有的电子邮箱地址重复.'));
        $mail->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($mail);
        
        /** 用户昵称 */
        $screenName = new Typecho_Widget_Helper_Form_Text('screenName', NULL, _t('用户昵称'), _t('用户昵称可以与用户名不同，用于前台显示.<br />
        如果你将此项留空,将默认使用用户名.'));
        $screenName->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($screenName);
        
        /** 用户密码 */
        $password = new Typecho_Widget_Helper_Form_Password('password', NULL, _t('用户密码'), _t('为此用户分配一个密码.<br />
        建议使用特殊字符与字母的混编样式,以增加系统安全性.'));
        $password->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($password);
        
        /** 用户密码确认 */
        $confirm = new Typecho_Widget_Helper_Form_Password('confirm', NULL, _t('用户密码确认'), _t('请确认你的密码,与上面输入的密码保持一致.'));
        $confirm->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($confirm);
        
        /** 个人主页地址 */
        $url = new Typecho_Widget_Helper_Form_Text('url', NULL, _t('个人主页地址'), _t('此用户的个人主页地址,请用<strong>http://</strong>开头.'));
        $url->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($url);
        
        /** 用户组 */
        $group =  new Typecho_Widget_Helper_Form_Select('group', array('visitor' => _t('访问者'),
        'subscriber' => _t('关注者'), 'contributor' => _t('贡献者'), 'editor' => _t('编辑'), 'administrator' => _t('管理员')),
        NULL, _t('用户组'), _t('不同的用户组拥有不同的权限.<br />
        具体的权限分配表请<a href="#">参考这里</a>.'));
        $form->addInput($group);
        
        /** 用户动作 */
        $do = new Typecho_Widget_Helper_Form_Hidden('do');
        $form->addInput($do);
        
        /** 用户主键 */
        $uid = new Typecho_Widget_Helper_Form_Hidden('uid');
        $form->addInput($uid);
        
        /** 空格 */
        $form->addItem(new Typecho_Widget_Helper_Layout('hr', array('class' => 'space')));
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Submit();
        $submit->button->setAttribute('class', 'submit');
        $form->addItem($submit);

        if(NULL != Typecho_Request::getParameter('uid'))
        {
            /** 更新模式 */
            $user = $this->db->fetchRow($this->select()
            ->where('`uid` = ?', Typecho_Request::getParameter('uid'))->limit(1));
            
            if(!$user)
            {
                throw new Typecho_Widget_Exception(_t('用户不存在'), Typecho_Exception::NOTFOUND);
            }
            
            $title->html(_t('编辑用户'));
            $submit->value(_t('编辑用户'));
            $name->value($user['name']);
            $screenName->value($user['screenName']);
            $url->value($user['url']);
            $mail->value($user['mail']);
            $group->value($user['group']);
            $do->value('update');
            $uid->value($user['uid']);
            $_action = 'update';
        }
        else
        {
            $title->html(_t('增加用户'));
            $submit->value(_t('增加用户'));
            $url->value('http://');
            $do->value('insert');
            $_action = 'insert';
        }
        
        if(empty($action))
        {
            $action = $_action;
        }
        
        /** 给表单增加规则 */
        if('insert' == $action || 'update' == $action)
        {
            $screenName->addRule(array($this, 'screenNameExists'), _t('昵称已经存在'));
            $url->addRule('url', _t('个人主页地址格式错误'));
            $mail->addRule('required', _t('必须填写电子邮箱'));
            $mail->addRule(array($this, 'mailExists'), _t('电子邮箱地址已经存在'));
            $mail->addRule('email', _t('电子邮箱格式错误'));
            $confirm->addRule('confirm', _t('两次输入的密码不一致'), 'password');
        }
        
        if('insert' == $action)
        {
            $name->addRule('required', _t('必须填写用户名称'));
            $name->addRule(array($this, 'nameExists'), _t('用户名称已经存在'));
            $password->label(_t('用户密码*'));
            $confirm->label(_t('用户密码确认*'));
            $password->addRule('required', _t('必须填写密码'));
        }
        
        if('update' == $action)
        {
            $name->input->setAttribute('disabled', 'disabled');
            $uid->addRule('required', _t('用户主键不存在'));
            $uid->addRule(array($this, 'userExists'), _t('用户不存在'));
        }
        
        return $form;
    }
    
    /**
     * 增加用户
     * 
     * @access public
     * @return void
     */
    public function insertUser()
    {
        try
        {
            $this->form('insert')->validate();
        }
        catch(Typecho_Widget_Exception $e)
        {
            Typecho_API::goBack('#edit');
        }
        
        /** 取出数据 */
        $user = Typecho_Request::getParametersFrom('name', 'mail', 'screenName', 'password', 'url', 'group');
        $user['screenName'] = empty($user['screenName']) ? $user['name'] : $user['screenName'];
        $user['password'] = md5($user['password']);
        $user['created'] = $this->options->gmtTime;
    
        /** 插入数据 */
        $user['uid'] = $this->insert($user);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("用户 '%s' 已经被增加", $user['screenName']), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('users.php', $this->options->adminUrl));
    }
    
    /**
     * 更新用户
     * 
     * @access public
     * @return void
     */
    public function updateUser()
    {
        try
        {
            $this->form('update')->validate();
        }
        catch(Typecho_Widget_Exception $e)
        {
            Typecho_API::goBack('#edit');
        }
    
        /** 取出数据 */
        $user = Typecho_Request::getParametersFrom('mail', 'screenName', 'password', 'url', 'group');
        $user['screenName'] = empty($user['screenName']) ? $user['name'] : $user['screenName'];
        if(empty($user['password']))
        {
            unset($user['password']);
        }
        else
        {
            $user['password'] = md5($user['password']);
        }
    
        /** 更新数据 */
        $this->update($user, $this->db->sql()->where('uid = ?', Typecho_Request::getParameter('uid')));
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("用户 '%s' 已经被更新", $user['screenName']), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('users.php', $this->options->adminUrl));
    }
    
    /**
     * 删除用户
     * 
     * @access public
     * @return void
     */
    public function deleteUser()
    {
        $users = Typecho_Request::getParameter('uid');
        $deleteCount = 0;
        
        if($users && is_array($users))
        {
            foreach($users as $user)
            {
                if(1 == $user)
                {
                    continue;
                }
                
                if($this->delete($this->db->sql()->where('uid = ?', $user)))
                {
                    $deleteCount ++;
                }
            }
        }
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set($deleteCount > 0 ? _t('用户已经删除') : _t('没有用户被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('users.php', $this->options->adminUrl));
    }
    
    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_API::factory('Widget_Users_Current')->pass('editor');
        Typecho_Request::bindParameter(array('do' => 'insert'), array($this, 'insertUser'));
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateUser'));
        Typecho_Request::bindParameter(array('do' => 'delete'), array($this, 'deleteUser'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
