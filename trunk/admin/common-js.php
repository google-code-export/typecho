<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<script type="text/javascript" src="<?php $options->adminUrl('javascript/mootools-1.2.1-core-yc.js'); ?>"></script>
<script type="text/javascript"> 
    (function () {
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

        window.addEvent('domready', function() {
            var handle = new typechoGuid('typecho:guid', {offset: 5, type: 'mouse'});
            handle.reSet();
        });
    })();
</script>
