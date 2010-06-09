<?php
/**
 * 评论设置
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论设置组件
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options_Discussion extends Widget_Abstract_Options implements Widget_Interface_Do
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
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/options-discussion', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);

        /** 评论日期格式 */
        $commentDateFormat = new Typecho_Widget_Helper_Form_Element_Text('commentDateFormat', NULL, $this->options->commentDateFormat,
        _t('评论日期格式'), _t('这是一个默认的格式,当你在模板中调用显示评论日期方法时, 如果没有指定日期格式, 将按照此格式输出.<br />
        具体写法请参考<a href="http://cn.php.net/manual/zh/function.date.php">PHP日期格式写法</a>.'));
        $form->addInput($commentDateFormat);

        /** 评论列表数目 */
        $commentsListSize = new Typecho_Widget_Helper_Form_Element_Text('commentsListSize', NULL, $this->options->commentsListSize,
        _t('评论列表数目'), _t('此数目用于指定显示在侧边拦中的评论列表数目.'));
        $commentsListSize->input->setAttribute('class', 'mini');
        $form->addInput($commentsListSize->addRule('isInteger', _t('请填入一个数字')));

        $commentsShowOptions = array(
            'commentsShowCommentOnly'   =>  _t('仅显示评论, 不显示Pingback和Trackback'),
            'commentsShowUrl'       =>  _t('评论者名称显示时自动加上其个人主页链接'),
            'commentsUrlNofollow'   =>  _t('对评论者个人主页链接使用<a href="http://en.wikipedia.org/wiki/Nofollow">nofollow属性</a>'),
            'commentsAvatar'        =>  _t('启用<a href="http://gravatar.com">Gravatar</a>头像服务, 最高显示评级为 %s 的头像',
            '</label><select id="commentsShow-commentsAvatarRating" name="commentsAvatarRating">
            <option value="G"' . ('G' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>G - 普通</option>
            <option value="PG"' . ('PG' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>PG - 13岁以上</option>
            <option value="R"' . ('R' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>R - 17岁以上成人</option>
            <option value="X"' . ('X' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>X - 限制级</option></select>
            <label for="commentsShow-commentsAvatarRating">'),
            'commentsPageBreak'     =>  _t('启用分页, 并且每页显示 %s 篇评论, 在列出时将 %s 作为默认显示',
            '</label><input type="text" value="' . $this->options->commentsPageSize
            . '" class="text num" id="commentsShow-commentsPageSize" name="commentsPageSize" /><label for="commentsShow-commentsPageSize">',
            '</label><select id="commentsShow-commentsPageDisplay" name="commentsPageDisplay">
            <option value="first"' . ('first' == $this->options->commentsPageDisplay ? ' selected="true"' : '') . '>' . _t('第一页') . '</option>
            <option value="last"' . ('last' == $this->options->commentsPageDisplay ? ' selected="true"' : '') . '>' . _t('最后一页') . '</option></select>'
            . '<label for="commentsShow-commentsPageDisplay">'),
            'commentsThreaded'      =>  _t('启用评论回复, 以 %s 层作为每个评论最多的回复层数',
            '</label><input name="commentsMaxNestingLevels" type="text" class="text num" value="' . $this->options->commentsMaxNestingLevels . '" id="commentsShow-commentsMaxNestingLevels" />
            <label for="commentsShow-commentsMaxNestingLevels">') . '</label></span><span class="multiline">'
            . _t('将 %s 的评论显示在前面', '<select id="commentsShow-commentsOrder" name="commentsOrder">
            <option value="DESC"' . ('DESC' == $this->options->commentsOrder ? ' selected="true"' : '') . '>' . _t('较新的') . '</option>
            <option value="ASC"' . ('ASC' == $this->options->commentsOrder ? ' selected="true"' : '') . '>' . _t('较旧的') . '</option></select><label for="commentsShow-commentsOrder">')
        );

        $commentsShowOptionsValue = array();
        if ($this->options->commentsShowCommentOnly) {
            $commentsShowOptionsValue[] = 'commentsShowCommentOnly';
        }

        if ($this->options->commentsShowUrl) {
            $commentsShowOptionsValue[] = 'commentsShowUrl';
        }

        if ($this->options->commentsUrlNofollow) {
            $commentsShowOptionsValue[] = 'commentsUrlNofollow';
        }
        
        if ($this->options->commentsAvatar) {
            $commentsShowOptionsValue[] = 'commentsAvatar';
        }

        if ($this->options->commentsPageBreak) {
            $commentsShowOptionsValue[] = 'commentsPageBreak';
        }

        if ($this->options->commentsThreaded) {
            $commentsShowOptionsValue[] = 'commentsThreaded';
        }

        $commentsShow = new Typecho_Widget_Helper_Form_Element_Checkbox('commentsShow', $commentsShowOptions,
        $commentsShowOptionsValue, _t('评论显示'));
        $form->addInput($commentsShow->multiMode());

        /** 评论提交 */
        $commentsPostOptions = array(
            'commentsRequireModeration'     =>  _t('所有评论必须经过审核'),
            'commentsRequireMail'           =>  _t('必须填写邮箱'),
            'commentsRequireURL'            =>  _t('必须填写网址'),
            'commentsCheckReferer'          =>  _t('检查评论来源页URL是否与文章链接一致'),
            'commentsAutoClose'             =>  _t('在文章发布 %s 天以后自动关闭评论',
            '</label><input name="commentsPostTimeout" type="text" class="text num" value="' . intval($this->options->commentsPostTimeout / (24 * 3600)) . '" id="commentsPost-commentsPostTimeout" />
            <label for="commentsPost-commentsPostTimeout">'),
            'commentsPostIntervalEnable'    =>  _t('同一IP发布评论的时间间隔限制为 %s 分钟',
            '</label><input name="commentsPostInterval" type="text" class="text num" value="' . round($this->options->commentsPostInterval / (60), 1) . '" id="commentsPost-commentsPostInterval" />
            <label for="commentsPost-commentsPostInterval">')
        );

        $commentsPostOptionsValue = array();
        if ($this->options->commentsRequireModeration) {
            $commentsPostOptionsValue[] = 'commentsRequireModeration';
        }

        if ($this->options->commentsRequireMail) {
            $commentsPostOptionsValue[] = 'commentsRequireMail';
        }

        if ($this->options->commentsRequireURL) {
            $commentsPostOptionsValue[] = 'commentsRequireURL';
        }

        if ($this->options->commentsCheckReferer) {
            $commentsPostOptionsValue[] = 'commentsCheckReferer';
        }

        if ($this->options->commentsAutoClose) {
            $commentsPostOptionsValue[] = 'commentsAutoClose';
        }

        if ($this->options->commentsPostIntervalEnable) {
            $commentsPostOptionsValue[] = 'commentsPostIntervalEnable';
        }

        $commentsPost = new Typecho_Widget_Helper_Form_Element_Checkbox('commentsPost', $commentsPostOptions,
        $commentsPostOptionsValue, _t('评论提交'));
        $form->addInput($commentsPost->multiMode());

        /** 允许使用的HTML标签和属性 */
        $commentsHTMLTagAllowed = new Typecho_Widget_Helper_Form_Element_Textarea('commentsHTMLTagAllowed', NULL,
        htmlspecialchars($this->options->commentsHTMLTagAllowed),
        _t('允许使用的HTML标签和属性'), _t('默认的用户评论不允许填写任何的HTML标签, 你可以在这里填写允许使用的HTML标签.<br />
        比如: &lt;a href=&quot;&quot;&gt; &lt;img src=&quot;&quot;&gt; &lt;blockquote&gt;'));
        $form->addInput($commentsHTMLTagAllowed);

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
    public function updateDiscussionSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }

        $settings = $this->request->from('commentDateFormat', 'commentsListSize', 'commentsShow', 'commentsPost', 'commentsPageSize', 'commentsPageDisplay', 'commentsAvatar',
                'commentsOrder', 'commentsMaxNestingLevels', 'commentsUrlNofollow', 'commentsPostTimeout', 'commentsUniqueIpInterval', 'commentsRequireMail', 'commentsAvatarRating',
                'commentsPostTimeout', 'commentsPostInterval', 'commentsRequireModeration', 'commentsRequireURL', 'commentsHTMLTagAllowed', 'commentsStopWords', 'commentsIpBlackList');

        $settings['commentsShowCommentOnly'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsShowCommentOnly');
        $settings['commentsShowUrl'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsShowUrl');
        $settings['commentsUrlNofollow'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsUrlNofollow');
        $settings['commentsAvatar'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsAvatar');
        $settings['commentsPageBreak'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsPageBreak');
        $settings['commentsThreaded'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsThreaded');

        $settings['commentsPageSize'] = intval($settings['commentsPageSize']);
        $settings['commentsMaxNestingLevels'] = max(2, intval($settings['commentsMaxNestingLevels']));
        $settings['commentsPageDisplay'] = ('first' == $settings['commentsPageDisplay']) ? 'first' : 'last';
        $settings['commentsOrder'] = ('DESC' == $settings['commentsOrder']) ? 'DESC' : 'ASC';
        $settings['commentsAvatarRating'] = in_array($settings['commentsAvatarRating'], array('G', 'PG', 'R', 'X'))
            ? $settings['commentsAvatarRating'] : 'G';

        $settings['commentsRequireModeration'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsRequireModeration');
        $settings['commentsRequireMail'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsRequireMail');
        $settings['commentsRequireURL'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsRequireURL');
        $settings['commentsCheckReferer'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsCheckReferer');
        $settings['commentsAutoClose'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsAutoClose');
        $settings['commentsPostIntervalEnable'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsPostIntervalEnable');

        $settings['commentsPostTimeout'] = intval($settings['commentsPostTimeout']) * 24 * 3600;
        $settings['commentsPostInterval'] = round($settings['commentsPostInterval'], 1) * 60;

        unset($settings['commentsShow']);
        unset($settings['commentsPost']);

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
        $this->on($this->request->isPost())->updateDiscussionSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
