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
class Widget_Options_General extends Widget_Abstract_Options implements Widget_Interface_DoWidget
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
        $form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Options/General.do', $this->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Submit(_t('保存设置'));
        $submit->button->setAttribute('class', 'submit');
        $form->addItem($submit->setAttribute('class', 'submit_nav'));
        
        /** 站点名称 */
        $title = new Typecho_Widget_Helper_Form_Text('title', $this->title, _t('站点名称'), _t('站点的名称将显示在网页的标题处.'));
        $title->input->setAttribute('class', 'text')->setAttribute('style', 'width:70%');
        $form->addInput($title);
        
        /** 站点描述 */
        $description = new Typecho_Widget_Helper_Form_Textarea('description', $this->description, _t('站点描述'), _t('站点描述将显示在网页代码的头部.'));
        $description->input->setAttribute('style', 'width:90%')->setAttribute('rows', '5');
        $form->addInput($description);
        
        /** 关键词 */
        $keywords = new Typecho_Widget_Helper_Form_Text('keywords', $this->keywords, _t('关键词'), _t('请以半角逗号","分割多个关键字.'));
        $keywords->input->setAttribute('class', 'text')->setAttribute('style', 'width:60%');
        $form->addInput($keywords);
        
        /** 时区 */
        $timezone = new Typecho_Widget_Helper_Form_Select('timezone', array('28800' => _t('中华人民共和国')), $this->timezone, _t('时区'));
        $form->addInput($timezone);
        
        /** 动作 */
        $do = new Typecho_Widget_Helper_Form_Hidden('do', 'update');
        $form->addInput($do);
        
        /** 空格 */
        $form->addItem(new Typecho_Widget_Helper_Layout('hr', array('class' => 'space')));
        
        /** 提交按钮 */
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
        $settings = $this->form()->getParameters();
        unset($settings['do']);
        foreach($settings as $name => $value)
        {
            $this->update(array('value' => $value), $this->db->sql()->where('`name` = ?', $name));
        }
        
        Typecho_API::factory('Widget_Notice')->set(_t("设置已经保存"), NULL, 'success');
        Typecho_API::goBack();
    }

    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_API::factory('Widget_Users_Current')->pass('administrator');
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateGeneralSettings'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
