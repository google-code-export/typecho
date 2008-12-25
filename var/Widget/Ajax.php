<?php

class Widget_Ajax extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 针对rewrite验证的请求返回
     * 
     * @access public
     * @return void
     */
    public function remoteCallback()
    {
        if ($this->options->generator == $this->request->getAgent()) {
            echo 'OK';
        }
    }
    
    /**
     * 异步请求入口
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->onRequest('do', 'remoteCallback')->remoteCallback();
    }
}
