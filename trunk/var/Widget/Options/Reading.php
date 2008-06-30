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
class Widget_Options_Reading extends Widget_Abstract_Options implements Widget_Interface_DoWidget
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
        $form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Options/Reading.do', $this->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Submit(_t('保存设置'));
        $submit->button->setAttribute('class', 'submit');
        $form->addItem($submit->setAttribute('class', 'table_nav'));
        
        /** 文章日期格式 */
        $postDateFormat = new Typecho_Widget_Helper_Form_Text('postDateFormat', $this->postDateFormat,
        _t('文章日期格式'), _t('请参考<a href="http://cn.php.net/manual/zh/function.date.php" target="_blank">PHP日期格式写法</a>.'));
        $postDateFormat->input->setAttribute('class', 'text')->setAttribute('style', 'width:40%');
        $form->addInput($postDateFormat);
        
        /** 每页文章数目 */
        $pageSize = new Typecho_Widget_Helper_Form_Text('pageSize', $this->pageSize,
        _t('每页文章数目'), _t('此数目用于指定文章归档输出时每页显示的文章数目.'));
        $pageSize->input->setAttribute('class', 'text')->setAttribute('style', 'width:40%');
        $form->addInput($pageSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 文章列表数目 */
        $postsListSize = new Typecho_Widget_Helper_Form_Text('postsListSize', $this->postsListSize,
        _t('文章列表数目'), _t('此数目用于指定显示在侧边拦中的文章列表数目.'));
        $postsListSize->input->setAttribute('class', 'text')->setAttribute('style', 'width:40%');
        $form->addInput($postsListSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** FEED全文输出 */
        $feedFullArticlesLayout = new Typecho_Widget_Helper_Form_Radio('feedFullArticlesLayout', array('0' => _t('仅输出摘要'), '1' => _t('全文输出')),
        $this->feedFullArticlesLayout, _t('聚合全文输出'));
        $form->addInput($feedFullArticlesLayout);
        
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
    public function updateReadingSettings()
    {
        /** 验证格式  */
        try
        {
            $this->form()->validate();
        }
        catch(Typecho_Widget_Exception $e)
        {
            Typecho_API::goBack();
        }
    
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
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateReadingSettings'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
