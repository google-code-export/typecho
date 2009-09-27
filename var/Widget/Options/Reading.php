<?php
/**
 * 文章阅读设置
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 文章阅读设置组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options_Reading extends Widget_Abstract_Options implements Widget_Interface_Do
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
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/options-reading', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 文章日期格式 */
        $postDateFormat = new Typecho_Widget_Helper_Form_Element_Text('postDateFormat', NULL, $this->options->postDateFormat,
        _t('文章日期格式'), _t('此格式用于指定显示在文章归档中的日期默认显示格式.<br />
        在某些主题中这个格式可能不会生效, 因为主题作者可以自定义日期格式.<br />
        请参考<a href="http://cn.php.net/manual/zh/function.date.php">PHP日期格式写法</a>.'));
        $form->addInput($postDateFormat);
        
        /** 每页文章数目 */
        $pageSize = new Typecho_Widget_Helper_Form_Element_Text('pageSize', NULL, $this->options->pageSize,
        _t('每页文章数目'), _t('此数目用于指定文章归档输出时每页显示的文章数目.'));
        $pageSize->input->setAttribute('class', 'mini');
        $form->addInput($pageSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 文章列表数目 */
        $postsListSize = new Typecho_Widget_Helper_Form_Element_Text('postsListSize', NULL, $this->options->postsListSize,
        _t('文章列表数目'), _t('此数目用于指定显示在侧边拦中的文章列表数目.'));
        $postsListSize->input->setAttribute('class', 'mini');
        $form->addInput($postsListSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** FEED全文输出 */
        $feedFullText = new Typecho_Widget_Helper_Form_Element_Radio('feedFullText', array('0' => _t('仅输出摘要'), '1' => _t('全文输出')),
        $this->options->feedFullText, _t('聚合全文输出'), _t('如果你不希望在聚合中输出文章全文,请使用仅输出摘要选项.<br />
        摘要的文字取决于你在文章中使用分隔符的位置.'));
        $form->addInput($feedFullText);
        
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
    public function updateReadingSettings()
    {
        /** 验证格式  */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
    
        $settings = $this->request->from('postDateFormat', 'pageSize', 'postsListSize', 'feedFullText');
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
        $this->on($this->request->isPost())->updateReadingSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
