function getContentSelection(win){
	var word = '', sel, startPos, endPos;
	if (document.selection) {
		win.edCanvas.focus();
	    sel = document.selection.createRange();
		if (sel.text.length > 0) {
			word = sel.text;
		}
	}
	else if (win.edCanvas.selectionStart || win.edCanvas.selectionStart == '0') {
		startPos = win.edCanvas.selectionStart;
		endPos = win.edCanvas.selectionEnd;
		if (startPos != endPos) {
			word = win.edCanvas.value.substring(startPos, endPos);
		}
	}
	return word;
}

function otxInsertLink(elem,nofollow,shortcode,otxKey) {
	var is_tinyMCE_active = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor  && !tinyMCE.activeEditor.isHidden();	
	if (!is_tinyMCE_active) {	
	var href,title,rel = '',text = '';
	var winder = window.top;
	var word = getContentSelection(winder);
	if (word.length != 0){
		text = word;
	}
	if (shortcode == 'on'){
		var link = '[otx "' + otxKey + '"]';
		if ( 0 != text.length) link = link + text + '[/otx]';
		else link = link + ' ' + '[/otx]';
	}
	else if ( undefined != elem) {
		elem = jQuery(elem);	
		var href = elem.attr('href');
		var title = elem.text();
		if(nofollow == 'on'){
			var rel = 'rel="nofollow"';
		}	
		var link = '<a href="'+href+'" title="'+title+'" '+rel+'>'+text+'</a>';	
	}
    winder.edInsertContent(winder.edCanvas, link);
	winder.tb_remove();
	return false;
	}
	
	else {	
	var ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
	var link = '[otx "' + otxKey + '"]';
	e = dom.getParent(n, 'A');
	if(e == null){
		if(shortcode == 'on'){
			if ( 0 != ed.selection.getContent().length ) link += ed.selection.getContent() + "[/otx]";
			else link += ' ' + '[/otx]';
			tinyMCEPopup.execCommand("mceInsertContent", false, link);
		}
		else if (undefined != elem) {
			elem = $(elem);
			tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
			tinymce.each(ed.dom.select("a"), function(n) {
				if (ed.dom.getAttrib(n, 'href') == '#mce_temp_url#') {
					e = n;
					ed.dom.setAttribs(e, {
						title : elem.text()
					});
					ed.dom.setAttribs(e, {
						href : elem.attr('href')
					});
					if(nofollow == 'on'){
						ed.dom.setAttribs(e, {
							rel : 'nofollow'
						});				
					}
				}
			});
		}
	}
	else if (undefined != elem) {
			elem = $(elem);
			ed.dom.setAttribs(e, {
				title : elem.text()
			});
			ed.dom.setAttribs(e, {
				href : elem.attr('href')
			});	
			if(nofollow == 'on'){
				ed.dom.setAttribs(e, {
					rel : 'nofollow'
				});				
			}
	}
	tinyMCEPopup.close();
	return false;
	}
}

function showFilter(){
	jQuery('.showFilter').css('display','none');
	jQuery('.filter').css('display','block');
}

function hideFilter(){
	jQuery('.showFilter').css('display','block');
	jQuery('.filter').css('display','none');
}