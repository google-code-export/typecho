<?php
/**
 * 基本设置
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 基本设置组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options_General extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 输出表单结构
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form
     */
    public function form()
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/options-general', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 站点名称 */
        $title = new Typecho_Widget_Helper_Form_Element_Text('title', NULL, $this->options->title, _t('站点名称'), _t('站点的名称将显示在网页的标题处.'));
        $form->addInput($title);
        
        /** 站点描述 */
        $description = new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, $this->options->description, _t('站点描述'), _t('站点描述将显示在网页代码的头部.'));
        $form->addInput($description);
        
        /** 关键词 */
        $keywords = new Typecho_Widget_Helper_Form_Element_Text('keywords', NULL, $this->options->keywords, _t('关键词'), _t('请以半角逗号","分割多个关键字.'));
        $form->addInput($keywords);
        
        /** gzip */
        $allowRegister = new Typecho_Widget_Helper_Form_Element_Radio('allowRegister', array('0' => _t('不允许'), '1' => _t('允许')), $this->options->allowRegister, _t('是否允许注册'),
        _t('允许访问者注册到你的网站, 默认的注册用户不享有任何写入权限.'));
        $form->addInput($allowRegister);
        
        /** 时区 */
        $timezone = new Typecho_Widget_Helper_Form_Element_Select('timezone', array('28800' => _t('中华人民共和国')), $this->options->timezone, _t('时区'));
        $form->addInput($timezone);
        
        /** gzip */
        /*
        $gzip = new Typecho_Widget_Helper_Form_Element_Radio('gzip', array('0' => _t('不启用'), '1' => _t('启用')), $this->options->gzip, _t('是否启用gzip'),
        _t('启用gzip压缩可以减小网页尺寸大小, 从而降低下载时间, 但是它会消耗一部分服务器附载.'));
        $form->addInput($gzip);
        */
        
        /** 扩展名 */
        $attachmentTypes = new Typecho_Widget_Helper_Form_Element_Text('attachmentTypes', NULL, $this->options->attachmentTypes, _t('允许上传的文件类型'),
        _t('用分号 ; 隔开, 例如: *.zip;*.jpg'));
        $form->addInput($attachmentTypes);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('保存设置'));
        $form->addItem($submit);
        
        return $form;
    }
    
    /**
     * 执行更新动作
     * 
     * @access public
     * @return void
     */
    public function updateGeneralSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
        
        $settings = $this->request->from('title', 'description', 'keywords', 'allowRegister', 'timezone', 'attachmentTypes');
        foreach ($settings as $name => $value) {
            $this->update(array('value' => $value), $this->db->sql()->where('name = ?', $name));
        }
        
        $this->widget('Widget_Notice')->set(_t("设置已经保存"), NULL, 'success');
        $this->response->goBack();
    }

    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->on($this->request->isPost())->updateGeneralSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
