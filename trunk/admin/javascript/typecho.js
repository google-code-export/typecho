/** 初始化全局对象 */
var Typecho = {};

Typecho.guid = function (el, config) {
    var _dl  = $(el);
    
    if (null == _dl) {
        return;
    }
    
    var _dt  = _dl.getElements('dt');
    var _dd  = _dl.getElements('dd');
    var _cur = null, _timer = null, _iframe = null;

    var handle = {
       reSet: function() {
           /*
           if (_cur) {
               console.info(_cur);
                //_cur.removeClass('current');
                //_cur.getNext('dd').setStyle('display', 'none');
                delete _cur;
           } else {
           */
                _dt.removeClass('current');
                _dd.setStyle('display', 'none');
            //}
        },

        popUp: function(el) {
            el = _cur =  $(el) || el;
            el.addClass('current');
            var _d = el.getNext('dd');
            if (_d) {
                _d.setStyle('left', el.getPosition().x - el.getParent('dl').getPosition().x - config.offset);
                if (_d.getStyle('display') != 'none') {
                    _d.setStyle('display', 'none');
                } else {
                    _d.setStyle('display', 'block');
                    _d.getElement('ul li:first-child').setStyle('border-top', 'none');
                    _d.getElement('ul li:last-child').setStyle('border-bottom', 'none');
                    _d.getElements('ul li').setStyle('width', _d.getCoordinates().width - 22);
                }
            }
        }
    };

   if (config.type == 'mouse') {
        _dt.addEvent('mouseenter', function(event){
            _timer = $clear(_timer); handle.reSet();
            if (event.target.nodeName.toLowerCase() == 'a') {
                event.target = $(event.target).getParent('dt');
            }

            handle.popUp(event.target);
        });

        _dt.addEvent('mouseout', function(event){
            if (!_timer) {
                _timer = handle.reSet.delay(500);
            }
        });

        _dd.addEvent('mouseenter', function(event){
            if (_timer) {
                _timer = $clear(_timer);
            }
        });

        _dd.addEvent('mouseleave', function(event){
            if (!_timer) {
                _timer = handle.reSet.delay(50);
            }
        });
    }

    if (config.type == 'click') {
        _dt.addEvent('click', function(event){
            handle.reSet();
            if (event.target.nodeName.toLowerCase() == 'a') {
                event.target = $(event.target).getParent('dt');
            }

            handle.popUp(event.target);
            event.stop(); // 停止事件传播
        });
        $(document).addEvent('click', handle.reSet);
    }

    return handle;
};

