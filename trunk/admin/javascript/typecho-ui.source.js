var typechoGuid = function (el, config) {
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
    }

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

var typechoTable = {
    
    table: null,        //当前表格
    
    draggable: false,    //是否可拖拽
    
    draggedEl: null,    //当前拖拽的元素
    
    draggedFired: false,    //是否触发

    init: function (match) {
        /** 初始化表格风格 */
        $(document).getElements(match).each(function (item) {
            typechoTable.table = item;
            typechoTable.draggable = item.hasClass('draggable');
            typechoTable.bindButtons();
            typechoTable.reset();
        });
    },
    
    reset: function () {
        var _el = typechoTable.table;
        typechoTable.draggedEl = null;
        
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
                item._parent = item.getParent(typechoTable.table._childTag);
               
                /** 监听click事件 */
                item.addEvent('click', typechoTable.checkBoxClick);
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
            
            typechoTable.bindEvents(item);
        });
    },
    
    checkBoxClick: function (event) {
        var _el = $(this);
        if (_el.getProperty('checked')) {
            _el.setProperty('checked', false);
            _el._parent.removeClass(_el._parent.hasClass('even') ? 'checked-even' : 'checked');
            typechoTable.unchecked(this, _el._parent);
        } else {
            _el.setProperty('checked', true);
            _el._parent.addClass(_el._parent.hasClass('even') ? 'checked-even' : 'checked');
            typechoTable.checked(this, _el._parent);
        }
    },
    
    itemMouseOver: function (event) {
        if(!typechoTable.draggedEl || typechoTable.draggedEl == this) {
            $(this).addClass('hover');
            
            //fix ie
            if (Browser.Engine.trident) {
                $(this).getElements('.hidden-by-mouse').setStyle('display', 'inline');
            }
        }
    },
    
    itemMouseLeave: function (event) {
        if(!typechoTable.draggedEl || typechoTable.draggedEl == this) {
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
        if (!typechoTable.draggedEl) {
            typechoTable.draggedEl = this;
            typechoTable.draggedFired = false;
            return false;
        }
    },
    
    itemMouseMove: function (event) {
        if (typechoTable.draggedEl) {
        
            if (!typechoTable.draggedFired) {
                typechoTable.dragStart(this);
                $(this).setStyle('cursor', 'move');
                typechoTable.draggedFired = true;
            }
            
            if (typechoTable.draggedEl != this) {
                /** 从下面进来的 */
                if ($(this).getCoordinates(typechoTable.draggedEl).top < 0) {
                    $(this).inject(typechoTable.draggedEl, 'after');
                } else {
                    $(this).inject(typechoTable.draggedEl, 'before');
                }
                
                if ($(this).hasClass('even')) {
                    if (!$(typechoTable.draggedEl).hasClass('even')) {
                        $(this).removeClass('even');
                        $(typechoTable.draggedEl).addClass('even');
                    }
                    
                    if ($(this).hasClass('checked-even') && 
                    !$(typechoTable.draggedEl).hasClass('checked-even')) {
                        $(this).removeClass('checked-even');
                        $(typechoTable.draggedEl).addClass('checked-even');
                    }
                } else {
                    if ($(typechoTable.draggedEl).hasClass('even')) {
                        $(this).addClass('even');
                        $(typechoTable.draggedEl).removeClass('even');
                    }
                    
                    if ($(this).hasClass('checked') && 
                    $(typechoTable.draggedEl).hasClass('checked')) {
                        $(this).removeClass('checked');
                        $(typechoTable.draggedEl).addClass('checked');
                    }
                }
                
                return false;
            }
        }
    },
    
    itemMouseUp: function (event) {
        if (typechoTable.draggedEl) {
            var _inputs = typechoTable.table.getElements(typechoTable.table._childTag + ' input[type=checkbox]');
            var result = '';
            
            for (var i = 0; i< _inputs.length; i ++) {
                if (result.length > 0) result += '&';
                result += _inputs[i].name + '=' + _inputs[i].value;
            }
            
            if (typechoTable.draggedFired) {    
                $(this).fireEvent('click');
                $(this).setStyle('cursor', '');
                typechoTable.dragStop(this, result);
                typechoTable.draggedFired = false;
                typechoTable.reset();
            }
            
            typechoTable.draggedEl = null;
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
            typechoTable.table.getElements(typechoTable.table._childTag + ' input[type=checkbox]')
            .each(function (item) {
                if (!item.getProperty('checked')) {
                    item.fireEvent('click');
                }
            });
        });
        
        /** 不选按钮 */
        $(document).getElements('.typecho-table-select-none')
        .addEvent('click', function () {
            typechoTable.table.getElements(typechoTable.table._childTag + ' input[type=checkbox]')
            .each(function (item) {
                if (item.getProperty('checked')) {
                    item.fireEvent('click');
                }
            });
        });
        
        /** 提交按钮 */
        $(document).getElements('.typecho-table-select-submit')
        .addEvent('click', function () {
            var _f = typechoTable.table.getParent('form');
            _f.getElement('input[name=do]').set('value', $(this).getProperty('rel'));
            _f.submit();
        });
    },
    
    bindEvents: function (item) {
        item.removeEvents();

        item.addEvents({
            'mouseover': typechoTable.itemMouseOver,
            'mouseleave': typechoTable.itemMouseLeave,
            'click': typechoTable.itemClick
        });

        if (typechoTable.draggable && 
        typechoTable.table.getElements(typechoTable.table._childTag + ' input[type=checkbox]').length > 0) {
            item.addEvents({
                'mousedown': typechoTable.itemMouseDown,
                'mousemove': typechoTable.itemMouseMove,
                'mouseup': typechoTable.itemMouseUp
            });
        }
    }
};

var typechoEditor = function () {
    
}

/** 消息窗口淡出 */
var typechoMessage = function () {
    var _message = $(document).getElement('.popup');
    if (_message) {
        var _messageEffect = new Fx.Morph(_message, {duration: 'short', transition: Fx.Transitions.Sine.easeOut});
        _messageEffect.addEvent('complete', function () {
            this.element.style.display = 'none';
        });
        _messageEffect.start({'margin-top': [30, 0], 'height': [21, 0], 'opacity': [1, 0]});
    }
};

/** 在新窗口打开链接 */
var typechoOpenLink = function (adminPattern, doPattern) {
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
var typechoScroll = function (sel, parentSel) {
    var _firstError = $(document).getElement(sel);
    
    //增加滚动效果
    if (_firstError) {
        var _errorFx = new Fx.Scroll(window).toElement(_firstError.getParent(parentSel));
    }
}

var typechoLocation = function (url) {
    setTimeout('window.location.href="' + url + '"', 0);
}

var typechoToggle = function (sel, btn, showWord, hideWord) {
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
var typechoHighlight = function (theId) {
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
var typechoAutoDisableSubmit = function () {
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
