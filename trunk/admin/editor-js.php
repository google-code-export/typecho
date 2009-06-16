<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript">    
    /** 这两个函数在插件中必须实现 */
    var insertImageToEditor = function (title, url, link) {
        Typecho.textareaAdd('#text', '<a href="' + link + '" title="' + title + '"><img src="' + url + '" alt="' + title + '" /></a>', '');
        new Fx.Scroll(window).toElement($(document).getElement('textarea#text'));
    };
    
    var insertLinkToEditor = function (title, url, link) {
        Typecho.textareaAdd('#text', '<a href="' + url + '" title="' + title + '">' + title + '</a>', '');
        new Fx.Scroll(window).toElement($(document).getElement('textarea#text'));
    };
    
    (function () {
        
        var _pressed = false, _text = $('text'),
        _resize = 0, _last = 0, mouseY = 0, _minFinalY = 70,
        _editorOffset = _text.getSize().y - parseInt(_text.getStyle('height'));

        var _cross = new Element('span', {
            'class': 'size-btn',
            
            'events' : {
            
                'mousedown': function (event) {
                    _pressed = true;
                    event.stop();
                }
            
            }
            
        }).inject(_text, 'after');
        
        $(document).addEvents({
            
            'mouseup': function (event) {
                
                if (_pressed) {
                    
                    _pressed = false;

                    var size = _text.getSize().y - _editorOffset;
                    var _r = new Request({
                        'method': 'post',
                        'url': '<?php $options->index('/action/ajax'); ?>'
                    }).send('size=' + size + '&do=editorResize');
                    
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
                
                var _finalY = _text.getSize().y - _editorOffset + _resize;
                
                if (_finalY > _minFinalY) {
                    _text.setStyle('height', _finalY);
                }
                
            }
        }, 10);
        
    })();
</script>