Typecho.Table = {
    
    table: null,        //当前表格
    
    draggable: false,    //是否可拖拽
    
    draggedEl: null,    //当前拖拽的元素
    
    draggedFired: false,    //是否触发

    init: function (match) {
        /** 初始化表格风格 */
        $(document).getElements(match).each(function (item) {
            Typecho.Table.table = item;
            Typecho.Table.draggable = item.hasClass('draggable');
            Typecho.Table.bindButtons();
            Typecho.Table.reset();
        });
    },
    
    reset: function () {
        var _el = Typecho.Table.table;
        Typecho.Table.draggedEl = null;
        
        if ('undefined' == typeof(_el._childTag)) {
            switch (_el.get('tag')) {
                case 'ul':
                    _el._childTag = 'li';
                    break;
                case 'table':
                    _el._childTag = 'tr';
                    break;
                default:
                    break;
            }
            
            var _cb = _el.getElements(_el._childTag + ' input[type=checkbox]').each(function (item) {
                item._parent = item.getParent(Typecho.Table.table._childTag);
               
                /** 监听click事件 */
                item.addEvent('click', Typecho.Table.checkBoxClick);
            });
        }
    
        /** 如果有even */
        var _hasEven = _el.getElements(_el._childTag + '.even').length > 0;
        
        _el.getElements(_el._childTag).filter(function (item, index) {
            /** 把th干掉 */
            return 'tr' != item.get('tag') || 0 == item.getChildren('th').length;
        }).each(function (item, index) {
            if (_hasEven) {
                /** 处理已经选择的选项 */
                if (index % 2) {
                    item.removeClass('even');
                } else {
                    item.addClass('even');
                }
                
                if (item.hasClass('checked') || item.hasClass('checked-even')) {
                    item.removeClass(index % 2 ? 'checked-even' : 'checked')
                    .addClass(index % 2 ? 'checked' : 'checked-even');
                }
            }
            
            Typecho.Table.bindEvents(item);
        });
    },
    
    checkBoxClick: function (event) {
        var _el = $(this);
        if (_el.getProperty('checked')) {
            _el.setProperty('checked', false);
            _el._parent.removeClass(_el._parent.hasClass('even') ? 'checked-even' : 'checked');
            Typecho.Table.unchecked(this, _el._parent);
        } else {
            _el.setProperty('checked', true);
            _el._parent.addClass(_el._parent.hasClass('even') ? 'checked-even' : 'checked');
            Typecho.Table.checked(this, _el._parent);
        }
    },
    
    itemMouseOver: function (event) {
        if(!Typecho.Table.draggedEl || Typecho.Table.draggedEl == this) {
            $(this).addClass('hover');
            
            //fix ie
            if (Browser.Engine.trident) {
                $(this).getElements('.hidden-by-mouse').setStyle('display', 'inline');
            }
        }
    },
    
    itemMouseLeave: function (event) {
        if(!Typecho.Table.draggedEl || Typecho.Table.draggedEl == this) {
            $(this).removeClass('hover');
            
            //fix ie
            if (Browser.Engine.trident) {
                $(this).getElements('.hidden-by-mouse').setStyle('display', 'none');
            }
        }
    },
    
    itemClick: function (event) {
        /** 触发多选框点击事件 */
        if ('undefined' != typeof(event)) {
            var _el = $(this).getElement('input[type=checkbox]'), _t = $(event.target);
            
            if (_el && ('a' != _t.get('tag')
            && ('input' != _t.get('tag') || ('text' != _t.get('type') && 'button' != _t.get('type') && 'submit' != _t.get('type')))
            && 'textarea' != _t.get('tag')
            && 'label' != _t.get('tag')
            && 'button' != _t.get('tag'))) {
                _el.fireEvent('click');
            }
        }
    },
    
    itemMouseDown: function (event) {
        if (!Typecho.Table.draggedEl) {
            Typecho.Table.draggedEl = this;
            Typecho.Table.draggedFired = false;
            return false;
        }
    },
    
    itemMouseMove: function (event) {
        if (Typecho.Table.draggedEl) {
        
            if (!Typecho.Table.draggedFired) {
                Typecho.Table.dragStart(this);
                $(this).setStyle('cursor', 'move');
                Typecho.Table.draggedFired = true;
            }
            
            if (Typecho.Table.draggedEl != this) {
                /** 从下面进来的 */
                if ($(this).getCoordinates(Typecho.Table.draggedEl).top < 0) {
                    $(this).inject(Typecho.Table.draggedEl, 'after');
                } else {
                    $(this).inject(Typecho.Table.draggedEl, 'before');
                }
                
                if ($(this).hasClass('even')) {
                    if (!$(Typecho.Table.draggedEl).hasClass('even')) {
                        $(this).removeClass('even');
                        $(Typecho.Table.draggedEl).addClass('even');
                    }
                    
                    if ($(this).hasClass('checked-even') && 
                    !$(Typecho.Table.draggedEl).hasClass('checked-even')) {
                        $(this).removeClass('checked-even');
                        $(Typecho.Table.draggedEl).addClass('checked-even');
                    }
                } else {
                    if ($(Typecho.Table.draggedEl).hasClass('even')) {
                        $(this).addClass('even');
                        $(Typecho.Table.draggedEl).removeClass('even');
                    }
                    
                    if ($(this).hasClass('checked') && 
                    $(Typecho.Table.draggedEl).hasClass('checked')) {
                        $(this).removeClass('checked');
                        $(Typecho.Table.draggedEl).addClass('checked');
                    }
                }
                
                return false;
            }
        }
    },
    
    itemMouseUp: function (event) {
        if (Typecho.Table.draggedEl) {
            var _inputs = Typecho.Table.table.getElements(Typecho.Table.table._childTag + ' input[type=checkbox]');
            var result = '';
            
            for (var i = 0; i< _inputs.length; i ++) {
                if (result.length > 0) result += '&';
                result += _inputs[i].name + '=' + _inputs[i].value;
            }
            
            if (Typecho.Table.draggedFired) {    
                $(this).fireEvent('click');
                $(this).setStyle('cursor', '');
                Typecho.Table.dragStop(this, result);
                Typecho.Table.draggedFired = false;
                Typecho.Table.reset();
            }
            
            Typecho.Table.draggedEl = null;
            return false;
        }
    },
    
    checked:   function (input, item) {return false;},
    
    unchecked: function (input, item) {return false;},
    
    dragStart: function (item) {return false;},
    
    dragStop: function (item, result) {return false;},
    
    bindButtons: function () {
        /** 全选按钮 */
        $(document).getElements('.typecho-table-select-all')
        .addEvent('click', function () {
            Typecho.Table.table.getElements(Typecho.Table.table._childTag + ' input[type=checkbox]')
            .each(function (item) {
                if (!item.getProperty('checked')) {
                    item.fireEvent('click');
                }
            });
        });
        
        /** 不选按钮 */
        $(document).getElements('.typecho-table-select-none')
        .addEvent('click', function () {
            Typecho.Table.table.getElements(Typecho.Table.table._childTag + ' input[type=checkbox]')
            .each(function (item) {
                if (item.getProperty('checked')) {
                    item.fireEvent('click');
                }
            });
        });
        
        /** 提交按钮 */
        $(document).getElements('.typecho-table-select-submit')
        .addEvent('click', function () {
            var _lang = this.get('lang');
            var _c = _lang ? confirm(_lang) : true;
            
            if (_c) {
                var _f = Typecho.Table.table.getParent('form');
                _f.getElement('input[name=do]').set('value', $(this).getProperty('rel'));
                _f.submit();
            }
        });
    },
    
    bindEvents: function (item) {
        item.removeEvents();

        item.addEvents({
            'mouseover': Typecho.Table.itemMouseOver,
            'mouseleave': Typecho.Table.itemMouseLeave,
            'click': Typecho.Table.itemClick
        });

        if (Typecho.Table.draggable && 
        Typecho.Table.table.getElements(Typecho.Table.table._childTag + ' input[type=checkbox]').length > 0) {
            item.addEvents({
                'mousedown': Typecho.Table.itemMouseDown,
                'mousemove': Typecho.Table.itemMouseMove,
                'mouseup': Typecho.Table.itemMouseUp
            });
        }
    }
};

