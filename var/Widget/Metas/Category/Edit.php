<?php
/**
 * 编辑分类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑分类组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Category_Edit extends Widget_Abstract_Metas implements Widget_Interface_Do
{
    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 编辑以上权限 */
        $this->user->pass('editor');
    }
    
    /**
     * 判断分类是否存在
     * 
     * @access public
     * @param integer $mid 分类主键
     * @return boolean
     */
    public function categoryExists($mid)
    {
        $category = $this->db->fetchRow($this->db->sql()->select('table.metas')
        ->where('type = ?', 'category')
        ->where('mid = ?', $mid)->limit(1));
        
        return $category ? true : false;
    }
    
    /**
     * 判断分类名称是否存在
     * 
     * @access public
     * @param string $name 分类名称
     * @return boolean
     */
    public function nameExists($name)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('type = ?', 'category')
        ->where('name = ?', $name)
        ->limit(1);
        
        if (Typecho_Request::getParameter('mid')) {
            $select->where('mid <> ?', Typecho_Request::getParameter('mid'));
        }
    
        $category = $this->db->fetchRow($select);
        return $category ? false : true;
    }
    
    /**
     * 判断分类缩略名是否存在
     * 
     * @access public
     * @param string $slug 缩略名
     * @return boolean
     */
    public function slugExists($slug)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('type = ?', 'category')
        ->where('slug = ?', $slug)
        ->limit(1);
        
        if (Typecho_Request::getParameter('mid')) {
            $select->where('mid <> ?', Typecho_Request::getParameter('mid'));
        }
    
        $category = $this->db->fetchRow($select);
        return $category ? false : true;
    }
    
    /**
     * 生成表单
     * 
     * @access public
     * @param string $action 表单动作
     * @return Typecho_Widget_Helper_Form_Element
     */
    public function form($action = NULL)
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/Metas/Category/Edit.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 分类名称 */
        $name = new Typecho_Widget_Helper_Form_Element_Text('name', NULL, NULL, _t('分类名称*'));
        $form->addInput($name);
        
        /** 分类缩略名 */
        $slug = new Typecho_Widget_Helper_Form_Element_Text('slug', NULL, NULL, _t('分类缩略名'),
        _t('分类缩略名用于创建友好的链接形式,建议使用字母,数字,下划线和横杠.'));
        $form->addInput($slug);
        
        /** 分类顺序 */
        $sort = new Typecho_Widget_Helper_Form_Element_Text('sort', NULL, NULL, _t('分类顺序'),
        _t('请填入一个数字以表示分类在列表中显示的顺序,如果没有特殊要求请留空'));
        $form->addInput($sort);
        
        /** 分类描述 */
        $description =  new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, NULL,
        _t('分类描述'), _t('此文字用于描述分类,在有的主题中它会被显示.'));
        $form->addInput($description);
        
        /** 分类动作 */
        $do = new Typecho_Widget_Helper_Form_Element_Hidden('do');
        $form->addInput($do);
        
        /** 分类主键 */
        $mid = new Typecho_Widget_Helper_Form_Element_Hidden('mid');
        $form->addInput($mid);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit();
        $form->addItem($submit);

        if ($this->request->mid) {
            /** 更新模式 */
            $meta = $this->db->fetchRow($this->select()
            ->where('mid = ?', $this->request->mid)
            ->where('type = ?', 'category')->limit(1));
            
            if (!$meta) {
                throw new Typecho_Widget_Exception(_t('分类不存在'), 404);
            }
            
            $name->value($meta['name']);
            $slug->value($meta['slug']);
            $sort->value($meta['sort']);
            $description->value($meta['description']);
            $do->value('update');
            $mid->value($meta['mid']);
            $submit->value(_t('编辑分类'));
            $_action = 'update';
        } else {
            $do->value('insert');
            $submit->value(_t('增加分类'));
            $_action = 'insert';
        }
        
        if (empty($action)) {
            $action = $_action;
        }
        
        /** 给表单增加规则 */
        if ('insert' == $action || 'update' == $action) {
            $name->addRule('required', _t('必须填写分类名称'));
            $name->addRule(array($this, 'nameExists'), _t('分类名称已经存在'));
            $slug->addRule(array($this, 'slugExists'), _t('缩略名已经存在'));
        }
        
        if ('update' == $action) {
            $mid->addRule('required', _t('分类主键不存在'));
            $mid->addRule(array($this, 'categoryExists'), _t('分类不存在'));
        }
        
        return $form;
    }
    
    /**
     * 增加分类
     * 
     * @access public
     * @return void
     */
    public function insertCategory()
    {
        try {
            $this->form('insert')->validate();
        } catch (Typecho_Widget_Exception $e) {
            Typecho_API::goBack('#edit');
        }
        
        /** 取出数据 */
        $category = Typecho_Request::getParametersFrom('name', 'slug', 'description');
        $category['slug'] = Typecho_API::slugName(empty($category['slug']) ? $category['name'] : $category['slug']);
        $category['type'] = 'category';
        $category['sort'] = $this->db->fetchObject($this->db->sql()->select('table.metas', 'MAX(sort) AS maxSort')
        ->where('type = ?', 'category'))->maxSort + 1;
    
        /** 插入数据 */
        $category['mid'] = $this->insert($category);
        $this->push($category);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("分类 '<a href=\"%s\">%s</a>' 已经被增加",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-cat.php', $this->options->adminUrl));
    }
    
    /**
     * ajax增加分类
     * 
     * @access public
     * @return void
     */
    public function ajaxInsertCategory()
    {
        try {
            $this->form('insert')->validate();
        } catch (Typecho_Widget_Exception $e) {
            Typecho_API::throwAjaxResponse(implode(',', $e->getMessages()));
        }
        
        /** 取出数据 */
        $category = Typecho_Request::getParametersFrom('name');
        $category['slug'] = Typecho_API::slugName($category['name']);
        $category['type'] = 'category';
        $category['description'] = $category['name'];
        $category['sort'] = $this->db->fetchObject($this->db->sql()->select('table.metas', 'MAX(sort) AS maxSort')
        ->where('type = ?', 'category'))->maxSort + 1;
    
        /** 插入数据 */
        Typecho_API::throwAjaxResponse($this->insert($category), $this->options->charset);
    }
    
    /**
     * 更新分类
     * 
     * @access public
     * @return void
     */
    public function updateCategory()
    {
        try {
            $this->form('update')->validate();
        } catch (Typecho_Widget_Exception $e) {
            Typecho_API::goBack('#edit');
        }
    
        /** 取出数据 */
        $category = Typecho_Request::getParametersFrom('name', 'slug', 'description');
        $category['slug'] = Typecho_API::slugName(empty($category['slug']) ? $category['name'] : $category['slug']);
        $category['type'] = 'category';
    
        /** 更新数据 */
        $this->update($category, $this->db->sql()->where('mid = ?', Typecho_Request::getParameter('mid')));
        $category['mid'] = Typecho_Request::getParameter('mid');
        $this->push($category);
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("分类 '<a href=\"%s\">%s</a>' 已经被更新",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-cat.php', $this->options->adminUrl));
    }
    
    /**
     * 删除分类
     * 
     * @access public
     * @return void
     */
    public function deleteCategory()
    {
        $categories = Typecho_Request::getParameter('mid');
        $deleteCount = 0;
        
        if ($categories && is_array($categories)) {
            foreach ($categories as $category) {
                if ($this->delete($this->db->sql()->where('mid = ?', $category))) {
                    $this->db->query($this->db->sql()->delete('table.relationships')->where('mid = ?', $category));
                    $deleteCount ++;
                }
            }
        }
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set($deleteCount > 0 ? _t('分类已经删除') : _t('没有分类被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-cat.php', $this->options->adminUrl));
    }
    
    /**
     * 合并分类
     * 
     * @access public
     * @return void
     */
    public function mergeCategory()
    {
        /** 验证数据 */
        $validator = new Typecho_Validate($this);
        $validator->addRule('merge', 'required', _t('分类主键不存在'));
        $validator->addRule('merge', 'categoryExists', _t('请选择需要合并的分类'));
        
        try {
            $validator->run(Typecho_Request::getParametersFrom('merge'));
        } catch (Typecho_Validate_Exception $e) {
            Typecho_API::factory('Widget_Notice')->set($e->getMessages(), NULL, 'error');
            Typecho_API::goBack();
        }
        
        $merge = Typecho_Request::getParameter('merge');
        $categories = Typecho_Request::getParameter('mid');
        
        if ($categories && is_array($categories)) {
            $this->merge($merge, 'category', $categories);
            
            /** 提示信息 */
            Typecho_API::factory('Widget_Notice')->set(_t('分类已经合并'), NULL, 'success');
        } else {
            Typecho_API::factory('Widget_Notice')->set(_t('没有选择任何分类'), NULL, 'notice');
        }
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-cat.php', $this->options->adminUrl));
    }
    
    /**
     * 分类排序
     * 
     * @access public
     * @return void
     */
    public function sortCategory()
    {
        $categories = Typecho_Request::getParameter('sort');
        if ($categories && is_array($categories)) {
            $this->sort($categories, 'category');
        }
        
        if (!Typecho_Request::isAjax()) {
            /** 转向原页 */
            Typecho_API::redirect(Typecho_API::pathToUrl('manage-cat.php', $this->options->adminUrl));
        } else {
            Typecho_API::throwAjaxResponse(_t('分类排序已经完成'), $this->options->charset);
        }
    }
    
    /**
     * 设置默认分类
     * 
     * @access public
     * @return void
     */
    public function defaultCategory()
    {
        /** 验证数据 */
        $validator = new Typecho_Validate($this);
        $validator->addRule('mid', 'required', _t('分类主键不存在'));
        $validator->addRule('mid', array($this, 'categoryExists'), _t('分类不存在'));
        $validator->run(Typecho_Request::getParametersFrom('mid'));
        
        $this->options->update(array('value' => Typecho_Request::getParameter('mid')),
        $this->db->sql()->where('name = ?', 'defaultCategory'));
        
        $this->db->fetchRow($this->select()->where('mid = ?', Typecho_Request::getParameter('mid'))
        ->where('type = ?', 'category')->limit(1), array($this, 'push'));
        
        /** 提示信息 */
        Typecho_API::factory('Widget_Notice')->set(_t("'<a href=\"%s\">%s</a>' 已经被设为默认分类",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho_API::redirect(Typecho_API::pathToUrl('manage-cat.php', $this->options->adminUrl));
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
        Typecho_Request::bindParameter(array('do' => 'insert'), array($this, 'insertCategory'));
        Typecho_Request::bindParameter(array('do' => 'ajaxInsert'), array($this, 'ajaxInsertCategory'));
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateCategory'));
        Typecho_Request::bindParameter(array('do' => 'delete'), array($this, 'deleteCategory'));
        Typecho_Request::bindParameter(array('do' => 'merge'), array($this, 'mergeCategory'));
        Typecho_Request::bindParameter(array('do' => 'sort'), array($this, 'sortCategory'));
        Typecho_Request::bindParameter(array('do' => 'default'), array($this, 'defaultCategory'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
