<?php
/**
 * 链接数据提交
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */
 
/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/** 载入提交基类支持 **/
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Metas.php';

/**
 * 链接数据提交组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class DoLinkWidget extends MetasWidget
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
        $validator->addRule('name', 'required', _t('必须填写链接名称'));
        $validator->addRule('name', 'nameExists', _t('链接名称已经存在'));
        $validator->addRule('slug', 'required', _t('必须填写链接地址'));
        $validator->addRule('slug', 'url', _t('链接地址格式错误'));
        $validator->addRule('slug', 'urlExists', _t('网址已经存在'));
        
        if($isUpdate)
        {
            $validator->addRule('mid', 'required', _t('链接主键不存在'));
            $validator->addRule('mid', 'linkExists', _t('链接不存在'));
        }
        
        try
        {
            $validator->run($data);
        }
        catch(TypechoValidationException $e)
        {
            unset($data['mid']);
            
            /** 记录cookie */
            TypechoRequest::setCookie('link', $data);
            
            /** 设置提示信息 */
            Typecho::widget('Notice')->set($e->getMessages(), NULL, 'detail');
            $this->goBack('#edit');
        }
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
    public function linkNameExists($name)
    {
        $link = $this->db->fetchRow($this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'link')
        ->where('`name` = ?', $name)->limit(1));
        
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
        
        if(TypechoRequest::getParameter('mid'))
        {
            $select->where('`mid` <> ?', TypechoRequest::getParameter('mid'));
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
    public function urlExists($url)
    {
        $select = $this->db->sql()->select('table.metas')
        ->where('`type` = ?', 'link')
        ->where('`slug` = ?', $url)
        ->limit(1);
        
        if(TypechoRequest::getParameter('mid'))
        {
            $select->where('`mid` <> ?', TypechoRequest::getParameter('mid'));
        }
    
        $link = $this->db->fetchRow($select);
        return $link ? false : true;
    }

    /**
     * 插入链接
     * 
     * @access public
     * @return void
     */
    public function insertLink()
    {
        /** 取出数据 */
        $link = TypechoRequest::getParametersFrom('name', 'slug', 'description');
        $link['type'] = 'link';
        
        /** 验证数据 */
        $this->validate($link);
    
        /** 插入数据 */
        $link['mid'] = $this->insertMeta($link, true);
        $this->push($link);
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("链接 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被增加",
        $this->slug, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-links.php', Typecho::widget('Options')->adminUrl));
    }
    
    /**
     * 更新链接
     * 
     * @access public
     * @return void
     */
    public function updateLink()
    {
        /** 取出数据 */
        $link = TypechoRequest::getParametersFrom('name', 'slug', 'description', 'mid');
        $link['type'] = 'link';
        
        /** 验证数据 */
        $this->validate($link, true);
    
        /** 更新数据 */
        $this->updateMeta($link, $link['mid'], 'link');
        $this->push($link);
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("链接 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被更新",
        $this->slug, $this->name), NULL, 'success');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-links.php', Typecho::widget('Options')->adminUrl));
    }
    
    /**
     * 删除链接
     * 
     * @access public
     * @return void
     */
    public function deleteLink()
    {
        $links = TypechoRequest::getParameter('mid');
        $deleteCount = 0;
        
        if($links && is_array($links))
        {
            foreach($links as $link)
            {
                if($this->deleteMeta($link, 'link'))
                {
                    $deleteCount ++;
                }
            }
        }

        /** 提示信息 */
        Typecho::widget('Notice')->set($deleteCount > 0 ? _t('链接已经删除') : _t('没有链接被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        Typecho::redirect(Typecho::pathToUrl('manage-links.php', Typecho::widget('Options')->adminUrl));
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
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertLink'));
        TypechoRequest::bindParameter(array('do' => 'update'), array($this, 'updateLink'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deleteLink'));
        Typecho::redirect(Typecho::widget('Options')->adminUrl);
    }
}
