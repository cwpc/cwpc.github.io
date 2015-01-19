/**
 * $Id: otx-editor-plugin.js
 *
 * @author JP Madison
 */

(function() {
	tinymce.PluginManager.requireLangPack('otxlink');
	tinymce.create('tinymce.plugins.otxlink', {
		init : function(ed, url) {
			this.editor = ed;
			// Register commands
			ed.addCommand('otxMceAddLink', function() {
				var se = ed.selection;
				// No selection and not in link
				// if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
				//	return;
				var content = ed.selection.getContent();
				var re=/(<\/?p)(?:\s[^>]*)?(>)|<[^>]*>/gi;
				content = content.replace(re,'');				
				ed.windowManager.open({
					file : url + '/../linking/otx-link-choices.php?validate=1&tri='+content + '&where=both&category=-1&type=',
					width : 600 + parseInt(ed.getLang('otxlink.delta_width', 0)),
					height : 500 + parseInt(ed.getLang('otxlink.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('otx_link', {
				title : 'otxlink.makeLink',
				image : url + '/post_link.png',
				cmd : 'otxMceAddLink'
			});
			ed.onNodeChange.add(function(ed, cm, n, co) {
			//	cm.setDisabled('post_link', co && n.nodeName != 'A');
				cm.setActive('otx_link', n.nodeName == 'A' && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'otxlink',
				author : 'JP Madison',
				authorurl : 'http://acrosswalls.org/authors/',
				infourl : '',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('otxlink', tinymce.plugins.otxlink);
})();