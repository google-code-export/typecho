<?php
/**
 * 撰写习惯设置
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 撰写习惯设置组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options_Writing extends Widget_Abstract_Options implements Widget_Interface_Action_Widget
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
        $form = new Typecho_Widget_Helper_Form(Typecho_API::pathToUrl('/Options/Writing.do', $this->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Submit(_t('保存设置'));
        $submit->button->setAttribute('class', 'submit');
        $form->addItem($submit);
        
        /** 编辑器大小 */
        $editorSize = new Typecho_Widget_Helper_Form_Text('editorSize', $this->editorSize,
        _t('编辑器大小'), _t('所见即所得编辑器的大小.'));
        $editorSize->input->setAttribute('class', 'text')->setAttribute('style', 'width:40%');
        $form->addInput($editorSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 自动保存 */
        $autoSave = new Typecho_Widget_Helper_Form_Radio('autoSave',
        array('0' => _t('关闭'), '1' => _t('打开')),
        $this->autoSave, _t('自动保存'), _t('自动保存功能可以更好地保护您的文章不会丢失.'));
        $form->addInput($autoSave);
        
        /** 默认允许评论 */
        $defaultAllowComment = new Typecho_Widget_Helper_Form_Radio('defaultAllowComment',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->defaultAllowComment, _t('默认允许评论'));
        $form->addInput($defaultAllowComment);
        
        /** 默认允许广播 */
        $defaultAllowPing = new Typecho_Widget_Helper_Form_Radio('defaultAllowPing',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->defaultAllowPing, _t('默认允许广播'));
        $form->addInput($defaultAllowPing);
        
        /** 默认允许聚合*/
        $defaultAllowFeed = new Typecho_Widget_Helper_Form_Radio('defaultAllowFeed',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->defaultAllowFeed, _t('默认允许聚合'));
        $form->addInput($defaultAllowFeed);
        
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
    public function updateWritingSettings()
    {
        /** 验证格式 */
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
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updateWritingSettings'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
