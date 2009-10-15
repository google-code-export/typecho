<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 评论编辑组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Edit extends Widget_Abstract_Comments implements Widget_Interface_Do
{
    /**
     * 标记评论状态
     * 
     * @access private
     * @param integer $coid 评论主键
     * @param string $status 状态
     * @return boolean
     */
    private function mark($coid, $status)
    {
        $comment = $this->db->fetchRow($this->select()
        ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
        
        if ($comment && $this->commentIsWriteable()) {
            /** 增加评论编辑插件接口 */
            $this->pluginHandle()->mark($comment, $this, $status);
        
            /** 不必更新的情况 */
            if ($status == $comment['status']) {
                return false;
            }
        
            /** 更新评论 */
            $this->db->query($this->db->update('table.comments')
            ->rows(array('status' => $status))->where('coid = ?', $coid));
        
            /** 更新相关内容的评论数 */
            if ('approved' == $comment['status'] && 'approved' != $status) {
                $this->db->query($this->db->update('table.contents')
                ->expression('commentsNum', 'commentsNum - 1')->where('cid = ? AND commentsNum > 0', $comment['cid']));
            } else if ('approved' != $comment['status'] && 'approved' == $status) {
                $this->db->query($this->db->update('table.contents')
                ->expression('commentsNum', 'commentsNum + 1')->where('cid = ?', $comment['cid']));
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 以数组形式获取coid
     * 
     * @access private
     * @return array
     */
    private function getCoidAsArray()
    {
        $coid = $this->request->filter('int')->coid;
        return $coid ? (is_array($coid) ? $coid : array($coid)) : array();
    }

    /**
     * 标记为待审核
     * 
     * @access public
     * @return void
     */
    public function waitingComment()
    {
        $comments = $this->getCoidAsArray();
        $updateRows = 0;
        
        foreach ($comments as $comment) {
            if ($this->mark($comment, 'waiting')) {
                $updateRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($updateRows > 0 ? _t('评论已经被标记为待审核') : _t('没有评论被标记为待审核'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 标记为垃圾
     * 
     * @access public
     * @return void
     */
    public function spamComment()
    {
        $comments = $this->getCoidAsArray();
        $updateRows = 0;
        
        foreach ($comments as $comment) {
            if ($this->mark($comment, 'spam')) {
                $updateRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($updateRows > 0 ? _t('评论已经被标记为垃圾') : _t('没有评论被标记为垃圾'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 标记为展现
     * 
     * @access public
     * @return void
     */
    public function approvedComment()
    {
        $comments = $this->getCoidAsArray();
        $updateRows = 0;
        
        foreach ($comments as $comment) {
            if ($this->mark($comment, 'approved')) {
                $updateRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($updateRows > 0 ? _t('评论已经被通过') : _t('没有评论被通过'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 删除评论
     * 
     * @access public
     * @return void
     */
    public function deleteComment()
    {
        $comments = $this->getCoidAsArray();
        $deleteRows = 0;
        
        foreach ($comments as $coid) {
            $comment = $this->db->fetchRow($this->select()
            ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
            
            if ($comment && $this->commentIsWriteable()) {
                /** 删除评论 */
                $this->db->query($this->db->delete('table.comments')->where('coid = ?', $coid));
            
                /** 更新相关内容的评论数 */
                if ('approved' == $comment['status']) {
                    $this->db->query($this->db->update('table.contents')
                    ->expression('commentsNum', 'commentsNum - 1')->where('cid = ?', $comment['cid']));
                }
                
                $deleteRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($deleteRows > 0 ? _t('评论已经被删除') : _t('没有评论被删除'), NULL,
        $deleteRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 删除所有垃圾评论
     * 
     * @access public
     * @return string
     */
    public function deleteSpamComment()
    {
        $deleteQuery = $this->db->delete('table.comments')->where('status = ?', 'spam');
        if (!$this->request->__typecho_all_comments || !$this->user->pass('editor', true)) {
            $deleteQuery->where('ownerId = ?', $this->user->uid);
        }
        
        if (isset($this->request->cid)) {
            $deleteQuery->where('cid = ?', $this->request->cid);
        }
        
        $deleteRows = $this->db->query($deleteQuery);
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($deleteRows > 0 ?
        _t('所有垃圾评论已经被删除') : _t('没有垃圾评论被删除'), NULL,
        $deleteRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 获取可编辑的评论
     * 
     * @access public
     * @return void
     */
    public function getComment()
    {
        if (!$this->request->isAjax()) {
            $this->response->goBack();
        }
        
        $coid = $this->request->filter('int')->coid;
        $comment = $this->db->fetchRow($this->select()
            ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
        
        if ($comment && $this->commentIsWriteable()) {
            
            $this->response->throwJson(array(
                'success'   => 1,
                'comment'   => $comment
            ));
            
        } else {
            
            $this->response->throwJson(array(
                'success'   => 0,
                'message'   => _t('获取评论失败')
            ));
            
        }
    }
    
    /**
     * 编辑评论
     * 
     * @access public
     * @return void
     */
    public function editComment()
    {
        if (!$this->request->isAjax()) {
            $this->response->goBack();
        }
        
        $coid = $this->request->filter('int')->coid;
        $commentSelect = $this->db->fetchRow($this->select()
            ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
        
        if ($commentSelect && $this->commentIsWriteable()) {
        
            //检验格式
            $validator = new Typecho_Validate();
            $validator->addRule('author', 'required', _t('必须填写用户名'));
            
            if ($this->options->commentsRequireMail) {
                $validator->addRule('mail', 'required', _t('必须填写电子邮箱地址'));
            }
            
            $validator->addRule('mail', 'email', _t('邮箱地址不合法'));
            
            if ($this->options->commentsRequireUrl && !$this->user->hasLogin()) {
                $validator->addRule('url', 'required', _t('必须填写个人主页'));
            }
            
            $validator->addRule('text', 'required', _t('必须填写评论内容'));
            $comment['text'] = $this->request->text;
            
            $comment['author'] = $this->request->filter('strip_tags', 'trim', 'xss')->author;
            $comment['mail'] = $this->request->filter('strip_tags', 'trim', 'xss')->mail;
            $comment['url'] = $this->request->filter('url')->url;
        
            /** 更新评论 */
            $this->db->query($this->db->update('table.comments')
            ->rows($comment)->where('coid = ?', $coid));

            $updatedComment = $this->db->fetchRow($this->select()
                ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
            $updatedComment['content'] = $this->content;
        
            $this->response->throwJson(array(
                'success'   => 1,
                'comment'   => $updatedComment
            ));
        }
        
        $this->response->throwJson(array(
            'success'   => 0,
            'message'   => _t('修改评论失败')
        ));
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('contributor');
        $this->on($this->request->is('do=waiting'))->waitingComment();
        $this->on($this->request->is('do=spam'))->spamComment();
        $this->on($this->request->is('do=approved'))->approvedComment();
        $this->on($this->request->is('do=delete'))->deleteComment();
        $this->on($this->request->is('do=delete-spam'))->deleteSpamComment();
        $this->on($this->request->is('do=get&coid'))->getComment();
        $this->on($this->request->is('do=edit&coid'))->editComment();
        
        $this->response->redirect($this->options->adminUrl);
    }
}
