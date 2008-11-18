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
class Widget_Options_General extends Widget_Abstract_Options implements Widget_Interface_Action_Widget
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
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::pathToUrl('/Options/General.do', $this->options->index),
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
        
        /** 时区 */
        $timezone = new Typecho_Widget_Helper_Form_Element_Select('timezone', array('28800' => _t('中华人民共和国')), $this->options->timezone, _t('时区'));
        $form->addInput($timezone);
        
        /** 是否使用地址重写功能 */
        $rewrite = new Typecho_Widget_Helper_Form_Element_Radio('rewrite', array('0' => _t('不启用'), '1' => _t('启用')),
        $this->options->rewrite, _t('是否使用地址重写功能'), _t('地址重写即rewrite功能是某些服务器软件提供的优化内部连接的功能.<br />
        打开此功能可以让你的链接看上去完全是静态地址.'));
        $form->addInput($rewrite);
        
        /** 动作 */
        $do = new Typecho_Widget_Helper_Form_Element_Hidden('do', NULL, 'update');
        $form->addInput($do);
        
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
        $settings = $this->request->from('title', 'description', 'keywords', 'timezone', 'rewrite');
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
    public function init()
    {
        $this->user->pass('administrator');
        $this->onRequest('do', 'update')->updateGeneralSettings();
    }
}
