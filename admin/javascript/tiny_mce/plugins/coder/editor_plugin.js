/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.CoderPlugin', {
		init : function(ed, url) {
            
			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName === 'CODE')
					ed.selection.select(e);
			});

            
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/<code([^>]*)>([\s\S]*?)<\/code>/ig, function (g, a, b) {
                    b = b.trim().replace(/ /g, "&nbsp;")
                    .replace("<", "&lt;")
                    .replace(">", "&gt;")
                    .replace(/(\r|\n)/g, "<br />");
                    
                    return '<code' + a + '>' + b + '</code>';
                });
			});
            
            /*
			ed.onPostProcess.add(function(ed, o) {
				if (o.get) {
					o.content = o.content.replace(/<textarea([^>]*)>/ig, '<code$1>');
					o.content = o.content.replace(/<\/textarea>/ig, '</code>');
                }
			});
            */
		},

		getInfo : function() {
			return {
				longname : 'Coder',
				author : 'Typecho Team',
				authorurl : 'http://typecho.org',
				infourl : 'http://typecho.org',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('coder', tinymce.plugins.CoderPlugin);
})();
