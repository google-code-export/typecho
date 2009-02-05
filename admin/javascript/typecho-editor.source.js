/** 编辑器组件 */
var TypechoEditor = function (textarea) {
    this._init(textarea);
};

TypechoEditor.prototype = {
    
    _iframe: null,
    
    _body: null,
    
    _txt: null,
    
    _init: function (textarea) {
        this._txt = $(document).getElement(textarea);
        if (this._txt) {
            var _size = this._txt.getSize(),
            _txt = this._txt,
            t = this;
        
            t._iframe = new IFrame({
            
                designMode: 'On',
                
                frameBorder: 0,
                
                frameSpacing: 0,
                
                border: 0,
                
                marginWidth: 0,
                
                styles: {
                    
                    width: _size.x,
                    
                    height: _size.y
                    
                },
                
                events: {
                    load: function () {
                        this._body = $(this.contentWindow.document.body);                        
                        var _html = _txt.get('text');
                        _html = ('' == _html) ? '<p></p>' : _html;

                        this._body.set({
                        
                            'contentEditable': true,
                            
                            'html': _html,
                        
                            'styles': {
                                'background-color': '#fff',
                                
                                'font-size': '10pt',
                                
                                'font-family': '"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana',
                                
                                'margin': 0,
                                
                                'padding': 0,
                                
                                'cursor': 'text',
                                
                                'width': '100%',
                                
                                'height': '100%'
                            },
                            
                            'events': {
                                
                                'mouseup': function () {
                                    alert($(t.getNode()).get('tag'));
                                },
                                
                                'keyup': function () {
                                    alert($(t.getNode()).get('tag'));
                                }
                            }
                        });
                    }
                }
            });

            this._iframe
            .inject(this._txt, 'before')
            .focus();
            
            $(this._txt).setStyle('display', 'none');
        }
    },
    
    getSel : function() {
        var w = this._iframe.contentWindow;
        return w.getSelection ? w.getSelection() : w.document.selection;
    },

    getRng : function() {
        var t = this, s = t.getSel(), r;

        try {
            if (s)
                r = s.rangeCount > 0 ? s.getRangeAt(0) : (s.createRange ? s.createRange() : t.contentWindow.document.createRange());
        } catch (ex) {
            // IE throws unspecified error here if TinyMCE is placed in a frame/iframe
        }

        // No range found then create an empty one
        // This can occur when the editor is placed in a hidden container element on Gecko
        // Or on IE when there was an exception
        if (!r)
            r = Browser.Engine.trident ? t.contentWindow.document.body.createTextRange() : t.contentWindow.document.createRange();

        return r;
    },
    
    getNode : function() {
        var t = this, r = t.getRng(), s = t.getSel(), e;

        if (!Browser.Engine.trident) {
            // Range maybe lost after the editor is made visible again
            if (!r)
                return t._body;

            e = r.commonAncestorContainer;

            // Handle selection a image or other control like element such as anchors
            if (!r.collapsed) {
                // If the anchor node is a element instead of a text node then return this element
                if (Browser.Engine.webkit && s.anchorNode && s.anchorNode.nodeType == 1) 
                    return s.anchorNode.childNodes[s.anchorOffset]; 

                if (r.startContainer == r.endContainer) {
                    if (r.startOffset - r.endOffset < 2) {
                        if (r.startContainer.hasChildNodes())
                            e = r.startContainer.childNodes[r.startOffset];
                    }
                }
            }
            
            if (1 == e.nodeType) {
                return e;
            } else if (e.parentNode && 1 == e.parentNode.nodeType) {
                return e.parentNode;
            } else {
                return $(e.parentNode).getParent('*[nodeType=1]');
            }
        }

        return r.item ? r.item(0) : r.parentElement();
    }
};
