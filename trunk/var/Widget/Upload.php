<?php
/**
 * 上传动作
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 上传组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 */
class Widget_Upload extends Widget_Abstract_Contents implements Widget_Interface_Do
{
    //上传文件目录
    const UPLOAD_PATH = '/usr/uploads';
    
    /**
     * 创建上传路径
     * 
     * @access private
     * @param string $path 路径
     * @return boolean
     */
    private static function makeUploadDir($path)
    {
        if (!@mkdir($path)) {
            return false;
        }
        
        $stat = @stat($path);
        $perms = $stat['mode'] & 0007777;
        @chmod($path, $perms);
        
        return true;
    }

    /**
     * 上传文件处理函数,如果需要实现自己的文件哈希或者特殊的文件系统,请在options表里把uploadHandle改成自己的函数
     * 
     * @access public
     * @param array $file 上传的文件
     * @return mixed
     */
    public static function uploadHandle($file)
    {
        if (empty($file['name'])) {
            return false;
        }
        
        $fileName = preg_split("(\/|\\|:)", $file['name']);
        $file['name'] = array_pop($fileName);
        
        if (!self::checkFileType($file['name'])) {
            return false;
        }
    
        $options = Typecho_Widget::widget('Widget_Options');
        $date = new Typecho_Date($options->gmtTime);
        $path = Typecho_Common::url(self::UPLOAD_PATH, __TYPECHO_ROOT_DIR__);
        
        //创建上传目录
        if (!is_dir($path)) {
            if (!self::makeUploadDir($path)) {
                return false;
            }
        }
        
        //获取扩展名
        $ext = 'bin';
        $part = explode('.', $file['name']);
        if (($length = count($part)) > 1) {
            $ext = strtolower($part[$length - 1]);
        }
        
        //创建年份目录
        if (!is_dir($path = $path . '/' . $date->year)) {
            if (!self::makeUploadDir($path)) {
                return false;
            }
        }
        
        //创建月份目录
        if (!is_dir($path = $path . '/' . $date->month)) {
            if (!self::makeUploadDir($path)) {
                return false;
            }
        }
        
        //获取文件名
        $fileName = sprintf('%u', crc32(uniqid())) . '.' . $ext;
        $path = $path . '/' . $fileName;

        if (isset($file['tmp_name'])) {
        
            //移动上传文件
            if (!move_uploaded_file($file['tmp_name'], $path)) {
                return false;
            }
        } else if (isset($file['bits'])) {
        
            //直接写入文件
            if (!file_put_contents($path, $file['bits'])) {
                return false;
            }
        } else {
            return false;
        }
        
        if (!isset($file['size'])) {
            $file['size'] = filesize($path);
        }
        
        //返回相对存储路径
        return array(
            'name' => $file['name'],
            'path' => self::UPLOAD_PATH . '/' . $date->year . '/' . $date->month . '/' . $fileName,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => Typecho_Common::mimeContentType($path)
        );
    }
    
    /**
     * 修改文件处理函数,如果需要实现自己的文件哈希或者特殊的文件系统,请在options表里把modifyHandle改成自己的函数
     * 
     * @access public
     * @param array $content 老文件
     * @param array $file 新上传的文件
     * @return mixed
     */
    public static function modifyHandle($content, $file)
    {
        if (empty($file['name'])) {
            return false;
        }
        
        $fileName = preg_split("(\/|\\|:)", $file['name']);
        $file['name'] = array_pop($fileName);
        
        if (!self::checkFileType($file['name'])) {
            return false;
        }
        
        $path = Typecho_Common::url($content['attachment']->path, __TYPECHO_ROOT_DIR__);

        if (isset($file['tmp_name'])) {
        
            //移动上传文件
            if (!move_uploaded_file($file['tmp_name'], $path)) {
                return false;
            }
        } else if (isset($file['bits'])) {
        
            //直接写入文件
            if (!file_put_contents($path, $file['bits'])) {
                return false;
            }
        } else {
            return false;
        }
        
        if (!isset($file['size'])) {
            $file['size'] = filesize($path);
        }
        
        //返回相对存储路径
        return array(
            'name' => $file['name'],
            'path' => self::UPLOAD_PATH . '/' . $date->year . '/' . $date->month . '/' . $fileName,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => Typecho_Common::mimeContentType($path)
        );
    }
    
