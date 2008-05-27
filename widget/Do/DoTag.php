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
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Metas.php';

/**
 * 描述数据提交管理组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class DoTagWidget extends MetasWidget
{
    /**
     * 验证数据
     * 
     * @access private
     * @param array $data 需要验证的数据
     * @param boolean $isUpdate 是否为更新数据
     * @return void
     */
    private function validate(array $data, $isUpdate = false)
    {
        /** 验证数据 */
        $validator = new TypechoValidation($this);
        $validator->addRule('name', 'required', _t('必须填写标签名称'));
        $validator->addRule('name', 'nameExists', _t('标签名称已经存在'));
        $validator->addRule('slug', 'slugExists', _t('缩略名已经存在'));
        
        if($isUpdate)
        {
            $validator->addRule('mid', 'required', _t('标签主键不存在'));
            $validator->addRule('mid', 'tagExists', _t('标签不存在'));
        }
        
        try
        {
            $validator->run($data);
        }
        catch(TypechoValidationException $e)
        {
            unset($data['mid']);
            
            /** 记录cookie */
            TypechoRequest::setCookie('tag', $data);
            
            /** 设置提示信息 */
            Typecho::widget('Notice')->set($e->getMessages(), NULL, 'detail');
            $this->goBack('#edit');
        }
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
        
        if(TypechoRequest::getParameter('mid'))
        {
            $select->where('`mid` <> ?', TypechoRequest::getParameter('mid'));
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
        
        if(TypechoRequest::getParameter('mid'))
        {
            $select->where('`mid` <> ?', TypechoRequest::getParameter('mid'));
        }
    
        $tag = $this->db->fetchRow($select);
        return $tag ? false : true;
    }

    /**
     * 插入标签
     * 
     * @access public
     * @return void
     */
    public function insertTag()
    {
        /** 取出数据 */
        $tag = TypechoRequest::getParametersFrom('name', 'slug');
        $tag['type'] = 'tag';
        $tag['slug'] = $tag['name'];
        
        /** 验证数据 */
        $this->validate($tag);
    
        /** 插入数据 */
        $tag['mid'] = $this->insertMeta($tag, false);
        $this->push($tag);
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("标签 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被增加",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-tag.php', Typecho::widget('Options')->adminUrl));
    }
    
    /**
     * 更新标签
     * 
     * @access public
     * @return void
     */
    public function updateTag()
    {
        /** 取出数据 */
        $tag = TypechoRequest::getParametersFrom('name', 'slug', 'mid');
        $tag['type'] = 'tag';
        $tag['slug'] = $tag['name'];
        
        /** 验证数据 */
        $this->validate($tag, true);
    
        /** 更新数据 */
        $this->updateMeta($tag, $tag['mid'], 'tag');
        $this->push($tag);
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("标签 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被更新",
        $this->permalink, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-tag.php', Typecho::widget('Options')->adminUrl));
    }
    
    /**
     * 删除标签
     * 
     * @access public
     * @return void
     */
    public function deleteTag()
    {
        $tags = TypechoRequest::getParameter('mid');
        $deleteCount = 0;
        
        if($tags && is_array($tags))
        {
            foreach($tags as $tag)
            {
                if($this->deleteMeta($tag, 'tag'))
                {
                    $deleteCount ++;
                }
            }
        }

        /** 提示信息 */
        Typecho::widget('Notice')->set($deleteCount > 0 ? _t('标签已经删除') : _t('没有标签被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-tag.php', Typecho::widget('Options')->adminUrl));
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
        $validator = new TypechoValidation($this);
        $validator->addRule('merge', 'required', _t('合并入的标签不存在'));
        $validator->addRule('merge', 'tagNameExists', _t('合并入的标签不存在'));
        
        /** 设置提示信息 */
        try
        {
            $validator->run(TypechoRequest::getParametersFrom('merge'));
        }
        catch(TypechoValidationException $e)
        {
            Typecho::widget('Notice')->set($e->getMessages(), NULL, 'error');
            $this->goBack();
        }
        
        $merge = $this->db->fetchObject($this->db->sql()->select('table.metas', '`mid`')->where('`type` = ?', 'tag')
        ->where('`name` = ?', TypechoRequest::getParameter('merge'))->limit(1))->mid;
        $tags = TypechoRequest::getParameter('mid');
        
        if($tags && is_array($tags))
        {
            $this->mergeMeta($merge, 'tag', $tags);
        }
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t('标签已经合并'), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-tag.php', Typecho::widget('Options')->adminUrl));
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
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertTag'));
        TypechoRequest::bindParameter(array('do' => 'update'), array($this, 'updateTag'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deleteTag'));
        TypechoRequest::bindParameter(array('do' => 'merge'), array($this, 'mergeTag'));
        Typecho::redirect(Typecho::widget('Options')->adminUrl);
    }
}