Typecho.toggleEl = null;
Typecho.toggleBtn = null;
Typecho.toggleHideWord = null;
Typecho.toggleOpened = false;

Typecho.toggle = function (sel, btn, showWord, hideWord) {
    var el = $(document).getElement(sel);
    
    if (null != Typecho.toggleBtn && btn != Typecho.toggleBtn) {
        $(Typecho.toggleBtn).set('html', Typecho.toggleHideWord);
        Typecho.toggleEl.setStyle('display', 'none');
        Typecho.toggleEl.fireEvent('tabHide');
        $(Typecho.toggleBtn).toggleClass('close');
    }
    
    $(btn).toggleClass('close');
    if ('none' == el.getStyle('display')) {
        $(btn).set('html', showWord);
        el.setStyle('display', 'block');
        el.fireEvent('tabShow');
        Typecho.toggleOpened = true;
    } else {
        $(btn).set('html', hideWord);
        el.setStyle('display', 'none');
        el.fireEvent('tabHide');
        Typecho.toggleOpened = false;
    }
    
    Typecho.toggleEl = el;
    Typecho.toggleBtn = btn;
    Typecho.toggleHideWord = hideWord;
};

/** 自动保存组件 */
Typecho.autoSave = new Class({

    //继承自Options
    Implements: [Options],
    
    //内部选项
    options: {
        time: 10,   //间隔
        getContentHandle: null, //获取内容函数
        messageElement: null,
        leaveMessage: 'leave?',
        form: null
    },

    initialize: function (url, options) {
        this.setOptions(options);
        this.duration = 0;
        this.start = false;
        this.url = url;
        this.rev = 0;
        this.saveRev = 0;
        
        window.onbeforeunload = this.leaveListener.bind(this);
        $(this.options.form).getElements('.submit button').addEvent('mousedown', (function () {
            this.saveRev = this.rev;
        }).bind(this));
        
        //时间间隔计数器
        (function () {
            if (this.start) {
                this.duration ++;
            }
            
            if (this.duration > this.options.time) {
                this.start = false;
                this.onContentChange();
            }
        }).periodical(1000, this);
    },
    
    //离开页面监听器
    leaveListener: function () {
        if (this.saveRev != this.rev) {
            return this.options.leaveMessage;
        }
    },
    
    //内容改变监听器
    onContentChange: function () {
        this.start = true;
        this.rev ++;
        
        if (this.duration > this.options.time) {
        
            var o = {text: this.options.getContentHandle()};
            this.start = false;
            this.duration = 0;
            this.saveText = o.text;
            this.saveRev = this.rev;
            $(this.options.form).getElement('input[name=do]').set('value', 'save');
        
            new Request.JSON({
                url: this.url,
                
                onSuccess: (function (responseJSON) {
                    if (responseJSON.success) {
                        $(this.options.form).getElement('input[name=cid]').set('value', responseJSON.cid);
                    }
                    
                    if (null != this.options.messageElement) {
                        $(this.options.messageElement).set('html', responseJSON.message);
                        $(this.options.messageElement).highlight('#ff0000');
                    }
                    
                }).bind(this)
            }).send($(this.options.form).toQueryString() + Hash.toQueryString(o));
        }
    }
});

