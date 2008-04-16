<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入提交基类支持 **/
require_once 'Post.php';

/**
 * 附件处理类
 * 
 * @package Widget
 */
class AttachmentPost extends Post
{
    /**
     * 创建目录树
     * 
     * @access private
     * @param string $path 目录路径
     * @return void
     * @throws TypechoWidgetException
     */
    private function mkDirTree($path)
    {
        $path = str_replace(array('//', '\\', '\\\\'), '/', $path);
        $dir = explode('/', $path);
        $dirs = array();

        foreach($dir as $key => $val)
        {
            $dirs[] = $val;
            $path = implode('/', $dirs);

            if(!is_dir($path))
            {
                if(false == mkdir($path, 0766)) 
                {
                    throw new TypechoWidgetException(_t('目录创建错误 %s', $path));
                }
            }
        }
    }
    
    /**
     * 检测扩展名是否合法
     * 
     * @access private
     * @param  string $extension
     * @return boolean
     */
    private function checkFileExtension($extension)
    {
        $extension = empty($extension) ? '?' : $extension;
        return in_array($extension, explode('|', widget('Options')->attachmentExtensions));
    }
    
    /**
     * 获取存储路径
     *
     * @access private
     * @return string
     */
    private function getStoragePath($date = NULL)
    {
        $date = empty($date) ? widget('Options')->gmtTime : $date;
    
        return widget('Options')->attachmentDirectory . '/'
        . date('Y', $date) . '/'
        . date('n', $date) . '/'
        . date('j', $date);
    }

    /**
     * 上传附件
     * 
     * @access public
     * @return void
     */
    public function uploadAttachment()
    {
        if(empty($_FILES['attachment']))
        {
            widget('Notice')->set(_t('没有文件被上传'));
            return;
        }
        
        //获取上传路径
        $attachmentPath = $this->getStoragePath();
        $uploadedAttachments = array();
        
        //添加钩子
        $hookName = TypechoWidgetHook::name(__FILE__, 'upload');
        
        //多个上传
        if(is_array($_FILES['attachment']['name']))
        {
            $fileParts = pathinfo($_FILES['attachment']['name'][$key]);
            $fileExtension = empty($fileParts['extension']) ? NULL : strtolower($fileParts['extension']);
            
            foreach($_FILES['attachment']['name'] as $key => $name)
            {
                if(is_uploaded_file($_FILES['attachment']['tmp_name'][$key])
                && $this->checkFileExtension($fileExtension))
                {
                    if(!is_dir($path))
                    {
                        $this->mkDirTree($path);
                    }
                    
                    $fileName = $attachmentId . (empty($fileExtension) ? NULL : '.' . $fileExtension);
                    
                    $attachment = array(
                        'title'     =>  empty($_POST['title'][$key]) ? $_FILES['attachment']['name'][$key] : $_POST['title'][$key],
                        'text'      =>  empty($_POST['text'][$key]) ? NULL : $_POST['text'][$key],
                        'uri'       =>  $fileName,
                        'created'   =>  widget('Options')->gmtTime,
                        'modified'  =>  widget('Options')->gmtTime,
                        'author'    =>  widget('Access')->user('uid'),
                        'type'      =>  'attachment'
                    );
                    
                    $attachmentId = $this->db->query(
                    $this->db->sql()
                    ->insert('table.contents')
                    ->rows($attachment));
                    
                    move_uploaded_file($_FILES['attachment']['tmp_name'][$key], $attachmentPath . '/' . $fileName);
                    $uploadedAttachments[] = $_FILES['attachment']['name'][$key];
                    
                    //执行钩子
                    TypechoWidgetHook::call($hookName, $attachmentId, $attachmentPath . '/' . $fileName);
                }
            }
        }
        
        widget('Notice')->set(_t('%s 已经被上传'), implode(',', $uploadedAttachments));
        $this->goBack();
    }
    
