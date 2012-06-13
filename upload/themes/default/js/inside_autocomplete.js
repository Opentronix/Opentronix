var d = document;
var w = window;

if( d.addEventListener ) {
	d.addEventListener("load", input_set_autocomplete, false);
	w.addEventListener("load", input_set_autocomplete, false);
}
else if( d.attachEvent ) {
	d.attachEvent("onload", input_set_autocomplete);
	w.attachEvent("onload", input_set_autocomplete);
}

var autocomplete_allset	= false;
var autocomplete_open	= false;
var autocomplete_data	= {};
function input_set_autocomplete()
{
	if( autocomplete_allset ) {
		return;
	}
	autocomplete_allset	= true;
	var all	= d.getElementsByTagName("INPUT");
	for(var i=0; i<all.length; i++) {
		input_set_autocomplete_to(all[i]);
	}
	var fsdfgh	= function() {
		setTimeout(input_hide_autocompletes, 100);
	}
	var all	= d.getElementsByTagName("TEXTAREA");
	for(var i=0; i<all.length; i++) {
		input_set_autocomplete_toarea(all[i]);
	}
	if( d.addEventListener ) {
		d.addEventListener("mouseup", fsdfgh, false);
	}
	else if( d.attachEvent ) {
		d.attachEvent("onmouseup", fsdfgh );
	}
}
function input_set_autocomplete_to(obj)
{
	if( obj.type != "text" ) { return; }
	if( obj.getAttribute("rel") != "autocomplete" ) { return; }
	obj.setAttribute("autocomplete", "off");
	obj.setAttribute("uniqindex", Math.round(Math.random()*99999));
	var f_onfocus	= function() {
		input_hide_autocompletes();
	};
	var f_onkeydown	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e || (!e.keyCode && !e.charCode) ) { return; }
		var code	= e.charCode ? e.charCode : e.keyCode;
		if( code == 38 || code == 40 ) {
			input_scroll_autocomplete(code==38?"UP":"DOWN");
			return false;
		}
	};
	var f_onkeyup	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e || (!e.keyCode && !e.charCode) ) { return; }
		var code	= e.charCode ? e.charCode : e.keyCode;
		if( (code>=48 && code<=57) || (code>=65 && code<=90) || (code>=97 && code<=122) || (code>=1040 && code<=1103) || (code==34 || code==39 || code==45) ) {
			obj.setAttribute("origvalue", obj.value);
			input_show_autocomplete(obj);
		}
	};
	var f_onkeypress	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e || (!e.keyCode && !e.charCode) ) { return; }
		var code	= e.charCode ? e.charCode : e.keyCode;
		if( code == 13 && autocomplete_open && autocomplete_data.id==obj.getAttribute("uniqindex") ) {
			input_scroll_autocomplete_to(autocomplete_data.focusIndex);
			if( autocomplete_data.data[autocomplete_data.focusIndex] ) {
				var sdf	= autocomplete_data.data[autocomplete_data.focusIndex];
				sdf[2](sdf[0], sdf[1]);
				if( e.preventDefault ) {
					e.preventDefault();
				}
				else {
					e.returnValue = false;
				}
			}
			input_hide_autocompletes();
			return false;
		}
	};
	var f_onblur	= function() {
		setTimeout(input_hide_autocompletes, 1000);
	};
	if( obj.addEventListener ) {
		obj.addEventListener("focus", f_onfocus, false);
		obj.addEventListener("keydown", f_onkeydown, false);
		obj.addEventListener("keypress", f_onkeypress, false);
		obj.addEventListener("keyup", f_onkeyup, false);
		obj.addEventListener("blur", f_onblur, false);
	}
	else if( obj.attachEvent ) {
		obj.attachEvent("onfocus", f_onfocus);
		obj.attachEvent("onkeydown", f_onkeydown);
		obj.attachEvent("onkeypress", f_onkeypress);
		obj.attachEvent("onkeyup", f_onkeyup);
		obj.attachEvent("onblur", f_onblur);
	}
}
function input_show_autocomplete(obj)
{
	input_hide_autocompletes();
	if( autocomplete_open && autocomplete_data.id==obj.getAttribute("uniqindex") && autocomplete_data.word==obj.getAttribute("origvalue") ) {
		return;
	}
	var callbck_after	= function() {};
	if( obj.getAttribute("autocompleteafter") ) {
		callbck_after	= function() { try{ eval(obj.getAttribute("autocompleteafter")) } catch(e){}; };
	}
	var f_only_fill	= function(word, url) {
		input_hide_autocompletes();
		obj.value	= word;
		callbck_after();
	};
	var f_fill_submit	= function(word, url) {
		input_hide_autocompletes();
		obj.value	= word;
		obj.form.submit();
		callbck_after();
	};
	var f_fill_redirect = function(word, url) {
		input_hide_autocompletes();
		obj.value	= word;
		w.location.href	= url;
		callbck_after();
	};
	var f_fill_callback	= function(word, url) {
		input_hide_autocompletes();
		obj.value	= word;
		var c	= obj.getAttribute("autocompletecallback");
		if( c ) {
			try{ eval(c) } catch(e) { }
		}
		callbck_after();
	};
	var datatype	= false;
	var jsaction	= f_only_fill;
	if( obj.name=="username" || obj.name=="groupname" ) {
		datatype	= obj.name;
	}
	else if( obj.name=="lookfor" ) {
		if( obj.form.lookin.value == "users" ) {
			datatype	= "username";
			jsaction	= f_fill_redirect;
		}
		else if( obj.form.lookin.value == "groups" ) {
			datatype	= "groupname";
			jsaction	= f_fill_redirect;
		}
	}
	else if( obj.name=="privusr_inp" ) {
		datatype	= "username";
		jsaction	= f_fill_callback;
	}
	else if( obj.name=="puser" && obj.form.name=="psrch" ) {
		datatype	= "username";
		jsaction	= f_only_fill;
	}
	else if( obj.name=="pgroup" && obj.form.name=="psrch" ) {
		datatype	= "groupname";
		jsaction	= f_only_fill;
	}
	else if( obj.name=="deluser" && obj.form.name=="deluser" ) {
		datatype	= "username";
		jsaction	= f_only_fill;
	}
	else if( obj.name=="editusername" && obj.form.name=="edituser" ) {
		datatype	= "username";
		jsaction	= f_fill_submit;
	}
	if( ! datatype ) {
		return;
	}
	if(obj.value.length < 2) { return; }
	var req = ajax_init(true);
	if( ! req ) { return; }
	autocomplete_open	= obj;
	autocomplete_data	= ({ id: -1, word: "", loaded: false, data: [], focusIndex: false });
	autocomplete_data.id	= obj.getAttribute("uniqindex");
	autocomplete_data.word	= obj.value;
	obj.setAttribute("origvalue",	obj.value);
	var dv	= d.createElement("DIV");
	dv.id	= "inp_dropbox_"+autocomplete_data.id;
	dv.className	= "recdrop";
	dv.style.display	= "none";
	dv.style.position	= "absolute";
	var coords	= obj_find_coords(obj);
	var tmph	= parseInt(obj.clientHeight, 10);
	if( ! tmph ) { tmph = parseInt(obj.style.height, 10)+2; }
	if( ! tmph ) { tmph = 23; }
	coords[1]	+= tmph;
	var tmpof	= obj.getAttribute("autocompleteoffset");
	if( tmpof ) {
		tmpof = tmpof.split(",");
		tmpof[0]	= parseInt(tmpof[0],10);
		tmpof[1]	= parseInt(tmpof[1],10);
		if( !isNaN(tmpof[0]) ) { coords[0] += tmpof[0]; }
		if( !isNaN(tmpof[1]) ) { coords[1] += tmpof[1]; }
	}
	dv.style.left	= coords[0] + "px";
	dv.style.top	= coords[1] + "px";
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( obj.value != autocomplete_data.word ) { return; }
		if( obj.getAttribute("uniqindex") != autocomplete_data.id ) { return; }
		if( ! req.responseXML ) { return; }
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] ) { return; }
		data	= data[0].getElementsByTagName("row");
		var i, j, word, url, html, dv2;
		for(i=0; i<data.length; i++) {
			word	= data[i].getAttribute("word");
			url	= data[i].getAttribute("url");
			html	= data[i].getAttribute("html");
			if( !word || !html ) { continue; }
			j	= autocomplete_data.data.length;
			autocomplete_data.data[j]	= [word, url, jsaction];
			dv2	= d.createElement("DIV");
			dv2.id	= "inp_dropbox_"+autocomplete_data.id+"_row_"+j;
			dv2.innerHTML	= html;
			dv2.setAttribute("autorowindex", j);
			dv2.onmouseover	= function() {
				input_scroll_autocomplete_to(this.getAttribute("autorowindex"), true);
			}
			dv2.onclick	= function() {
				input_scroll_autocomplete_to(this.getAttribute("autorowindex"));
				if( autocomplete_data.data[autocomplete_data.focusIndex] ) {
					var sdf	= autocomplete_data.data[autocomplete_data.focusIndex];
					sdf[2](sdf[0], sdf[1]);
				}
				input_hide_autocompletes();
			};
			dv.appendChild(dv2);
		}
		if( j == 0 && autocomplete_data.data[j][1] == obj.value ) {
			return;
		}
		autocomplete_data.loaded	= true;
		if( autocomplete_data.data.length > 0 ) {
			dv.style.display	= "";
		}
	}
	d.body.appendChild(dv);
	req.open("POST", siteurl+"ajax/autocomplete/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("datatype="+encodeURIComponent(datatype)+"&word="+encodeURIComponent(autocomplete_data.word));
}
function input_hide_autocompletes()
{
	if( ! autocomplete_open ) { return; }
	var box	= d.getElementById("inp_dropbox_"+autocomplete_open.getAttribute("uniqindex"));
	if( box ) {
		box.style.display	= "none";
		box.parentNode.removeChild(box);
	}
	autocomplete_open	= false;
	autocomplete_data	= {};
}
function input_scroll_autocomplete(dir)
{
	if( ! autocomplete_open ) { return; }
	if( autocomplete_data.focusIndex === false ) {
		nw	= dir=="DOWN" ? 0 : (autocomplete_data.data.length-1);
	}
	else {
		nw	= autocomplete_data.focusIndex + (dir=="DOWN" ? 1 : -1);
		if( nw == autocomplete_data.data.length ) { nw = 0; } else 
		if( nw == -1 ) { nw = autocomplete_data.data.length-1; }
	}
	input_scroll_autocomplete_to(nw);
}
function input_scroll_autocomplete_to(pos, dontshow)
{
	if( ! autocomplete_open ) { return; }
	if( autocomplete_data.focusIndex !== false ) {
		var old	= d.getElementById("inp_dropbox_"+autocomplete_data.id+"_row_"+autocomplete_data.focusIndex);
		if( old ) { old.className = ""; }
	}
	if( pos!==false && autocomplete_data.data[pos] ) {
		pos	= parseInt(pos, 10);
		if( ! dontshow ) {
			autocomplete_open.value	= autocomplete_data.data[pos][0];
		}
		autocomplete_data.focusIndex	= pos;
		var nw	= d.getElementById("inp_dropbox_"+autocomplete_data.id+"_row_"+pos);
		if( nw ) { nw.className = "selected"; }
	}
}
function input_set_autocomplete_toarea(obj)
{
	if(obj.type != "textarea" ) { return; }
	if( obj.getAttribute("rel") != "autocomplete" ) { return; }
	obj.setAttribute("autocomplete", "off");
	obj.setAttribute("uniqindex", Math.round(Math.random()*99999));
	var f_onfocus	= function() {
		input_hide_autocompletes();
	};
	var f_onkeydown	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e || (!e.keyCode && !e.charCode) ) { return; }
		var code	= e.charCode ? e.charCode : e.keyCode;
		if( code == 38 || code == 40 ) {
			input_scroll_autocomplete_area(code==38?"UP":"DOWN");
			return false;
		}
	};
	var f_onkeyup	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e || (!e.keyCode && !e.charCode) ) { return; }
		var code	= e.charCode ? e.charCode : e.keyCode;
		var str	=	obj.value;
		var	len	=	str.length;
		var	occur	=	str.lastIndexOf('@');
		if(occur>=0)
		{
			if( (code>=48 && code<=57) || (code>=65 && code<=90) || (code>=97 && code<=122) || (code>=1040 && code<=1103) || (code==34 || code==39 || code==45) ) {
				var	val	=	str.substring(occur+1,len);
				obj.setAttribute("origvalue", val);
				input_show_autocomplete_textarea(obj);
			}
		}
	};
	var f_onkeypress	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e || (!e.keyCode && !e.charCode) ) { return; }
		var code	= e.charCode ? e.charCode : e.keyCode;
		if( code == 13 && autocomplete_open && autocomplete_data.id==obj.getAttribute("uniqindex") ) {
			input_scroll_autocomplete_toarea(autocomplete_data.focusIndex);
			if( autocomplete_data.data[autocomplete_data.focusIndex] ) {
				var sdf	= autocomplete_data.data[autocomplete_data.focusIndex];
				sdf[2](sdf[0], sdf[1]);
				if( e.preventDefault ) {
					e.preventDefault();
				}
				else {
					e.returnValue = false;
				}
			}
			input_hide_autocompletes();
			return false;
		}
	};
	var f_onblur	= function() {
		setTimeout(input_hide_autocompletes, 1000);
	};
	if( obj.addEventListener ) {
		obj.addEventListener("focus", f_onfocus, false);
		obj.addEventListener("keydown", f_onkeydown, false);
		obj.addEventListener("keypress", f_onkeypress, false);
		obj.addEventListener("keyup", f_onkeyup, false);
		obj.addEventListener("blur", f_onblur, false);
	}
	else if( obj.attachEvent ) {
		obj.attachEvent("onfocus", f_onfocus);
		obj.attachEvent("onkeydown", f_onkeydown);
		obj.attachEvent("onkeypress", f_onkeypress);
		obj.attachEvent("onkeyup", f_onkeyup);
		obj.attachEvent("onblur", f_onblur);
	}
}
function input_show_autocomplete_textarea(obj)
{
	input_hide_autocompletes();
	var val	=	obj.getAttribute("origvalue")
	if( autocomplete_open && autocomplete_data.id==obj.getAttribute("uniqindex") && autocomplete_data.word==obj.getAttribute("origvalue") ) {
		return;
	}
	var callbck_after	= function() {};
	if( obj.getAttribute("autocompleteafter") ) {
		callbck_after	= function() { try{ eval(obj.getAttribute("autocompleteafter")) } catch(e){}; };
	}
	
	var f_only_filltextarea	= function(word, url) {
		input_hide_autocompletes();
		var str	=	obj.value;
		var	len	=	str.length;
		var	occur	=	str.lastIndexOf('@');
		var valuebefore	=	str.substring(0,occur+1);
		obj.value	= valuebefore+word;
		callbck_after();
	};
	
	var datatype	= false;
	var jsaction	= f_only_filltextarea;
	if( obj.name=="message") {
		datatype	= "username";
		jsaction	= f_only_filltextarea;
	}
	if( obj.name=="comment") {
		datatype	= "username";
		jsaction	= f_only_filltextarea;
	}
	if( ! datatype ) {
		return;
	}
	if(val.length < 2) { return; }
	var req = ajax_init(true);
	if( ! req ) { return; }
	autocomplete_open	= obj;
	autocomplete_data	= ({ id: -1, word: "", loaded: false, data: [], focusIndex: false });
	autocomplete_data.id	= obj.getAttribute("uniqindex");
	autocomplete_data.word	= val;
	obj.setAttribute("origvalue",	val);
	var dv	= d.createElement("DIV");
	dv.id	= "inp_dropbox_"+autocomplete_data.id;
	dv.className	= "recdrop";
	dv.style.display	= "none";
	dv.style.position	= "absolute";
	var coords	= obj_find_coords(obj);
	var tmph	= parseInt(obj.clientHeight, 10);
	if( ! tmph ) { tmph = parseInt(obj.style.height, 10)+2; }
	if( ! tmph ) { tmph = 23; }
	coords[1]	+= tmph;
	var tmpof	= obj.getAttribute("autocompleteoffset");
	if( tmpof ) {
		tmpof = tmpof.split(",");
		tmpof[0]	= parseInt(tmpof[0],10);
		tmpof[1]	= parseInt(tmpof[1],10);
		if( !isNaN(tmpof[0]) ) { coords[0] += tmpof[0]; }
		if( !isNaN(tmpof[1]) ) { coords[1] += tmpof[1]; }
	}
	dv.style.left	= coords[0] + "px";
	dv.style.top	= coords[1] + "px";
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( val != autocomplete_data.word ) { return; }
		if( obj.getAttribute("uniqindex") != autocomplete_data.id ) { return; }
		if( ! req.responseXML ) { return; }
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] ) { return; }
		data	= data[0].getElementsByTagName("row");
		var i, j, word, url, html, dv2;
		for(i=0; i<data.length; i++) {
			word	= data[i].getAttribute("word");
			url	= data[i].getAttribute("url");
			html	= data[i].getAttribute("html");
			if( !word || !html ) { continue; }
			j	= autocomplete_data.data.length;
			autocomplete_data.data[j]	= [word, url, jsaction];
			dv2	= d.createElement("DIV");
			dv2.id	= "inp_dropbox_"+autocomplete_data.id+"_row_"+j;
			dv2.innerHTML	= html;
			dv2.setAttribute("autorowindex", j);
			dv2.onmouseover	= function() {
				input_scroll_autocomplete_toarea(this.getAttribute("autorowindex"), true);
			}
			dv2.onclick	= function() {
				input_scroll_autocomplete_toarea(this.getAttribute("autorowindex"));				
				if( autocomplete_data.data[autocomplete_data.focusIndex] ) {
					var sdf	= autocomplete_data.data[autocomplete_data.focusIndex];		
					sdf[2](sdf[0], sdf[1]);
				}
				input_hide_autocompletes();
			};
			dv.appendChild(dv2);
		}
		if( j == 0 && autocomplete_data.data[j][1] == val ) {
			return;
		}
		autocomplete_data.loaded	= true;
		if( autocomplete_data.data.length > 0 ) {
			dv.style.display	= "";
		}
	}
	d.body.appendChild(dv);
	req.open("POST", siteurl+"ajax/autocomplete/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("datatype="+encodeURIComponent(datatype)+"&word="+encodeURIComponent(autocomplete_data.word));
}