/** 文本编辑器插入文字 */
Typecho.textarea = new Class({

    //继承自Options
    Implements: [Options],

    //内部选项
    options: {
        resizeAble: false,  //能否调整大小
        resizeClass: 'size-btn',    //调整大小的class名
        resizeUrl: '',  //调整大小后的请求地址
        autoSave: false,
        autoSaveMessageElement: null,
        autoSaveLeaveMessage: 'leave?',
        autoSaveTime: 60,
        minSize: 30
    },

    initialize: function (el, options) {
        this.textarea = $(document).getElement(el);
        this.range = null;
        this.setOptions(options);
        
        if (this.options.autoSave) {
            this.autoSave = new Typecho.autoSave(this.textarea.getParent('form').getProperty('action'), {
                time: this.options.autoSaveTime,
                getContentHandle: this.getContent.bind(this),
                messageElement: this.options.autoSaveMessageElement,
                leaveMessage: this.options.autoSaveLeaveMessage,
                form: this.textarea.getParent('form')
            });
        }
        
        var recordRangeCallback = this.recordRange.bind(this);
        
        this.textarea.addEvents({
            mouseup: recordRangeCallback,
            keyup: (function () {
                recordRangeCallback();
                if (this.options.autoSave) {
                    this.autoSave.onContentChange();
                }
            }).bind(this)
        });

        if (this.options.resizeAble) {
            this.makeResizeAble();
        }
    },
    
    //记录当前位置
    recordRange: function () {
        this.range = this.textarea.getSelectedRange();
    },
    
    //设置当前编辑域为可调整大小
    makeResizeAble: function () {
        this.resizeOffset = this.textarea.getStyle('height') ? 
        this.textarea.getSize().y - parseInt(this.textarea.getStyle('height')) : 0;
        this.resizeMouseY = 0;
        this.lastMouseY = 0;
        
        //是否在调整区域按下鼠标
        this.isResizePressed = false;
        
        //创建调整区
        var cross = new Element('span', {
            
            'class': this.options.resizeClass,
            
            'events': {
                mousedown: this.resizeMouseDown.bind(this)
            }
        }).inject(this.textarea, 'after');
        
        //截获事件
        $(document).addEvents({
            mouseup: this.resizeMouseUp.bind(this),
            mousemove: this.resizeMouseMove.bind(this)
        });
        
        //监听事件
        this.resizeListener.periodical(10, this);
    },
    
    //监听调整区
    resizeListener: function () {
        if (this.isResizePressed) {
            var resize = (0 == this.lastMouseY) ? 0 : this.resizeMouseY - this.lastMouseY;
            this.lastMouseY = this.resizeMouseY;
            
            var finalY = this.textarea.getSize().y - this.resizeOffset + resize;
            
            if (finalY > this.options.minSize) {
                this.textarea.setStyle('height', finalY);
            }
        }
    },
    
    //按下调整区
    resizeMouseDown: function (e) {
        this.isResizePressed = true;
        e.stop();
    },
    
    //松开调整区
    resizeMouseUp: function (e) {
        if (this.isResizePressed) {
            this.isResizePressed = false;
            
            var size = this.textarea.getSize().y - this.resizeOffset;
            
            //发送ajax请求
            new Request({
                'method': 'post',
                'url': this.options.resizeUrl
            }).send('size=' + size + '&do=editorResize');
            
            this.resizeMouseY = 0;
            this.lastMouseY = 0;
        }
    },
    
    //移动调整区
    resizeMouseMove: function (e) {
        if (this.isResizePressed) {
            this.resizeMouseY = e.page.y;
        }
    },
    
    //获取内容
    getContent: function () {
        return this.textarea.get('value');
    },
    
    //设置当前选定的内容
    setContent: function (before, after) {
        var range = (null == this.range) ? this.textarea.getSelectedRange() : this.range,
        text = this.textarea.get('value'),
        selectedText = text.substr(range.start, range.end - range.start),
        scrollTop = this.textarea.scrollTop;
        
        //alert(textarea.selectionStart);
        
        this.textarea.set('value', text.substr(0, range.start) + before + selectedText
        + after + text.substr(range.end));
        
        (function () {
            this.textarea.scrollTop = scrollTop;
        }).bind(this).delay(0);

        this.textarea.focus();
        this.textarea.selectRange(range.start, range.end + before.length + after.length);
    }
});

