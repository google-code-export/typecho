<?php
/**
 * 编辑链接
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑链接组件
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Link_Edit extends Widget_Abstract_Metas implements Widget_Interface_Action_Widget
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
        Typecho_API::factory('Widget_Users_Current')->pass('editor');
    }
    
    /**
     * 判断链接是否存在
     * 
     * @access public
     * @param integer $mid 链接主键
     * @return boolean
     */
    public function linkExists($mid)
    {
        $link = $this->db->fetchRow($this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'link')
        ->where('`mid` = ?', $mid)->limit(1));
        
        return $link ? true : false;
    }
    
    /**
     * 判断链接名称是否存在
     * 
     * @access public
     * @param string $name 链接名称
     * @return boolean
     */
    public function nameExists($name)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'link')
        ->where('`name` = ?', $name)
        ->limit(1);
        
        if(Typecho_Request::getParameter('mid'))
        {
            $select->where('`mid` <> ?', Typecho_Request::getParameter('mid'));
        }
    
        $link = $this->db->fetchRow($select);
        return $link ? false : true;
    }
    
    /**
     * 判断链接缩略名是否存在
     * 
     * @access public
     * @param string $slug 缩略名
     * @return boolean
     */
    public function slugExists($slug)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'link')
        ->where('`slug` = ?', $slug)
        ->limit(1);
        
        if(Typecho_Request::getParameter('mid'))
        {
            $select->where('`mid` <> ?', Typecho_Request::getParameter('mid'));
        }
    
        $link = $this->db->fetchRow($select);
        return $link ? false : true;
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
        $form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Metas/Link/Edit.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 创建标题 */
        $title = new Typecho_Widget_Helper_Layout('h4');
        $form->addItem($title->setAttribute('id', 'edit'));
        
        /** 链接名称 */
        $name = new Typecho_Widget_Helper_Form_Text('name', NULL, _t('链接名称*'));
        $name->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($name);
        
        /** 链接缩略名 */
        $slug = new Typecho_Widget_Helper_Form_Text('slug', NULL, _t('链接地址*'), _t('此链接的网址,请用<strong>http://</strong>开头.'));
        $slug->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($slug);
        
        /** 链接描述 */
        $description =  new Typecho_Widget_Helper_Form_Textarea('description', NULL, _t('链接描述'), _t('用简短的语言描述此链接,在某些模板中它将被显示.'));
        $description->input->setAttribute('rows', 5)->setAttribute('style', 'width:80%');
        $form->addInput($description);
        
        /** 链接动作 */
        $do = new Typecho_Widget_Helper_Form_Hidden('do');
        $form->addInput($do);
        
        /** 链接主键 */
        $mid = new Typecho_Widget_Helper_Form_Hidden('mid');
        $form->addInput($mid);
        
        /** 空格 */
        $form->addItem(new Typecho_Widget_Helper_Layout('hr', array('class' => 'space')));
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Submit();
        $submit->button->setAttribute('class', 'submit');
        $form->addItem($submit);

        if(NULL != Typecho_Request::getParameter('mid'))
        {
            /** 更新模式 */
            $meta = $this->db->fetchRow($this->select()
            ->where('`mid` = ?', Typecho_Request::getParameter('mid'))
            ->where('`type` = ?', 'link')->limit(1));
            
            if(!$meta)
            {
                throw new Typecho_Widget_Exception(_t('链接不存在'), Typecho_Exception::NOTFOUND);
            }
            
            $name->value($meta['name']);
            $slug->value($meta['slug']);
            $description->value($meta['description']);
            $do->value('update');
            $mid->value($meta['mid']);
            $submit->value(_t('编辑链接'));
            $title->html(_t('编辑链接'));
            $_action = 'update';
        }
        else
        {
            $slug->value('http://');
            $do->value('insert');
            $submit->value(_t('增加链接'));
            $title->html(_t('增加链接'));
            $_action = 'insert';
        }
        
        if(empty($action))
        {
            $action = $_action;
        }
        
        /** 给表单增加规则 */
        if('insert' == $action || 'update' == $action)
        {
            $name->addRule('required', _t('必须填写链接名称'));
            $name->addRule(array($this, 'nameExists'), _t('链接名称已经存在'));
            $slug->addRule('required', _t('必须填写链接地址'));
            $slug->addRule('url', _t('链接地址格式错误'));
            $slug->addRule(array($this, 'slugExists'), _t('缩略名已经存在'));
        }
        
        if('update' == $action)
        {
            $mid->addRule('required', _t('链接主键不存在'));
            $mid->addRule(array($this, 'linkExists'), _t('链接不存在'));
        }
        
        return $form;
    }
    
    /**
     * 增加链接
     * 
     * @access public
     * @return void
     */
    public function insertLink()
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
        $link = Typecho_Request::getParametersFrom('name', 'slug', 'description');
        $link['slug'] = $link['slug'];
        $link['type'] = 'link';
        $link['sort'] = $this->db->fetchObject($this->db->sql()->select('table.metas', 'MAX(`sort`) AS `maxSort`')
        ->where('`type` = ?', 'link'))->maxSort + 1;
    
        /** 插入数据 */
        $link['mid'] = $this->insert($link);
        $this->push($link);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("链接 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被增加",
        $this->slug, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-links.php', $this->options->adminUrl));
    }
    
    /**
     * 更新链接
     * 
     * @access public
     * @return void
     */
    public function updateLink()
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
        $link = Typecho_Request::getParametersFrom('name', 'slug', 'description');
        $link['slug'] = $link['slug'];
        $link['type'] = 'link';
    
        /** 更新数据 */
        $this->update($link, $this->db->sql()->where('mid = ?', Typecho_Request::getParameter('mid')));
        $link['mid'] = Typecho_Request::getParameter('mid');
        $this->push($link);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("链接 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被更新",
        $this->slug, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-links.php', $this->options->adminUrl));
    }
    
    /**
     * 删除链接
     * 
     * @access public
     * @return void
     */
    public function deleteLink()
    {
        $links = Typecho_Request::getParameter('mid');
        $deleteCount = 0;
        
        if($links && is_array($links))
        {
            foreach($links as $link)
            {
                if($this->delete($this->db->sql()->where('mid = ?', $link)))
                {
                    $deleteCount ++;
                }
            }
        }
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set($deleteCount > 0 ? _t('链接已经删除') : _t('没有链接被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-links.php', $this->options->adminUrl));
    }
    
    /**
     * 链接排序
     * 
     * @access public
     * @return void
     */
    public function sortLink()
    {
        $links = Typecho_Request::getParameter('sort');
        if($links && is_array($links))
        {
            $this->sort($links, 'link');
        }
        
        if(!Typecho_Request::isAjax())
        {
            /** 转向原页 */
            Typecho_API::redirect(Typecho_API::pathToUrl('manage-links.php', $this->options->adminUrl));
        }
        else
        {
            Typecho_API::throwAjaxResponse(_t('链接排序已经完成'), $this->options->charset);
        }
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
        Typecho_Request::bindParameter(array('do' => 'insert'), array($this, 'insertLink'));
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateLink'));
        Typecho_Request::bindParameter(array('do' => 'delete'), array($this, 'deleteLink'));
        Typecho_Request::bindParameter(array('do' => 'sort'), array($this, 'sortLink'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
