<?php
/**
 * 标签编辑
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 标签编辑组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Tag_Edit extends Widget_Abstract_Metas implements Widget_Interface_DoWidget
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
     * 判断标签是否存在
     * 
     * @access public
     * @param integer $mid 标签主键
     * @return boolean
     */
    public function tagExists($mid)
    {
        $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'tag')
        ->where('`mid` = ?', $mid)->limit(1));
        
        return $tag ? true : false;
    }
    
    /**
     * 判断标签名称是否存在
     * 
     * @access public
     * @param string $name 标签名称
     * @return boolean
     */
    public function tagNameExists($name)
    {
        $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'tag')
        ->where('`name` = ?', $name)->limit(1));
        
        return $tag ? true : false;
    }
    
    /**
     * 判断标签名称是否存在
     * 
     * @access public
     * @param string $name 标签名称
     * @return boolean
     */
    public function nameExists($name)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'tag')
        ->where('`name` = ?', $name)
        ->limit(1);
        
        if(Typecho_Request::getParameter('mid'))
        {
            $select->where('`mid` <> ?', Typecho_Request::getParameter('mid'));
        }
    
        $tag = $this->db->fetchRow($select);
        return $tag ? false : true;
    }
    
    /**
     * 判断标签缩略名是否存在
     * 
     * @access public
     * @param string $slug 缩略名
     * @return boolean
     */
    public function slugExists($slug)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'tag')
        ->where('`slug` = ?', $slug)
        ->limit(1);
        
        if(Typecho_Request::getParameter('mid'))
        {
            $select->where('`mid` <> ?', Typecho_Request::getParameter('mid'));
        }
    
        $tag = $this->db->fetchRow($select);
        return $tag ? false : true;
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
        $form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Metas/Tag/Edit.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 创建标题 */
        $title = new Typecho_Widget_Helper_Layout('h4');
        $form->addItem($title->setAttribute('id', 'edit'));
        
        /** 标签名称 */
        $name = new Typecho_Widget_Helper_Form_Text('name', NULL, _t('标签名称*'), _t('这是标签在站点中显示的名称.'));
        $name->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($name);
        
        /** 标签缩略名 */
        $slug = new Typecho_Widget_Helper_Form_Text('slug', NULL, _t('标签缩略名'), _t('标签缩略名用于创建友好的链接形式,如果留空则默认使用标签名称.'));
        $slug->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($slug);
        
        /** 标签动作 */
        $do = new Typecho_Widget_Helper_Form_Hidden('do');
        $form->addInput($do);
        
        /** 标签主键 */
        $mid = new Typecho_Widget_Helper_Form_Hidden('mid');
        $form->addInput($mid);
        
        /** 空格 */
        $form->addItem(new Typecho_Widget_Helper_Layout('hr', array('class' => 'space')));
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Submit();
        $submit->button->setAttribute('class', 'submit');
        $form->addItem($submit->setAttribute('class', 'table_nav'));

        if(NULL != Typecho_Request::getParameter('mid'))
        {
            /** 更新模式 */
            $meta = $this->db->fetchRow($this->select()
            ->where('`mid` = ?', Typecho_Request::getParameter('mid'))
            ->where('`type` = ?', 'tag')->limit(1));
            
            if(!$meta)
            {
                throw new Typecho_Widget_Exception(_t('标签不存在'), Typecho_Exception::NOTFOUND);
            }
            
            $name->value($meta['name']);
            $slug->value($meta['slug']);
            $do->value('update');
            $mid->value($meta['mid']);
            $submit->value(_t('编辑标签'));
            $title->html(_t('编辑标签'));
            $_action = 'update';
        }
        else
        {
            $do->value('insert');
            $submit->value(_t('增加标签'));
            $title->html(_t('增加标签'));
            $_action = 'insert';
        }
        
        if(empty($action))
        {
            $action = $_action;
        }
        
        /** 给表单增加规则 */
        if('insert' == $action || 'update' == $action)
        {
            $name->addRule('required', _t('必须填写标签名称'));
            $name->addRule(array($this, 'nameExists'), _t('标签名称已经存在'));
            $slug->addRule(array($this, 'slugExists'), _t('缩略名已经存在'));
        }
        
        if('update' == $action)
        {
            $mid->addRule('required', _t('标签主键不存在'));
            $mid->addRule(array($this, 'tagExists'), _t('标签不存在'));
        }
        
        return $form;
    }
    
    /**
     * 插入标签
     * 
     * @access public
     * @return void
     */
    public function insertTag()
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
        $tag = Typecho_Request::getParametersFrom('name', 'slug');
        $tag['type'] = 'tag';
        $tag['slug'] = empty($tag['slug']) ? $tag['name'] : $tag['slug'];
    
        /** 插入数据 */
        $tag['mid'] = $this->insert($tag);
        $this->push($tag);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("标签 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被增加",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-tag.php', $this->options->adminUrl));
    }
    
    /**
     * 更新标签
     * 
     * @access public
     * @return void
     */
    public function updateTag()
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
        $tag = Typecho_Request::getParametersFrom('name', 'slug', 'mid');
        $tag['type'] = 'tag';
        $tag['slug'] = empty($tag['slug']) ? $tag['name'] : $tag['slug'];
    
        /** 更新数据 */
        $this->update($tag, $this->db->sql()->where('mid = ?', Typecho_Request::getParameter('mid')));
        $this->push($tag);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("标签 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被更新",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-tag.php', $this->options->adminUrl));
    }
    
    /**
     * 删除标签
     * 
     * @access public
     * @return void
     */
    public function deleteTag()
    {
        $tags = Typecho_Request::getParameter('mid');
        $deleteCount = 0;
        
        if($tags && is_array($tags))
        {
            foreach($tags as $tag)
            {
                if($this->delete($this->db->sql()->where('mid = ?', $tag)))
                {
                    $this->db->query($this->db->sql()->delete('table.relationships')->where('`mid` = ?', $tag));
                    $deleteCount ++;
                }
            }
        }

        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set($deleteCount > 0 ? _t('标签已经删除') : _t('没有标签被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-tag.php', $this->options->adminUrl));
    }
    
    /**
     * 合并标签
     * 
     * @access public
     * @return void
     */
    public function mergeTag()
    {
        /** 验证数据 */
        $validator = new Typecho_Validate();
        $validator->addRule('merge', 'required', _t('合并入的标签不存在'));
        $validator->addRule('merge', array($this, 'tagNameExists'), _t('合并入的标签不存在'));
        
        /** 设置提示信息 */
        try
        {
            $validator->run(Typecho_Request::getParametersFrom('merge'));
        }
        catch(Typecho_Validate_Exception $e)
        {
            Typecho_API::factory('Widget_Notice')->set($e->getMessages(), NULL, 'error');
            Typecho_API::goBack();
        }
        
        $merge = $this->db->fetchObject($this->db->sql()->select('table.metas', '`mid`')->where('`type` = ?', 'tag')
        ->where('`name` = ?', Typecho_Request::getParameter('merge'))->limit(1))->mid;
        $tags = Typecho_Request::getParameter('mid');
        
        if($tags && is_array($tags))
        {
            $this->merge($merge, 'tag', $tags);
            
            /** 提示信息 */
            Typecho_API::factory('Widget_Notice')->set(_t('标签已经合并'), NULL, 'success');
        }
        else
        {
            Typecho_API::factory('Widget_Notice')->set(_t('没有选择任何标签'), NULL, 'success');
        }
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-tag.php', $this->options->adminUrl));
    }

    /**
     * 入口函数,绑定事件
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_Request::bindParameter(array('do' => 'insert'), array($this, 'insertTag'));
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateTag'));
        Typecho_Request::bindParameter(array('do' => 'delete'), array($this, 'deleteTag'));
        Typecho_Request::bindParameter(array('do' => 'merge'), array($this, 'mergeTag'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
