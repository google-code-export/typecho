var typechoGuid = function (el, config) {
    var _dl  = $(el);
    var _dt  = _dl.getElements('dt');
    var _dd  = _dl.getElements('dd');
    var _cur = null, _timer = null;

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
                _d.setStyle('left', el.getPosition().x - config.offset);
                if (_d.getStyle('display') != 'none') {
                    _d.setStyle('display', 'none');
                } else {
                    _d.setStyle('display', 'block');
                    _d.getElement('ul li:first-child').setStyle('border-top', 'none');
                    _d.getElement('ul li:last-child').setStyle('border-bottom', 'none');
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

/** 消息窗口淡出 */
var typechoMessage = function () {
    var message = $(document).getElement('.popup');
    if (message) {
        var messageEffect = new Fx.Morph(message, {duration: 'short', transition: Fx.Transitions.Sine.easeOut});
        messageEffect.addEvent('complete', function () {
            this.element.style.display = 'none';
        });
        messageEffect.start({'margin-top': [30, 0], 'height': [21, 0], 'opacity': [1, 0]});
    }
};

/** 在新窗口打开链接 */
var typechoOpenLink = function (adminPattern, doPattern) {
    $(document).getElements('a').each(function (item) {
        href = item.href;
        if (href && '#' != href) {
            /** 如果匹配则继续 */
            if (adminPattern.exec(href) || doPattern.exec(href)) {
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
    var firstError = $(document).getElement(sel);
    
    //增加滚动效果
    if (firstError) {
        var errorFx = new Fx.Scroll(window).toElement(firstError.getParent(parentSel));
    }
}

var typechoLocation = function (url) {
    setTimeout('window.location.href="' + url + '"', 0);
}

var typechoToggle = function (sel, btn, showWord, hideWord) {
    var el = $(document).getElement(sel);
    btn.toggleClass('close');
    if ('none' == el.getStyle('display')) {
        $(btn).set('html', showWord);
        el.setStyle('display', 'block');
    } else {
        $(btn).set('html', hideWord);
        el.setStyle('display', 'none');
    }
}

/** 提交表单 */
var typechoSubmit = function (formSel, inputSel, op) {
    var form = $(document).getElement(formSel);
    var input = $(document).getElement(inputSel);
    
    if (form && input) {
        input.set('value', op);
        form.submit();
    }
}

/** 提交按钮自动失效,防止重复提交 */
var typechoAutoDisableSubmit = function () {

    $(document).getElements('input[type=submit]').addEvent('click', function (event) {
            event.stopPropagation();
            $(this).setProperty('disabled', true);
            $(this).getParent('form').submit();
    });
    
    $(document).getElements('button[type=submit]').addEvent('click', function (event) {
            event.stopPropagation();
            $(this).setProperty('disabled', true);
            $(this).getParent('form').submit();
    });
}

var _typechoCheckItem = function (item) {
    if (item.hasClass('even')) {
        item.addClass('checked-even');
    } else {
        item.addClass('checked');
    }
}

var _typechoUncheckItem = function (item) {
    if (item.hasClass('even')) {
        item.removeClass('checked-even');
    } else {
        item.removeClass('checked');
    }
}

/** 操作按钮 */
var typechoOperate = function (selector, op) {
    /** 获取元素 */
    var el = $(document).getElement(selector);
    
    if (el && 'table' == el.get('tag')) {
        /** 如果是标准表格 */
        var elements = el.getElements('tbody tr td input[type=checkbox]');
        switch (op) {
            case 'selectAll':
                elements.each(function(item) {
                    _typechoCheckItem($(item).getParent('tr'));
                    $(item).setProperty('checked', 'true');
                });
                break;
            case 'selectNone':
                elements.each(function(item) {
                    _typechoUncheckItem($(item).getParent('tr'));
                    $(item).removeProperty('checked');
                });
                break;
            default:
                break;
        }
    } else if (el && 'ul' == el.get('tag')) {
        /** 如果是列表形式 */
        var elements = el.getElements('li input[type=checkbox]');
        switch (op) {
            case 'selectAll':
                elements.each(function(item) {
                    _typechoCheckItem($(item).getParent('li'));
                    $(item).setProperty('checked', 'true');
                });
                break;
            case 'selectNone':
                elements.each(function(item) {
                    _typechoUncheckItem($(item).getParent('li'));
                    $(item).removeProperty('checked');
                });
                break;
            default:
                break;
        }
    }
};

var typechoTableListener = function (selector) {
    /** 获取元素 */
    var el = $(document).getElement(selector);
    
    if (el && 'table' == el.get('tag')) {
        /** 如果是标准表格 */
        
        /** 监听click事件 */
        el.getElements('tbody tr td input[type=checkbox]').each(function(item) {
            $(item).addEvent('click', function(event) {
                event.stopPropagation();
                if ($(this).getProperty('checked')) {
                    _typechoCheckItem($(this).getParent('tr'));
                } else {
                    _typechoUncheckItem($(this).getParent('tr'));
                }
            });
        });
        
        /** 监听鼠标事件 */
        el.getElements('tbody tr').each(function(item) {
            $(item).addEvents({'mouseover': function() {
                $(this).addClass('hover');
            },
            'mouseleave': function() {
                $(this).removeClass('hover');
            },
            'click': function() {
                var checkBox = $(this).getElement('input[type=checkbox]');
                if (checkBox) {
                    checkBox.click();
                }
            }
            });
        });
    } else if (el && 'ul' == el.get('tag')) {
        /** 如果是列表形式 */
        el.getElements('li input[type=checkbox]').each(function(item) {
            $(item).addEvent('click', function(event) {
                event.stopPropagation();
                if ($(this).getProperty('checked')) {
                    _typechoCheckItem($(this).getParent('li'));
                } else {
                    _typechoUncheckItem($(this).getParent('li'));
                }
            });
        });
        
        /** 监听鼠标事件 */
        el.getElements('li').each(function(item) {
            $(item).addEvents({'mouseover': function() {
                $(this).addClass('hover');
            },
            'mouseleave': function() {
                $(this).removeClass('hover');
            },
            'click': function() {
                var checkBox = $(this).getElement('input[type=checkbox]');
                if (checkBox) {
                    checkBox.click();
                }
            }
            });
        });
    }
};