/** 自动完成 */
Typecho.autoComplete = function (match, token) {
    var _sp = ',', _index, _cur = -1, _hoverList = false, _remember = 0,
    _el = $(document).getElement(match).setProperty('autocomplete', 'off');
    
    //创建搜索索引
    var _build = function () {
        var _len = 0, _val = _el.get('value');
        _index = [];
        
        if (_val.length > 0) {
            _val.split(_sp).each(function (item, index) {
                var _final = _len + item.length,
                _l = 0, _r = 0;
                
                item = item.replace(/(\s*)(.*)(\s*)/, function (v, a, b, c) {
                    _l = a.length;
                    _r = c.length;
                    return b;
                });
            
                _index[index] = {
                    txt: item,
                    start: index*1 + _len,
                    end: index*1 + _final,
                    offsetStart: index*1 + _len + _l,
                    offsetEnd: index*1 + _final - _r
                };
                
                _len = _final;
            });
        }
    };
    
    //获取当前keyword
    var _keyword = function (s, pos) {
        return pos ? pos.txt.substr(0, s - pos.offsetStart) : '';
    };
    
    //搜索token
    var _match = function (keyword) {
        var matchCase = keyword.length > 0 ? token.filter(function (item) {
            return 0 == item.indexOf(keyword);
        }) : [];
        
        var matchOther = keyword.length > 0 ? token.filter(function (item) {
            return (0 == item.toLowerCase().indexOf(keyword.toLowerCase()) && !matchCase.contains(item));
        }) : []; 
        
        return matchCase.extend(matchOther);
    };
    
    //选择特定元素
    var _select = function (s, pos) {
        _el.selectRange(pos.offsetStart > s ? pos.offsetStart : s, pos.offsetEnd);
    };
    
    //定位
    var _location = function (s) {
        for (var i in _index) {
            if (s >= _index[i].start && s <= _index[i].end) {
                return _index[i];
            }
        }
        
        return false;
    };
    
    //替换
    var _replace = function (w, s, e) {
        var _val = _el.get('value');
        return _el.set('value', _val.substr(0, s) + w + _val.substr(e));
    };
    
    //显示
    var _show = function (key, list) {
        _cur = -1;
        _hoverList = false;
    
        var _ul = new Element('ul', {
            'class': 'autocompleter-choices',
            'styles': {
                'width': _el.getSize().x - 2,
                'left': _el.getPosition().x,
                'top': _el.getPosition().y + _el.getSize().y
            }
        });
        
        list.each(function (item, index) {
        
            _ul.grab(new Element('li', {
                'rel': index,
                'html': '<span class="autocompleter-queried">' + item.substr(0, key.length)
                    + '</span>' + item.substr(key.length),
                'events': {
                    
                    'mouseover': function () {
                        _hoverList = true;
                        this.addClass('autocompleter-hover');
                    },
                    
                    'mouseleave': function () {
                        _hoverList = false;
                        this.removeClass('autocompleter-hover');
                    },
                    
                    'click': function () {
                        var _i = parseInt(this.get('rel'));
                        var _start = _remember > 0 ? _remember : _el.getSelectedRange().start,
                        _pos = _location(_start);

                        _replace(list[_i], _pos.offsetStart, _pos.offsetEnd);
                        _build();
                        
                        _pos = _location(_start);
                        _el.selectRange(_pos.offsetEnd, _pos.offsetEnd);
                        _hide();
                    }
                }
            }));
        });
        
       $(document).getElement('body').grab(_ul);
    };
    
    var _hide = function () {
        var _e = $(document).getElement('.autocompleter-choices');
        
        if (_e) {
            _e.destroy();
            _hoverList = false;
        }
    };
    
    _build();
    
    var _k, _l;
    
    //绑定事件
    _el.addEvents({
        
        'mouseup': function (e) {
            var _start = _el.getSelectedRange().start,
            _pos = _location(_start);
            _hide();
            _select(_start, _pos);
            this.fireEvent('keyup', e);
            
            _remember = _el.getSelectedRange().end;
            
            e.stop();
            return false;
        },
        
        'blur': function () {            
            if (!_hoverList) {
                _hide();
            }
        },
        
        'keydown': function (e) {
            _build();
            var _start = _el.getSelectedRange().start,
            _pos = _location(_start);
            
            _remember = _el.getSelectedRange().end;
            
            switch (e.key) {
                case 'up':
                
                    if (_l.length > 0 && _cur >= 0) {
                        if (_cur < _l.length) {
                            $(document).getElement('.autocompleter-choices li[rel=' + _cur + ']').removeClass('autocompleter-selected');
                        }

                        if (_cur > 0) {
                            _cur --;
                        } else {
                            _cur = _l.length - 1;
                        }
                        
                        $(document).getElement('.autocompleter-choices li[rel=' + _cur + ']').addClass('autocompleter-selected');
                        _replace(_l[_cur], _pos.offsetStart, _pos.offsetEnd);
                        _build();

                        _pos = _location(_start);
                        _select(_start, _pos);
                    }
                    
                    e.stop();
                    return false;
                
                case 'down':

                    if (_l.length > 0 && _cur < _l.length) {
                        if (_cur >= 0) {
                            $(document).getElement('.autocompleter-choices li[rel=' + _cur + ']').removeClass('autocompleter-selected');
                        }
                    
                        if (_cur < _l.length - 1) {
                            _cur ++;
                        } else {
                            _cur = 0;
                        }
                        
                        $(document).getElement('.autocompleter-choices li[rel=' + _cur + ']').addClass('autocompleter-selected');
                        _replace(_l[_cur], _pos.offsetStart, _pos.offsetEnd);
                        _build();

                        _pos = _location(_start);
                        _select(_start, _pos);
                    }
                    
                    e.stop();
                    return false;
                    
                case 'enter':
                    _hide();
                    _el.selectRange(_pos.offsetEnd, _pos.offsetEnd);
                    e.stop();
                    return false;
                    
                default:
                    break;
            }
        },
        
        'keyup': function (e) {
        
            _build();
            var _start = _el.getSelectedRange().start,
            _pos = _location(_start);
            
            _remember = _el.getSelectedRange().end;
        
            switch (e.key) {
                    
                case 'left':
                case 'right':
                case 'backspace':
                case 'delete':
                case 'esc':
                    
                    _hide();
                    e.key = 'a';
                    this.fireEvent('keyup', e, 1000);
                    break;
                    
                case 'enter':
                    return false;
                    
                case 'up':
                case 'down':
                    return false;
                
                case 'space':
                default:
                    _hide();
                    _k = _keyword(_start, _pos);
                    _l = _match(_k);
                        
                    if (_l.length > 0) {
                        
                        /*
                        if (0 == _l[0].indexOf(_k) && 'undefined' == typeof(e.shoot)) {
                            //_replace(_l[0], _pos.offsetStart, _pos.offsetEnd);
                            _build();
                            _pos = _location(_start);
                        }
                        */
                        
                        _select(_start, _pos);
                        _show(_k, _l);
                    }
                    
                    break;
            }
        }
        
    });
};