    /**
     * 更新附件
     * 
     * @access public
     * @return void
     */
    public function modifyAttachment()
    {
        if(empty($_POST['cid']))
        {
            widget('Notice')->set(_t('没有文件被更新'));
            return;
        }
        
        $attachmentId = intval($_POST['cid']);
        
        $attachment = $this->db->fetchRow($this->db->sql()->select('table.contents')
                                          ->where('cid = ?', $attachmentId)
                                          ->limit(1));
        if(!$attachment)
        {
            widget('Notice')->set(_t('没有文件被更新'));
            return;
        }
        
        //验证用户身份
        if($attachment['author'] != widget('Access')->user('uid'))
        {
            widget('Access')->pass('editor');
        }
        
        //添加钩子
        $hookName = TypechoWidgetHook::name(__FILE__, 'modify');
        
        if(!empty($_FILES['attachment']))
        {
            $fileParts = pathinfo($_FILES['attachment']['name']);
            $fileExtension = empty($fileParts['extension']) ? NULL : strtolower($fileParts['extension']);
            $attachmentPath = $this->getStoragePath($attachment['created']);
            $fileName = $attachment['uri'];
            
            if(is_uploaded_file($_FILES['attachment']['tmp_name']) && 
            $this->checkFileExtension($fileExtension))
            {
                unlink($attachmentPath . '/' . $fileName);
                move_uploaded_file($_FILES['attachment']['tmp_name'], $attachmentPath . '/' . $fileName);
            }
        }
        
        $attachmentModify = array(
            'title'     =>  empty($_POST['title']) ? NULL : $_POST['title'],
            'text'      =>  empty($_POST['text']) ? NULL : $_POST['text'],
            'modified'  =>  widget('Options')->gmtTime
        );
        
        $this->db->query($this->db->sql()
        ->update('table.contents')
        ->rows($attachment)
        ->where('cid = ?', $attachmentId));
        
        TypechoWidgetHook::call($hookName, $attachmentId, $attachmentPath . '/' . $fileName);
        widget('Notice')->set(_t('附件已经被更新'));
        $this->goBack();
    }
    
    /**
     * 删除附件
     * 
     * @access public
     * @return void
     */
    public function deleteAttachment()
    {
        if(!($formData = TypechoRequest::getParameterList('cid')))
        {
            widget('Notice')->set(_t('没有文件被删除'));
            return;
        }
        
        $formData = array_map('intval', $formData);
        $deleteCount = 0;
        
        //添加钩子
        $hookName = TypechoWidgetHook::name(__FILE__, 'delete');
        
        foreach($formData as $attachmentId)
        {
            $attachment = $this->db->fetchRow($this->db->sql()->select('table.contents')
                                              ->where('`cid` = ?', $attachmentId)
                                              ->limit(1));
            
            if($attachment)
            {
                //验证用户身份
                if($attachment['author'] != widget('Access')->user('uid'))
                {
                    widget('Access')->pass('editor');
                }
            
                $this->db->query($this->db->sql()
                ->delete('table.contents')
                ->where('cid = ?', $attachmentId));
                
                //删除附件
                $attachmentPath = $this->getStoragePath($attachment['created']);
                $fileName = $attachment['uri'];
                unlink($attachmentPath . '/' . $fileName);
                $deleteCount ++;
                
                TypechoWidgetHook::call($hookName, $attachmentId, $attachmentPath . '/' . $fileName);
            }
        }
        
        widget('Notice')->set(_t('%d个附件已经被删除', $deleteCount));
        $this->goBack();
    }

    public function render()
    {
        //贡献者以上有提交文件权限
        widget('Access')->pass('contributor');
        TypechoRequest::bindParameter(array('act' => 'upload'), 'uploadAttachment');
        TypechoRequest::bindParameter(array('act' => 'modify'), 'modifyAttachment');
        TypechoRequest::bindParameter(array('act' => 'delete'), 'deleteAttachment');
    }
}
