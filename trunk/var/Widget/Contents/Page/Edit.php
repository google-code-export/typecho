<?php
/**
 * 编辑页面
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑页面组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Page_Edit extends Widget_Contents_Post_Edit implements Widget_Interface_Do
{
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 必须为编辑以上权限 */
        $this->user->pass('editor');
    
        /** 获取文章内容 */
        if ((isset($this->request->cid) && 'delete' != $this->request->do && 'sort' != $this->request->do
         && 'insert' != $this->request->do) || 'update' == $this->request->do || 'customHomePage' == $this->request->do) {
            $this->db->fetchRow($this->select()
            ->where('table.contents.type = ?', 'page')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));
            
            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('页面不存在'), 404);
            } else if ($this->have() && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            }
        }
    }
    
    /**
     * 新增页面
     * 
     * @access public
     * @return void
     */
    public function insertPage()
    {
        $contents = $this->request->from('text', 'template', 'allowComment', 'allowPing', 'allowFeed', 'slug', 'order');
        $contents['type'] = 'page';
        $contents['status'] = $this->request->draft ? 'draft' :  'publish';
        $contents['title'] = $this->request->get('title', _t('未命名文档'));
        $contents['created'] = $this->getCreated();
        
        /** 提交数据的过滤 */
        $contents = $this->pluginHandle()->insert($contents);
        $insertId = $this->insert($contents);
        
        if ($insertId > 0) {
            $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $insertId)->limit(1), array($this, 'push'));
            
            /** 同步附件 */
            $this->attach($insertId);
        }
        
        /** 发送ping */
        $this->widget('Widget_Service')->sendPing($this->cid);
        
        if ($this->request->isAjax()) {
            if ($insertId > 0) {
                $created = new Typecho_Date($this->options->gmtTime);
                $this->response->throwJson(array(
                    'success'  =>  1,
                    'message'  =>  _t('页面保存于 %s', $created->format('H:i A')),
                    'cid'      =>  $insertId
                ));
            } else {
                $this->response->throwJson(array(
                    'success'  =>  0,
                    'message'  =>  _t('页面保存失败')
                ));
            }
        } else {
            /** 页面提示信息 */
            if ('publish' == $contents['status']) {
                $this->widget('Widget_Notice')->set($insertId > 0 ? 
                _t('页面 "<a href="%s">%s</a>" 已经被创建', $this->permalink, $this->title)
                : _t('页面提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
            } else {
                $this->widget('Widget_Notice')->set($insertId > 0 ? 
                _t('草稿 "%s" 已经被保存', $this->title) :
                _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
            }
            
            /** 设置高亮 */
            $this->widget('Widget_Notice')->highlight($this->theId);

            /** 跳转页面 */
            if ('draft' == $contents['status']) {
                $this->response->redirect(Typecho_Common::url('write-page.php?cid=' . $insertId, $this->options->adminUrl));
            } else {
                $this->response->redirect(Typecho_Common::url('manage-pages.php?status=' . $contents['status'], $this->options->adminUrl));
            }
        }
    }
    
    /**
     * 更新页面
     * 
     * @access public
     * @return void
     */
    public function updatePage()
    {
        $contents = $this->request->from('text', 'template', 'allowComment', 'allowPing', 'allowFeed', 'slug', 'order');
        $contents['type'] = 'page';
        $contents['status'] = $this->request->draft ? 'draft' :  'publish';
        $contents['title'] = $this->request->get('title', _t('未命名文档'));
        $contents['created'] = $this->getCreated();
    
        /** 提交数据的过滤 */
        $contents = $this->pluginHandle()->update($contents);
        $updateRows = $this->update($contents, $this->db->sql()->where('cid = ?', $this->cid));
        
        if ($updateRows > 0) {
            /** 取出页面 */
            $this->db->fetchRow($this->select()->where('cid = ?', $this->cid)->limit(1), array($this, 'push'));
            
            /** 同步附件 */
            $this->attach($this->cid);
        }
        
        /** 发送pingback */
        $this->widget('Widget_Service')->sendPing($this->cid);
        
        if ($this->request->isAjax()) {
            if ($updateRows > 0) {
                $created = new Typecho_Date($this->options->gmtTime);
                $this->response->throwJson(array(
                    'success'  =>  1,
                    'message'  =>  _t('页面保存于 %s', $created->format('H:i A')),
                    'cid'      =>  $this->cid
                ));
            } else {
                $this->response->throwJson(array(
                    'success'  =>  0,
                    'message'  =>  _t('页面保存失败')
                ));
            }
        } else {
            /** 页面提示信息 */
            if ('publish' == $contents['status']) {
                $this->widget('Widget_Notice')->set($updateRows > 0 ? 
                _t('页面 "<a href="%s">%s</a>" 已经被更新', $this->permalink, $this->title)
                : _t('页面提交失败'), NULL, $updateRows > 0 ? 'success' : 'error');
            } else {
                $this->widget('Widget_Notice')->set($updateRows > 0 ? 
                _t('草稿 "%s" 已经被保存', $this->title) :
                _t('草稿保存失败'), NULL, $updateRows > 0 ? 'success' : 'error');
            }
            
            /** 设置高亮 */
            $this->widget('Widget_Notice')->highlight($this->theId);

            /** 跳转页面 */
            if ('draft' == $contents['status']) {
                $this->response->redirect(Typecho_Common::url('write-page.php?cid=' . $this->cid, $this->options->adminUrl));
            } else {
                $this->response->redirect(Typecho_Common::url('manage-pages.php?status=' . $contents['status'], $this->options->adminUrl));
            }
        }
    }
    
    /**
     * 删除页面
     * 
     * @access public
     * @return void
     */
    public function deletePage()
    {
        $cid = $this->request->filter('int')->cid;
        $deleteCount = 0;

        if ($cid) {
            /** 格式化页面主键 */
            $pages = is_array($cid) ? $cid : array($cid);
            foreach ($pages as $page) {
                if ($this->delete($this->db->sql()->where('cid = ?', $page))) {
                    /** 删除评论 */
                    $this->db->query($this->db->delete('table.comments')
                    ->where('cid = ?', $page));
                    
                    /** 解除附件关联 */
                    $this->unAttach($page);
                    
                    /** 解除自定义首页 */
                    if ($page == $this->options->customHomePage) {
                        $this->db->query($this->db->update('table.options')
                        ->rows(array('value' => 0))
                        ->where('name = ?', 'customHomePage'));
                    }
                    
                    $deleteCount ++;
                }
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($deleteCount > 0 ? _t('页面已经被删除') : _t('没有页面被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 页面排序
     * 
     * @access public
     * @return void
     */
    public function sortPage()
    {
        $pages = $this->request->filter('int')->cid;
        
        if ($pages && is_array($pages)) {
            foreach ($pages as $sort => $cid) {
                $this->db->query($this->db->update('table.contents')->rows(array('order' => $sort + 1))
                ->where('cid = ?', $cid));
            }
        }
        
        if (!$this->request->isAjax()) {
            /** 转向原页 */
            $this->response->goBack();
        } else {
            $this->response->throwJson(array('success' => 1, 'message' => _t('页面排序已经完成')));
        }
    }
    
    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->on($this->request->is('do=insert'))->insertPage();
        $this->on($this->request->is('do=update'))->updatePage();
        $this->on($this->request->is('do=delete'))->deletePage();
        $this->on($this->request->is('do=sort'))->sortPage();
        $this->response->redirect($this->options->adminUrl);
    }
}
