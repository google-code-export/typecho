<?php
/**
 * 编辑文章
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑文章组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Attachment_Edit extends Widget_Contents_Post_Edit implements Widget_Interface_Do
{
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 必须为贡献者以上权限 */
        $this->user->pass('contributor');
    
        /** 获取文章内容 */
        if ((isset($this->request->cid) && 'delete' != $this->request->do
         && 'insert' != $this->request->do) || 'update' == $this->request->do) {
            $this->db->fetchRow($this->select()
            ->where('table.contents.type = ?', 'attachment')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));
            
            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('附件不存在'), 404);
            } else if ($this->have() && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            }
        }
    }

    /**
     * 删除文章
     * 
     * @access public
     * @return void
     */
    public function deleteAttachment()
    {
        $cid = $this->request->filter('int')->cid;
        $deleteCount = 0;

        if ($cid) {
            /** 格式化文章主键 */
            $posts = is_array($cid) ? $cid : array($cid);
            foreach ($posts as $post) {
            
                $condition = $this->db->sql()->where('cid = ?', $post);
                $this->db->fetchRow($this->select()
                ->where('table.contents.type = ?', 'attachment')
                ->where('table.contents.cid = ?', $post)
                ->limit(1), array($this, 'push'));
                
                if ($this->isWriteable($condition) && $this->delete($condition)) {
                    /** 删除文件 */
                    call_user_func($this->attachment->deleteHandle, $this->attachment->path);
                
                    /** 删除评论 */
                    $this->db->query($this->db->delete('table.comments')
                    ->where('cid = ?', $post));
                    
                    $deleteCount ++;
                }
                
                unset($condition);
            }
        }
        
        if ($this->request->isAjax()) {
            $this->response->throwJson($deleteCount > 0 ? array('code' => 200, 'message' => _t('附件已经被删除'))
            : array('code' => 500, 'message' => _t('没有附件被删除')));
        } else {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($deleteCount > 0 ? _t('附件已经被删除') : _t('没有附件被删除'), NULL,
            $deleteCount > 0 ? 'success' : 'notice');
            
            /** 返回原网页 */
            $this->response->goBack();
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
        $this->onRequest('do', 'delete')->deleteAttachment();
        $this->response->redirect($this->options->adminUrl);
    }
}
