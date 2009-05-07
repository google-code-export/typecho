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
            if (!@mkdir($path, 0777)) {
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
            if (!@mkdir($path, 0777)) {
                return false;
            }
        }
        
        //创建月份目录
        if (!is_dir($path = $path . '/' . $date->month)) {
            if (!@mkdir($path, 0777)) {
                return false;
            }
        }
        
        //创建日期目录
        if (!is_dir($path = $path . '/' . $date->day)) {
            if (!@mkdir($path, 0777)) {
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
            'path' => self::UPLOAD_PATH . '/' . $date->year . '/' . $date->month . '/' . $date->day . '/' . $fileName,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => Typecho_Common::mimeContentType($path)
        );
    }
    
    /**
     * 删除文件
     * 
     * @access public
     * @param string $path 文件路径
     * @return string
     */
    public static function deleteHandle($path)
    {
        return @unlink(__TYPECHO_ROOT_DIR__ . '/' . $path);
    }
    
    /**
     * 获取实际文件路径
     * 
     * @access public
     * @param string $file 相对文件路径
     * @return void
     */
    public static function attachmentHandle($file)
    {
        $options = Typecho_Widget::widget('Widget_Options');
        return Typecho_Common::url($file, $options->siteUrl);
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
                $attachmentHandle = unserialize($this->options->attachmentHandle);
                $result = call_user_func($uploadHandle, $file);
                
                if (false === $result) {
                    $this->response->setStatus(502);
                    exit;
                } else {
                
                    $result['uploadHandle'] = $uploadHandle;
                    $result['deleteHandle'] = $deleteHandle;
                    $result['attachmentHandle'] = $attachmentHandle;
                
                    $this->insert(array(
                        'title'     =>  $result['name'],
                        'slug'      =>  $result['name'],
                        'type'      =>  'attachment',
                        'text'      =>  serialize($result),
                        'allowComment'      =>  1,
                        'allowPing'         =>  0,
                        'allowFeed'         =>  1
                    ));
                    
                    $this->response->throwJson(array(
                        'title'     =>  $result['name'],
                        'type'      =>  $result['ext'],
                        'size'      =>  $result['size'],
                        'url'       =>  call_user_func($attachmentHandle, $result['path'])
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
            $this->onPost()->upload();
        } else {
            $this->response->setStatus(403);
        }
    }
}
