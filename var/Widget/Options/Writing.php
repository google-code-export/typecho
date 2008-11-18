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
class Widget_Options_Writing extends Widget_Abstract_Options implements Widget_Interface_Do
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
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::pathToUrl('/Options/Writing.do', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 编辑器大小 */
        $editorSize = new Typecho_Widget_Helper_Form_Element_Text('editorSize', NULL, $this->options->editorSize,
        _t('编辑器大小'), _t('所见即所得编辑器的大小.'));
        $form->addInput($editorSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 自动保存 */
        $autoSave = new Typecho_Widget_Helper_Form_Element_Radio('autoSave',
        array('0' => _t('关闭'), '1' => _t('打开')),
        $this->options->autoSave, _t('自动保存'), _t('自动保存功能可以更好地保护您的文章不会丢失.'));
        $form->addInput($autoSave);
        
        /** 默认允许评论 */
        $defaultAllowComment = new Typecho_Widget_Helper_Form_Element_Radio('defaultAllowComment',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->options->defaultAllowComment, _t('默认允许评论'));
        $form->addInput($defaultAllowComment);
        
        /** 默认允许广播 */
        $defaultAllowPing = new Typecho_Widget_Helper_Form_Element_Radio('defaultAllowPing',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->options->defaultAllowPing, _t('默认允许广播'));
        $form->addInput($defaultAllowPing);
        
        /** 默认允许聚合*/
        $defaultAllowFeed = new Typecho_Widget_Helper_Form_Element_Radio('defaultAllowFeed',
        array('0' => _t('不允许'), '1' => _t('允许')),
        $this->options->defaultAllowFeed, _t('默认允许聚合'));
        $form->addInput($defaultAllowFeed);
        
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
    public function updateWritingSettings()
    {
        /** 验证格式 */
        try {
            $this->form()->validate();
        } catch (Typecho_Widget_Exception $e) {
            Typecho_API::goBack();
        }
    
        $settings = $this->request->from('editorSize', 'autoSave', 'defaultAllowComment', 'defaultAllowPing', 'defaultAllowFeed');
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
        $this->onPost()->updateWritingSettings();
    }
}
