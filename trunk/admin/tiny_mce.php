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
        
        var _pressed = false;
        var _resize = 0, _last = 0, mouseY = 0, editorOffset = 0, _minFinalY = 70;
        
        var _holder = new Element('div', {
        
            styles: {
                'border': '1px dashed #C1CD94',
                'background': '#fff',
                'display': 'none',
                'width': 706,
                'height': $('text').getSize().y
            }
        
        }).inject('text', 'after');

        var _cross = new Element('span', {
            'class': 'size-btn',
            
            'events' : {
            
                'mousedown': function (event) {
                    
                    if (!_pressed) {
                        _holder.setStyle('height', $('text').getSize().y - 2);
                    }
                
                    _pressed = true;
                    
                    $('text').setStyle('display', 'none');
                    _holder.setStyle('display', 'block');
                    
                    event.stop();
                }
            
            }
            
        }).inject(_holder, 'after');
        
        $(document).addEvents({
            
            'mouseup': function (event) {
                
                if (_pressed) {
                    
                    _pressed = false;
                    $('text').setStyle('display', '');

                    var size = _holder.getSize().y - 8;
                    $('text').setStyle('height', size);
                    
                    var _r = new Request({
                        'method': 'post',
                        'url': '<?php $options->index('Ajax.do'); ?>'
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
        
    })();
</script>
