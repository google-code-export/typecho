<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 异常处理组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_ExceptionHandle extends Typecho_Widget
{
    /**
     * 全局选项
     * 
     * @access protected
     * @var Widget_Options
     */
    protected $options;
    
    /**
     * 数据库对象
     * 
     * @access protected
     * @var Typecho_Db
     */
    protected $db;

    /**
     * 重载构造函数
     * 
     * @access public
     * @param Exception $excepiton 抛出的异常
     * @return void
     */
    public function __construct(Exception $excepiton)
    {
        parent::__construct(array(
            'code'      =>  $excepiton->getCode(),
            'message'   =>  $excepiton->getMessage(),
            'trace'     =>  $excepiton->getTrace(),
            'error'     =>  $excepiton->__toString()
        ));
        
        /** 如果数据库可用 */
        //~ 503和500都是内部程序错误
        if (503 != $this->parameter->code && 500 != $this->parameter->code) {
            /** 初始化数据库 */
            $this->db = Typecho_Db::get();
        
            /** 初始化常用组件 */
            $this->options = $this->widget('Widget_Options');
        }
        
        //~ 执行
        $this->execute();
    }
    
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        @ob_clean();
    
        if (503 != $this->parameter->code && 500 != $this->parameter->code &&
        is_file($file = __TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/' .
        $this->options->theme . '/' . $this->parameter->code . '.php')) {
            require_once $file;
        } else {
            $charset = Typecho_Common::$config['charset'];
            $title = $this->parameter->code > 0 ? $this->parameter->code : _t('错误');
            
            if (503 == $this->parameter->code) {
                $message = _t('数据库服务器当前不可用<br />
产生此问题的原因可能是由于数据库的连接产生了异常, 请检查您的服务器设置. 如果您无法解决这个问题可以到typecho社区寻求帮助.');
                error_log($this->parameter->error);
            } else if (500 == $this->parameter->code) {
                $message = _t('服务器运行时发生错误<br />
产生此问题的原因可能是由于程序的内部出现了严重错误造成, 请检查你的程序. 如果您无法解决这个问题可以到typecho社区寻求帮助.');
                error_log($this->parameter->error);
            } else {
                $message = $this->parameter->message;
            }
            
            echo 
<<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
    <title>{$title}</title>

    <style type="text/css">
        body {
            background: #f7fbe9;
            font-family: "Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana;
        }
        
        #error {
            background: #333;
            width: 360px;
            margin: 0 auto;
            margin-top: 100px;
            color: #fff;
            padding: 10px;
            
            -moz-border-radius-topleft: 4px;
            -moz-border-radius-topright: 4px;
            -moz-border-radius-bottomleft: 4px;
            -moz-border-radius-bottomright: 4px;
            -webkit-border-top-left-radius: 4px;
            -webkit-border-top-right-radius: 4px;
            -webkit-border-bottom-left-radius: 4px;
            -webkit-border-bottom-right-radius: 4px;

            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        
        h1 {
            padding: 10px;
            margin: 0;
            font-size: 36px;
        }
        
        p {
            padding: 0 20px 20px 20px;
            margin: 0;
            font-size: 12px;
        }
        
        img {
            padding: 0 0 5px 260px;
        }
    </style>
</head>
<body>
    <div id="error">
        <h1>{$title}</h1>
        <p>{$message}</p>
        <img src="?464D-E63E-9D08-97E2-16DD-6A37-BDEC-6021" />
    </div>
</body>
</html>
EOF;
        }
    }
}
