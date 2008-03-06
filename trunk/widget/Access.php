<?php

class Access extends TypechoWidget
{
    private $_group;
    private $_user;

    public function __construct()
    {
        parent::__construct();
        $this->_group = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2,
            'subscriber'	=> 3,
            'visitor'		=> 4
        );
    }

    public function render()
    {
        return;
    }
    
    public function user($name)
    {
        if($this->hasLogin())
        {
            if(in_array($name, array('uid', 'group', 'name')))
            {
                return $_SESSION[$name];
            }
            else
            {
                if(empty($this->_user))
                {
                    $db = TypechoDb::get();
                    $this->_user = $db->fetchRow($db->sql()
                    ->select('table.user')
                    ->where('uid = ?', $_SESSION['uid']));
                }
                
                return isset($this->_user[$name]) ? $this->_user[$name] : NULL;
            }
        }
        else
        {
            return NULL;
        }
    }
    
    public function login($uid, $name, $group)
    {
        $_SESSION['uid'] = $uid;
        $_SESSION['name'] = $name;
        $_SESSION['group'] = $group;
    }
    
    public function logout()
    {
        session_unregister('id');
        session_unregister('name');
        session_unregister('group');
    }
    
    public function hasLogin()
    {
        return isset($_SESSION['uid']);
    }
    
    public function pass($group)
    {
        if($this->hasLogin() && array_key_exists($group, $this->_group)
        && $this->_group[$_SESSION['group']] <= $this->_group[$group])
        {
            return true;
        }
        
        throw new TypechoWidgetException(_t('禁止访问'), __TYPECHO_EXCEPTION_403__);
    }
}