    /**
     * 删除文件
     * 
     * @access public
     * @param array $content 文件相关信息
     * @return string
     */
    public static function deleteHandle(array $content)
    {
        return @unlink(__TYPECHO_ROOT_DIR__ . '/' . $content['attachment']->path);
    }
    
    /**
     * 获取实际文件绝对访问路径
     * 
     * @access public
     * @param array $content 文件相关信息
     * @return string
     */
    public static function attachmentHandle(array $content)
    {
        $options = Typecho_Widget::widget('Widget_Options');
        return Typecho_Common::url($content['attachment']->path, $options->siteUrl);
    }
    
    /**
     * 获取实际文件数据
     * 
     * @access public
     * @param array $content
     * @return string
     */
    public static function attachmentDataHandle(array $content)
    {
        return file_get_contents(Typecho_Common::url($content['attachment']->path, __TYPECHO_ROOT_DIR__));
    }
    
    /**
     * 检查文件名
     * 
     * @access private
     * @param string $fileName 文件名
     * @return boolean
     */
    public static function checkFileType($fileName)
    {
        $options = Typecho_Widget::widget('Widget_Options');
        $exts = array_filter(explode(';', $options->attachmentTypes));
        
        foreach ($exts as $ext) {
            $ext = str_replace(array('.', '*'), array('\.', '.*'), $ext);
            if (preg_match("|^{$ext}$|is", $fileName)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 执行升级程序
     * 
     * @access public
     * @return void
     */
    public function upload()
    {
        if (!empty($_FILES)) {
            $file = array_pop($_FILES);
            if (0 == $file['error'] && is_uploaded_file($file['tmp_name'])) {
                $uploadHandle = unserialize($this->options->uploadHandle);
                $deleteHandle = unserialize($this->options->deleteHandle);
                $modifyHandle = unserialize($this->options->modifyHandle);
                $attachmentHandle = unserialize($this->options->attachmentHandle);
                $attachmentDataHandle = unserialize($this->options->attachmentDataHandle);
                $result = call_user_func($uploadHandle, $file);
                
                if (false === $result) {
                    $this->response->setStatus(502);
                    exit;
                } else {
                
                    $result['uploadHandle'] = $uploadHandle;
                    $result['deleteHandle'] = $deleteHandle;
                    $result['modifyHandle'] = $modifyHandle;
                    $result['attachmentHandle'] = $attachmentHandle;
                    $result['attachmentDataHandle'] = $attachmentDataHandle;
                
                    $insertId = $this->insert(array(
                        'title'     =>  $result['name'],
                        'slug'      =>  $result['name'],
                        'type'      =>  'attachment',
                        'status'    =>  'unattached',
                        'text'      =>  serialize($result),
                        'allowComment'      =>  1,
                        'allowPing'         =>  0,
                        'allowFeed'         =>  1
                    ));
                    
                    $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $insertId)
                    ->where('table.contents.type = ?', 'attachment'), array($this, 'push'));
                    
                    /** 增加插件接口 */
                    $this->plugin()->upload($this);
                    
                    $this->response->throwJson(array(
                        'cid'       =>  $insertId,
                        'title'     =>  $this->attachment->name,
                        'type'      =>  $this->attachment->type,
                        'size'      =>  $this->attachment->size,
                        'isImage'   =>  $this->attachment->isImage,
                        'url'       =>  $this->attachment->url,
                        'permalink' =>  $this->permalink
                    ));
                }
            }
        }
        
        $this->response->setStatus(500);
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        if ($this->user->pass('contributor', true)) {
            $this->on($this->request->isPost())->upload();
        } else {
            $this->response->setStatus(403);
        }
    }
}
