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
class Widget_Comments_Edit extends Widget_Abstract_Comments implements Widget_Interface_Action_Widget
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
        $comment = $this->db()->fetchRow($this->db()->sql()->select('table.comments')
        ->where('`coid` = ?', $coid)->limit(1));
        
        if ($comment) {
            /** 不必更新的情况 */
            if ($status == $comment['status']) {
                return false;
            }
        
            /** 更新评论 */
            $this->db()->query($this->db()->sql()->update('table.comments')
            ->rows(array('status' => $status))->where('`coid` = ?', $coid));
        
            /** 更新相关内容的评论数 */
            if ('approved' == $comment['status'] && 'approved' != $status) {
                $this->db()->query($this->db()->sql()->update('table.contents')
                ->row('commentsNum', '`commentsNum` - 1')->where('`cid` = ?', $comment['cid']));
            } else if ('approved' != $comment['status'] && 'approved' == $status) {
                $this->db()->query($this->db()->sql()->update('table.contents')
                ->row('commentsNum', '`commentsNum` + 1')->where('`cid` = ?', $comment['cid']));
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
        $coid = $this->request()->coid;
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
        $this->notice()->set($updateRows > 0 ? _t('评论已经被标记为待审核') : _t('没有评论被标记为待审核'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response()->goBack();
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
        $this->notice()->set($updateRows > 0 ? _t('评论已经被标记为垃圾') : _t('没有评论被标记为垃圾'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response()->goBack();
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
        $this->notice()->set($updateRows > 0 ? _t('评论已经被呈现') : _t('没有评论被呈现'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response()->goBack();
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
            $comment = $this->db()->fetchRow($this->db()->sql()->select('table.comments')
            ->where('`coid` = ?', $coid)->limit(1));
            
            if ($comment) {
                /** 删除评论 */
                $this->db()->query($this->db()->sql()->delete('table.comments')->where('`coid` = ?', $coid));
            
                /** 更新相关内容的评论数 */
                if ('approved' == $comment['status']) {
                    $this->db()->query($this->db()->sql()->update('table.contents')
                    ->row('commentsNum', '`commentsNum` - 1')->where('`cid` = ?', $comment['cid']));
                } else if ('approved' != $comment['status']) {
                    $this->db()->query($this->db()->sql()->update('table.contents')
                    ->row('commentsNum', '`commentsNum` + 1')->where('`cid` = ?', $comment['cid']));
                }
                
                $deleteRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->notice()->set($deleteRows > 0 ? _t('评论已经被删除') : _t('没有评论被删除'), NULL,
        $deleteRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response()->goBack();
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @param Typecho_Widget_Request $request 请求对象
     * @param Typecho_Widget_Response $response 回执对象
     * @return void
     */
    public function init(Typecho_Widget_Request $request, Typecho_Widget_Response $response)
    {
        $this->user()->pass('editor');
        $this->onRequest('do', 'waiting')->waitingComment();
        $this->onRequest('do', 'spam')->spamComment();
        $this->onRequest('do', 'approved')->approvedComment();
        $this->onRequest('do', 'delete')->deleteComment();
        
        $response->redirect($this->options()->adminUrl);
    }
}
