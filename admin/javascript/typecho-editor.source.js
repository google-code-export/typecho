/** 编辑器组件 */
var TypechoEditor = function (textarea) {
    this._init(textarea);
};

var TypechoEditorButtonStrong = {
    
    btn: null,
    
    editor: null,
    
    tested: false,

    init: function (btn) {
        btn.set('html', '<strong>B</strong>')
    },

    test: function (node) {
    
        if ('strong' == node.get('tag') || 'b' == node.get('tag')) {
            return true;
        }
    
        return false;
    },

    click: function (t) {
        var v = t.tested, h = t.editor.getContent().innerHTML;
        
        if (!v) {
            t.editor.setContent('<strong>' + t.editor.getContent().innerHTML + '</strong>');
        } else {
            t.editor.setContent('</strong>' + t.editor.getContent().innerHTML + '<strong>');
        }
    }
}

TypechoEditor.prototype = {
    
    _iframe: null,
    
    _body: null,
    
    _txt: null,
    
    _btn: [TypechoEditorButtonStrong],
    
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
                        t._body = $(this.contentWindow.document.body);                        
                        var _html = _txt.get('text');
                        _html = ('' == _html) ? '<p></p>' : _html;

                        t._body.set({
                        
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
                                    t.test($(t.getNode()));
                                },
                                
                                'keyup': function () {
                                    t.test($(t.getNode()));
                                }
                            }
                        });
                    }
                }
            });

            this._iframe
            .inject(this._txt, 'before')
            .focus();

            this._btn.each(function (item) {
                var _btn = $(document.createElement('button'));
                item.btn = _btn;
                item.editor = t;
                item.init(_btn);
                _btn.set('type', 'button')
                .inject(t._iframe, 'before')
                .addEvent('click', function () { item.click(item); t.test($(t.getNode())); });
            });
            
            $(this._txt).setStyle('display', 'none');
        }
    },
    
    test: function (node) {
        var n = node;
    
        this._btn.each(function (item) {
            item.btn.setStyle('color', '#000');
            item.tested = false;
        
            if (item.test(n)) {
                item.tested = true;
                item.btn.setStyle('color', '#ddd');
            }
        });
    },
    
    
    /** 以下函数来自tinyMCE */
    getContent : function() {
        var t = this, r = t.getRng(), e = document.createElement('body'), se = t.getSel(), wb, wa, n;
        wb = wa = '';
        
        if (r.cloneContents) {
            n = r.cloneContents();

            if (n)
                e.appendChild(n);
        } else if ('undefined' != typeof(r.item) || 'undefined' != typeof(r.htmlText))
            e.innerHTML = r.item ? r.item(0).outerHTML : r.htmlText;
        else
            e.innerHTML = r.toString();

        return e;
    },

    setContent : function(h) {
        var t = this, r = t.getRng(), c, d = t._iframe.contentWindow.document;

        if (r.insertNode) {
            // Make caret marker since insertNode places the caret in the beginning of text after insert
            h += '<span id="__caret">_</span>';

            // Delete and insert new node
            r.deleteContents();
            r.insertNode(t.getRng().createContextualFragment(h));

            // Move to caret marker
            c = t._body.getElement('#__caret');

            // Make sure we wrap it compleatly, Opera fails with a simple select call
            r = d.createRange();
            r.setStartBefore(c);
            r.setEndAfter(c);
            t.setRng(r);

            // Delete the marker, and hopefully the caret gets placed in the right location
            d.execCommand('Delete', false, null);

            // In case it's still there
            c.destroy();
        } else {
            if (r.item) {
                // Delete content and get caret text selection
                d.execCommand('Delete', false, null);
                r = t.getRng();
            }

            r.pasteHTML(h);
        }
    },
    
    getSel : function() {
        var w = this._iframe.contentWindow;
        return w.getSelection ? w.getSelection() : w.document.selection;
    },
    
    setRng : function(r) {
        var s;

        if (!Browser.Engine.trident) {
            s = this.getSel();

            if (s) {
                s.removeAllRanges();
                s.addRange(r);
            }
        } else {
            try {
                r.select();
            } catch (ex) {
                // Needed for some odd IE bug #1843306
            }
        }
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
