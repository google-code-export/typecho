<?php
/**
 * 描述数据提交管理
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/** 载入提交基类支持 **/
require_once 'DoPost.php';

/**
 * 描述数据提交管理组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class DoEditCategoryWidget extends DoPostWidget
{
    /**
     * 验证数据
     * 
     * @access private
     * @param boolean $isUpdate 是否为更新数据
     * @return void
     */
    private function validate($isUpdate = false)
    {
        /** 验证数据 */
        $validator = new TypechoValidation($this);
        $validator->addRule('name', 'required', _t('必须填写分类名称'));
        $validator->addRule('name', 'nameExists', _t('分类名称已经存在'));
        $validator->addRule('slug', 'required', _t('必须填写分类缩略名'));
        $validator->addRule('slug', 'alphaDash', _t('分类缩略名只能使用字母,数字,下划线和横杠'));
        $validator->addRule('slug', 'slugExists', _t('缩略名已经存在'));
        
        if($isUpdate)
        {
            $validator->addRule('mid', 'required', _t('分类主键不存在'));
            $validator->addRule('mid', 'categoryExists', _t('分类不存在'));
        }
        
        try
        {
            $validator->run(TypechoRequest::getParametersFrom('name', 'slug', 'mid'));
        }
        catch(TypechoValidationException $e)
        {
            /** 记录cookie */
            TypechoRequest::setCookie('category', TypechoRequest::getParametersFrom('name', 'slug', 'description', 'mid'));
            
            /** 设置提示信息 */
            Typecho::widget('Notice')->set($e->getMessages(), NULL, 'detail');
            $this->goBack('#edit');
        }
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
        ->where('`type` = ?', 'category')
        ->where('`mid` = ?', $mid)->limit(1));
        
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
        ->where('`type` = ?', 'category')
        ->where('`name` = ?', $name)
        ->limit(1);
        
        if(TypechoRequest::getParameter('mid'))
        {
            $select->where('`mid` <> ?', TypechoRequest::getParameter('mid'));
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
        ->where('`type` = ?', 'category')
        ->where('`slug` = ?', $slug)
        ->limit(1);
        
        if(TypechoRequest::getParameter('mid'))
        {
            $select->where('`mid` <> ?', TypechoRequest::getParameter('mid'));
        }
    
        $category = $this->db->fetchRow($select);
        return $category ? false : true;
    }

    /**
     * 插入分类
     * 
     * @access public
     * @return void
     */
    public function insertCategory()
    {
        /** 验证数据 */
        $this->validate();
    
        /** 取出最大的排序 */
        $sort = $this->db->fetchObject($this->db->sql()->select('table.metas', 'COUNT(`sort`) AS `maxSort`')
        ->where('`type` = ?', 'category'))->maxSort + 1;
    
        /** 插入数据 */
        $this->db->query($this->db->sql()->insert('table.metas')
        ->rows(TypechoRequest::getParametersFrom('name', 'slug', 'description'))
        ->row('type', "'category'")
        ->row('sort', $sort));
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("分类 '%s' 已经被增加", TypechoRequest::getParameter('name')), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-cat.php', Typecho::widget('Options')->adminUrl));
    }
    
    /**
     * 更新分类
     * 
     * @access public
     * @return void
     */
    public function updateCategory()
    {
        /** 验证数据 */
        $this->validate(true);
    
        /** 更新数据 */
        $this->db->query($this->db->sql()->update('table.metas')
        ->rows(TypechoRequest::getParametersFrom('name', 'slug', 'description'))
        ->where('`mid` = ?', TypechoRequest::getParameter('mid'))
        ->where('`type` = ?', 'category'));
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("分类 '%s' 已经被更新", TypechoRequest::getParameter('name')), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-cat.php', Typecho::widget('Options')->adminUrl));
    }
    
    /**
     * 删除分类
     * 
     * @access public
     * @return void
     */
    public function deleteCategory()
    {
        $categories = TypechoRequest::getParameter('mid');
        
        if($categories && is_array($categories))
        {
            foreach($categories as $category)
            {
                $this->db->query($this->db->sql()->delete('table.metas')->where('`mid` = ?', $category));
                $this->db->query($this->db->sql()->delete('table.relationships')->where('`mid` = ?', $category));
            }
        }
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("分类已经删除"), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-cat.php', Typecho::widget('Options')->adminUrl));
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
        $validator = new TypechoValidation($this);
        $validator->addRule('merge', 'required', _t('分类主键不存在'));
        $validator->addRule('merge', 'categoryExists', _t('分类不存在'));
        $validator->run(TypechoRequest::getParametersFrom('merge'));
        
        $merge = TypechoRequest::getParameter('merge');
        $categories = TypechoRequest::getParameter('mid');
        $posts = Typecho::arrayFlatten($this->db->fetchAll($this->db->sql()->select('table.relationships', '`cid`')
        ->where('`mid` = ?', $merge)), 'cid');
        
        if($categories && is_array($categories))
        {
            foreach($categories as $category)
            {
                if($merge != $category)
                {
                    $existsPosts = Typecho::arrayFlatten($this->db->fetchAll($this->db->sql()->select('table.relationships', '`cid`')
                    ->where('`mid` = ?', $category)), 'cid');
                    
                    $this->db->query($this->db->sql()->delete('table.metas')->where('`mid` = ?', $category));
                    $this->db->query($this->db->sql()->delete('table.relationships')->where('`mid` = ?', $category));
                    
                    $diffPosts = array_diff($existsPosts, $posts);
                    foreach($diffPosts as $post)
                    {
                        $this->db->query($this->db->sql()->insert('table.relationships')
                        ->rows(array('mid' => $merge, 'cid' => $post)));
                    }
                }
            }
        }
        
        $num = $this->db->fetchObject($this->db->sql()
        ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
        ->where('table.relationships.`mid` = ?', $merge))->num;
        
        $this->db->query($this->db->sql()->update('table.metas')
        ->row('count', $num)
        ->where('`mid` = ?', $merge));
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("分类已经合并"), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-cat.php', Typecho::widget('Options')->adminUrl));
    }

    /**
     * 入口函数,绑定事件
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        Typecho::widget('Access')->pass('editor');
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertCategory'));
        TypechoRequest::bindParameter(array('do' => 'update'), array($this, 'updateCategory'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deleteCategory'));
        TypechoRequest::bindParameter(array('do' => 'merge'), array($this, 'mergeCategory'));
        Typecho::redirect(Typecho::widget('Options')->adminUrl);
    }
}
