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
        var _el;
        if (_el = $(this).getElement('input[type=checkbox]')) {
            _el.fireEvent('click');
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

/** tinyMCE编辑器封装 */
Typecho.currentEditor = '';

Typecho.isRichEditor = function () {
    return 'vw' == Typecho.currentEditor;
};

Typecho.tinyMCE = function (id, url, vw, cw, current) {

    var _currentY = parseInt($(id).getStyle('height')), _ed;
    Typecho.currentEditor = current;
    
    var _transfer = function () {
    
        var _r = new Request({
            'method': 'post',
            'url': url
        }).send('content=' + encodeURIComponent($(id).get('value')) + '&do=cutParagraph');
    
        _r.addEvent('onSuccess', function (responseText) {
            _ed.setContent(decodeURIComponent(responseText));
            $(id).removeProperty('disabled');
        });
    
    };
    
    var _toCode = function () {
    
        $('typecho-editor-tab-cw').addClass('loading');
        var _r = new Request({
            'method': 'post',
            'url': url
        }).send('content=' + encodeURIComponent(_ed.getContent()) + '&do=removeParagraph');
        
        _r.addEvent('onSuccess', function (responseText) {
            $(id).set('value', ' ');    //webkit的鸟浏览器非要这么搞一下
            $(id).set('value', decodeURIComponent(responseText));
            $('typecho-editor-tab-cw').removeClass('loading');
            $(id + '_parent').setStyle('display', 'none');
            $(id).setStyle('display', 'block');
        });
    
    };
    
    var _toVisual = function () {
    
        $('typecho-editor-tab-vw').addClass('loading');
        var _r = new Request({
            'method': 'post',
            'url': url
        }).send('content=' + encodeURIComponent($(id).get('value')) + '&do=cutParagraph');
        
        _r.addEvent('onSuccess', function (responseText) {
            _ed.setContent(decodeURIComponent(responseText));
            $('typecho-editor-tab-vw').removeClass('loading');
            $(id + '_parent').setStyle('display', 'block');
            $(id).setStyle('display', 'none');
        });
    
    };
    
    var _show = function () {
        if ('cw' == current) {
            $(id + '_parent').setStyle('display', '');
        } else {
            $(id).setStyle('display', '');
        }
    };
    
    var _hide = function () {
        if ('cw' == current) {
            $(id + '_parent').setStyle('display', 'none');
        } else {
            $(id).setStyle('display', 'none');
        }
    };
    
    tinyMCE.init({
        // General options
        mode : "exact",
        elements : id,
        theme : "advanced",
        skin : "typecho",
        plugins : "safari,morebreak,inlinepopups,media,coder",
        extended_valid_elements : "code[*],pre[*],script[*],iframe[*]",
        
        //Event setup
        setup : function(ed) {
        
            var _tab = new Element('ul', {'class': 'typecho-editor-tab'})
            .grab(new Element('li', {'text': vw, 'id': 'typecho-editor-tab-vw', 'events': {
            
                'click': function () {

                    if (current == 'cw') {
                        $(this).addClass('current');
                        $('typecho-editor-tab-cw').removeClass('current');
                        current = 'vw';
                        Typecho.currentEditor = current;
                        _toVisual();
                    }
                    
                }
            
            }}))
            .grab(new Element('li', {'text': cw, 'id': 'typecho-editor-tab-cw', 'events': {
            
                'click': function () {

                    if (current == 'vw') {
                        $(this).addClass('current');
                        $('typecho-editor-tab-vw').removeClass('current');
                        current = 'cw';
                        Typecho.currentEditor = current;
                        _toCode();
                    }
                }
            
            }}))
            .setStyle('width', $(id).getSize().x)
            .inject(id, 'before');
            
            var _lb = $(document).getElement('label[for=' + id + ']');
            if (_lb) {
                _lb.setStyles({
                    'float': 'left',
                    'position': 'absolute'
                });
                
                if (Browser.Engine.webkit) {
                    _lb.setStyle('padding-top', 7);
                }
            }
            
            $('typecho-editor-tab-' + current).addClass('current');
        
            ed.onInit.add(function(ed) {
            
                var _pressed = false;
                var _resize = 0, _last = 0, mouseY = 0, editorOffset = 0, _minFinalY = 0;
                _ed = ed;
                
                _transfer();
                
                var _holder = new Element('div', {
                
                    styles: {
                        
                        'border': '1px dashed #C1CD94',
                        
                        'background': '#fff',
                        
                        'display': 'none',
                        
                        'width': $(id + '_tbl').getSize().x - 2,
                        
                        'height': $(id + '_tbl').getSize().y - 2
                        
                    }
                
                }).inject(id + '_parent', 'after');

                var _cross = new Element('span', {
                    'class': 'size-btn',
                    
                    'events' : {
                    
                        'mousedown': function (event) {
                            _show();
                            
                            if (0 == editorOffset) {
                                editorOffset = $(id + '_tbl').getSize().y - _currentY;
                            }
                            
                            if (0 == _minFinalY) {
                                _minFinalY = $(id + '_ifr').getPosition($(id + '_tbl')).y;
                            }
                            
                            if (!_pressed) {
                                _holder.setStyle('height', ('vw' == current ? $(id + '_tbl').getSize().y : $(id).getSize().y) - 2);
                            }
                            
                            _hide();
                        
                            _pressed = true;
                            
                            if ('vw' == current) {
                                $(id + '_tbl').setStyle('display', 'none');
                            } else {
                                $(id).setStyle('display', 'none');
                            }
                            
                            _holder.setStyle('display', 'block');
                            
                            event.stop();
                        }
                    
                    }
                    
                }).inject(_holder, 'after');
                
                $(document).addEvents({
                    
                    'mouseup': function (event) {
                        
                        if (_pressed) {
                            
                            _pressed = false;
                            
                            if ('vw' == current) {
                                $(id + '_tbl').setStyle('display', '');
                            } else {
                                $(id).setStyle('display', '');
                            }
                            
                            _show();
                            
                            var sizeOffset = $(id + '_tbl').getSize().y - $(id + '_ifr').getSize().y;
                            var size = _holder.getSize().y - editorOffset;
                            
                            $(id).setStyle('height', _holder.getSize().y - 8);
                            $(id + '_tbl').setStyle('height', _holder.getSize().y);
                            $(id + '_ifr').setStyle('height',  _holder.getSize().y - sizeOffset);
                            
                            _hide();
                            
                            var _r = new Request({
                                'method': 'post',
                                'url': url
                            }).send('size=' + size + '&do=editorResize');
                            
                            _holder.setStyle('display', 'none');
                            _last = 0;
                            _resize = 0;
                            mouseY = 0;
                        }
                        
                    },
                    
                    'mousemove': function (event) {
                        if (_pressed) {
                            mouseY = event.page.y;
                        }
                    }
                });
                
                setInterval(function () {
                    if (_pressed) {
                    
                        _resize = (0 == _last) ? 0 : mouseY - _last;
                        _last = mouseY;
                        
                        var _finalY = _holder.getSize().y - 2 + _resize;
                        
                        if (_finalY > _minFinalY) {
                            _holder.setStyle('height', _finalY);
                        }
                        
                    }
                }, 10);
                
                if ('cw' == current) {
                    $(id + '_parent').setStyle('display', 'none');
                    $(id).setStyle('display', 'block');
                }
                
                _show();
                $(id).setStyle('height', $(id).getSize().y + (Browser.Engine.trident ? -1 : 3));
                _hide();
            });
        },

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,blockquote,|,link,unlink,image,media,|,forecolor,backcolor,|,morebreak",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        convert_urls : false,
        language : 'typecho'
    });
};  

/** 消息窗口淡出 */
Typecho.message = function (el) {
    var _message = $(document).getElement(el);
    
    setTimeout(function () {
        if (_message) {
            var _messageEffect = new Fx.Morph(_message, {duration: 'short', transition: Fx.Transitions.Sine.easeOut});
            _messageEffect.addEvent('complete', function () {
                this.element.style.display = 'none';
            });
            _messageEffect.start({'margin-top': [30, 0], 'height': [21, 0], 'opacity': [1, 0]});
        }
    }, 5000);
};

/** 在新窗口打开链接 */
Typecho.openLink = function (adminPattern, doPattern) {
    $(document).getElements('a').each(function (item) {
        var _href = item.href;
        if (_href && '#' != _href) {
            $(item).addEvent('click', function (event) {
                var _lang = this.get('lang');
                var _c = _lang ? confirm(_lang) : true;
                
                if (!_c) {
                    event.stop();
                }
            });
        
            /** 如果匹配则继续 */
            if (adminPattern.exec(_href) || doPattern.exec(_href)) {
                return;
            }
            
            $(item).addEvent('click', function () {            
                window.open(this.href);
                return false;
            });
        }
    });
};

/** 页面滚动 */
Typecho.scroll = function (sel, parentSel) {
    var _firstError = $(document).getElement(sel);
    
    //增加滚动效果
    if (_firstError) {
        var _errorFx = new Fx.Scroll(window).toElement(_firstError.getParent(parentSel));
    }
};

Typecho.location = function (url) {
    setTimeout('window.location.href="' + url + '"', 0);
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

/** 文本编辑器插入文字 */
Typecho.textareaHasPrepare = false;

Typecho.textareaAdd = function (match, flg1, flg2) {
    var _el = $(document).getElement(match);
    var _scrollTop, _start, _end, _range;
    
    _scrollTop = _el.scrollTop;
    if (typeof(_el.selectionStart) == "number") {
        _el.focus();
        _start = _el.selectionStart;
        _end = _el.selectionEnd;
    }
    
    else if(document.selection) {
        _el.focus();
        _range = document.selection.createRange();
    }

    if (typeof(_el.selectionStart) == "number") {
    
        var pre = _el.value.substr(0, _start);
        var post = _el.value.substr(_end);
        var center = _el.value.substr(_start, _end - _start);
        _el.value = pre + flg1 + center + flg2 + post;
        
        _el.setSelectionRange(_start + flg1.length, _start + flg1.length);
    } else if (document.selection) {
        if (_range.text.length > 0) {
            _range.text = flg1 + _range.text + flg2;
        } else {
            _range.text = flg1 + flg2;
        }
    }

    setTimeout(function () {_el.scrollTop = _scrollTop}, 0);
    _el.focus();

    return true;
}

/** 高亮元素 */
Typecho.highlight = function (theId) {
    if (theId) {
        var el = $(theId);
        if (el) {
            el.set('tween', {duration: 1500});
            
            var _bg = el.getStyle('background-color');
            if (!_bg || 'transparent' == _bg) {
                _bg = '#F7FBE9';
            }

            el.tween('background-color', '#AACB36', _bg);
        }
    }
};

/** 提交按钮自动失效,防止重复提交 */
Typecho.autoDisableSubmit = function () {
    $(document).getElements('input[type=submit]').removeProperty('disabled');
    $(document).getElements('button[type=submit]').removeProperty('disabled');
    
    var _disable = function (event) {
        event.stopPropagation();
        $(this).setProperty('disabled', true);
        $(this).getParent('form').submit();
        return false;
    };

    $(document).getElements('input[type=submit]').addEvent('click', _disable);
    $(document).getElements('button[type=submit]').addEvent('click', _disable);
};

/** 扩展mootools */
Element.implement({

	getSelectedRange: function() {
		if (!Browser.Engine.trident) return {start: this.selectionStart, end: this.selectionEnd};
		var pos = {start: 0, end: 0};
		var range = this.getDocument().selection.createRange();
		if (!range || range.parentElement() != this) return pos;
		var dup = range.duplicate();
		if (this.type == 'text') {
			pos.start = 0 - dup.moveStart('character', -100000);
			pos.end = pos.start + range.text.length;
		} else {
			var value = this.value;
			var offset = value.length - value.match(/[\n\r]*$/)[0].length;
			dup.moveToElementText(this);
			dup.setEndPoint('StartToEnd', range);
			pos.end = offset - dup.text.length;
			dup.setEndPoint('StartToStart', range);
			pos.start = offset - dup.text.length;
		}
		return pos;
	},

	selectRange: function(start, end) {
		if (Browser.Engine.trident) {
			var diff = this.value.substr(start, end - start).replace(/\r/g, '').length;
			start = this.value.substr(0, start).replace(/\r/g, '').length;
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', start + diff);
			range.moveStart('character', start);
			range.select();
		} else {
			this.focus();
			this.setSelectionRange(start, end);
		}
		return this;
	}

});

/** 自动完成 */
Typecho.autoComplete = function (match, token) {
    var _sp = ',', _index, _cur = -1, _hoverList = false,
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
                        var _start = _el.getSelectedRange().start,
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