function input_scroll_autocomplete_toarea(pos, dontshow)
{
	if( ! autocomplete_open ) { return; }
	if( autocomplete_data.focusIndex !== false ) {
		var old	= d.getElementById("inp_dropbox_"+autocomplete_data.id+"_row_"+autocomplete_data.focusIndex);
		if( old ) { old.className = ""; }			
	}

	if( pos!==false && autocomplete_data.data[pos] ) {		
		pos	= parseInt(pos, 10);
		if( ! dontshow ) {
			var str	=	autocomplete_open.value;
			var	len	=	str.length;
			var	occur	=	str.lastIndexOf('@');
			var valuebefore	=	str.substring(0,occur+1);
			autocomplete_open.value	= valuebefore+autocomplete_data.data[pos][0];
		}
		autocomplete_data.focusIndex	= pos;
		var nw	= d.getElementById("inp_dropbox_"+autocomplete_data.id+"_row_"+pos);
		if( nw ) { nw.className = "selected"; }
	}
}

function input_scroll_autocomplete_area(dir)
{
	if( ! autocomplete_open ) { return; }
	if( autocomplete_data.focusIndex === false ) {
		nw	= dir=="DOWN" ? 0 : (autocomplete_data.data.length-1);
	}
	else {
		nw	= autocomplete_data.focusIndex + (dir=="DOWN" ? 1 : -1);
		if( nw == autocomplete_data.data.length ) { nw = 0; } else 
		if( nw == -1 ) { nw = autocomplete_data.data.length-1; }
	}
	input_scroll_autocomplete_toarea(nw);
}
