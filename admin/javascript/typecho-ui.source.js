/** 初始化全局对象 */
var Typecho = {};

Typecho.guid = function (el, config) {
    var _dl  = $(el);
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
}

/** 页面滚动 */
Typecho.scroll = function (sel, parentSel) {
    var _firstError = $(document).getElement(sel);
    
    //增加滚动效果
    if (_firstError) {
        var _errorFx = new Fx.Scroll(window).toElement(_firstError.getParent(parentSel));
    }
}

Typecho.location = function (url) {
    setTimeout('window.location.href="' + url + '"', 0);
}

Typecho.toggle = function (sel, btn, showWord, hideWord) {
    var el = $(document).getElement(sel);
    $(btn).toggleClass('close');
    if ('none' == el.getStyle('display')) {
        $(btn).set('html', showWord);
        el.setStyle('display', 'block');
    } else {
        $(btn).set('html', hideWord);
        el.setStyle('display', 'none');
    }
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
}

/** 提交按钮自动失效,防止重复提交 */
Typecho.autoDisableSubmit = function () {
    $(document).getElements('input[type=submit]').removeProperty('disabled');
    $(document).getElements('button[type=submit]').removeProperty('disabled');

    $(document).getElements('input[type=submit]').addEvent('click', function (event) {
            event.stopPropagation();
            $(this).setProperty('disabled', true);
            $(this).getParent('form').submit();
            return false;
    });
    
    $(document).getElements('button[type=submit]').addEvent('click', function (event) {
            event.stopPropagation();
            $(this).setProperty('disabled', true);
            $(this).getParent('form').submit();
            return false;
    });
}
