var d = document;
var w = window;

function post_fave(postid)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	var thislink	= d.getElementById("postlink_fave_"+postid);
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.cursor	= "pointer";
		thislink.style.display	= "none";
		d.getElementById("postlink_unfave_"+postid).style.display	= "block";
	}
	req.open("POST", siteurl+"ajax/favepost/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=on&postid="+encodeURIComponent(postid));
	thislink.style.cursor	= "wait";
}
function post_unfave(postid, confirm_msg, remove_from_list)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( ! confirm(confirm_msg) ) { return; }
	var thislink	= d.getElementById("postlink_unfave_"+postid);
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.cursor	= "pointer";
		thislink.style.display	= "none";
		d.getElementById("postlink_fave_"+postid).style.display	= "block";
		if( remove_from_list ) {
			post_hide_slow(postid, posts_synchronize);
		}
	}
	req.open("POST", siteurl+"ajax/favepost/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=off&postid="+encodeURIComponent(postid));
	thislink.style.cursor	= "wait";
}
function post_delete(postid, confirm_msg, callback_after)
{
	if( postcomments_open_state && !d.getElementById("viewpost") ) {
		var state	= postcomments_open_state[postid];
		if( state == 1 ) {
			d.getElementById("postcomments_"+postid+"_textarea").focus();
			return;
		}
		if( state == 2 ) {
			return;
		}
	}
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( confirm_msg ) {
		if( ! confirm(confirm_msg) ) { return; }
	}
	if( pf_open_state == 2 ) { return; }
	if( pf_open_state == 1 && pf_data.existing_post_id==postid ) {
		postform_close();
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		if( callback_after ) {
			callback_after();
		}
		post_hide_slow(postid, posts_synchronize);
		if(msgbox_close) { msgbox_close(); };
		if(postform_topmsg_close) { postform_topmsg_close(); };
	}
	req.open("POST", siteurl+"ajax/delpost/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("postid="+encodeURIComponent(postid));
	d.getElementById("postlink_del_"+postid).style.cursor	= "wait";
}

function post_hide_slow(postid, callback_after)
{
	var div_id	= "post_"+postid;
	pf_htmlobjects_shown[div_id]	= 1;
	postform_htmlobject_hide(div_id, callback_after);
}

var sync_tmout	= false;
function posts_synchronize()
{
	if( postcomments_open_state ) {
		for(var i in postcomments_open_state) {
			if( postcomments_open_state[i] != 0 ) {
				return false;
			}
		}
	}
	var req = ajax_init(false);
	if( ! req ) { return; }
	var dv	= d.getElementById("posts_html");
	if( ! dv ) { return; }	
	var url	= w.location.href.toString();
	if( ! url ) { return; }
	if( url.substr(0, siteurl.length) == siteurl ) {
		url	= url.substr(siteurl.length);
		url	= siteurl+"from:ajax/"+url+"/r:"+Math.round(Math.random()*1000);
	}
	else {
		url	= url.replace(/^http(s)?\:\/\//, "");
		url	= url.substr(url.indexOf("/"));
		url	= siteurl+"from:ajax/"+url+"/r:"+Math.round(Math.random()*1000);
	}
	var i, ch, lastpostdate = "";
	for(i=0; i<dv.childNodes.length; i++) {
		ch	= dv.childNodes[i];
		if( !ch.id || !ch.id.match(/^post_/) ) { continue; }
		lastpostdate	= ch.getAttribute("postdate");
		break;
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseText ) { return; }
		var txt	= ltrim(req.responseText);
		if( txt.substr(0,3) != "OK:" ) { return; }
		txt	= txt.substr(3);
		dv.innerHTML	= txt;
		setTimeout(posts_synchronize_step2, 1);
		setTimeout( function() {
			var i, all	= dv.getElementsByTagName("INPUT");
			for(i=0; i<all.length; i++) {
				postform_forbid_hotkeys_conflicts(all[i]);
			}
			all	= dv.getElementsByTagName("TEXTAREA");
			for(i=0; i<all.length; i++) {
				postform_forbid_hotkeys_conflicts(all[i]);
			}
		}, 1 );
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("lastpostdate="+encodeURIComponent(lastpostdate));
	if(sync_tmout) {
		clearTimeout(sync_tmout);
		sync_tmout	= false;
	}
}
function posts_synchronize_step2()
{
	var dv	= d.getElementById("posts_html");
	if( ! dv ) { return; }
	var i, ch, h, tmp;
	for(i=0; i<dv.childNodes.length; i++) {
		ch	= dv.childNodes[i];
		if( !ch.id || !ch.id.match(/^post_/) ) { continue; }
		if( ch.style.display != "none" ) { continue; }
		if( disable_animations ) {
			ch.style.display	= "block";
			return;
		}
		h	= parseInt(ch.clientHeight, 10);
		if( isNaN(h) || h==0 ) {
			tmp	= ch.cloneNode(true);
			tmp.id	= "asdfgh"+(Math.round(Math.random()*1000));
			tmp.style.visibility	= "hidden";
			tmp.style.display		= "block";
			dv.appendChild(tmp);
			h	= parseInt(tmp.clientHeight, 10);
			tmp.style.display	= "none";
			tmp.parentNode.removeChild(tmp);
		}
		if( h == 0 ) {
			ch.style.display	= "block";
		}
		else {
			postform_htmlobject_show(ch.id, h);
		}
	}
	sync_tmout	= setTimeout( posts_synchronize, 20000 );
}
function posts_synchronize_single(post_id)
{
	var comments_open	= false;
	if( postcomments_open_state && postcomments_open_state[post_id]==1 ) {
		comments_open	= true;
	}
	var req = ajax_init(false);
	if( ! req ) { return; }
	var dv	= d.getElementById("posts_html");
	if( ! dv ) { return; }
	var pdv	= d.getElementById("post_"+post_id);
	if( ! pdv ) { return; }
	var url	= w.location.href.toString();
	if( ! url ) { return; }
	if( url.substr(0, siteurl.length) == siteurl ) {
		url	= url.substr(siteurl.length);
		url	= siteurl+"from:ajax/"+url+"/onlypost:"+post_id+"/"+(comments_open?("opencomments:"+post_id+"/"):"")+url+"/r:"+Math.round(Math.random()*1000);
	}
	else {
		url	= url.replace(/^http(s)?\:\/\//, "");
		url	= url.substr(url.indexOf("/"));
		url	= siteurl+"from:ajax/"+url+"/onlypost:"+post_id+"/"+(comments_open?("opencomments:"+post_id+"/"):"")+url+"/r:"+Math.round(Math.random()*1000);
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseText ) { return; }
		var txt	= ltrim(req.responseText);
		if( txt.substr(0,3) != "OK:" ) { return; }
		txt	= txt.substr(3);
		var ndv	= d.createElement("DIV");
		ndv.innerHTML	= txt;
		setTimeout( function() {
			ndv	= ndv.getElementsByTagName("DIV");
			if( ndv && ndv[0] ) {
				ndv	= ndv[0].cloneNode(true);
				pdv.style.visibility	= "hidden";
				dv.insertBefore(ndv, pdv);
				pdv.style.display	= "none";
				dv.removeChild(pdv);
				setTimeout( function() {
					var i, all	= ndv.getElementsByTagName("INPUT");
					for(i=0; i<all.length; i++) {
						postform_forbid_hotkeys_conflicts(all[i]);
					}
					all	= ndv.getElementsByTagName("TEXTAREA");
					for(i=0; i<all.length; i++) {
						postform_forbid_hotkeys_conflicts(all[i]);
					}
				}, 1 );
			}
		}, 1);
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	if(sync_tmout) {
		clearTimeout(sync_tmout);
		sync_tmout	= false;
	}
	req.send("");
}
function viewpost_synchronize()
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	var dv	= d.getElementById("viewpost");
	if( ! dv ) { return; }
	var url	= w.location.href.toString();
	if( ! url ) { return; }
	if( url.substr(0, siteurl.length) == siteurl ) {
		url	= url.substr(siteurl.length);
		url	= siteurl+"from:ajax/"+url+"/r:"+Math.round(Math.random()*1000);
	}
	else {
		url	= url.replace(/^http(s)?\:\/\//, "");
		url	= url.substr(url.indexOf("/"));
		url	= siteurl+"from:ajax/"+url+"/r:"+Math.round(Math.random()*1000);
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseText ) { return; }
		var txt	= ltrim(req.responseText);
		if( txt.substr(0,3) != "OK:" ) { return; }
		txt	= txt.substr(3);
		var ndv	= d.createElement("DIV");
		ndv.innerHTML	= txt;
		setTimeout( function() {
			ndv	= ndv.getElementsByTagName("DIV")[0].cloneNode(true);
			dv.style.visibility	= "hidden";
			while(dv.firstChild) {
				dv.removeChild(dv.firstChild);
			}
			dv.parentNode.insertBefore(ndv, dv);
			dv.style.display	= "none";
			dv.parentNode.removeChild(dv);
		}, 1);
		setTimeout( function() {
			var i, all	= ndv.getElementsByTagName("INPUT");
			for(i=0; i<all.length; i++) {
				postform_forbid_hotkeys_conflicts(all[i]);
			}
			all	= ndv.getElementsByTagName("TEXTAREA");
			for(i=0; i<all.length; i++) {
				postform_forbid_hotkeys_conflicts(all[i]);
			}
		}, 1 );
	}
	req.open("POST", url, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("");
}

var flybox_opened	= false;
function flybox_open(width, height, title, html)
{
	if( flybox_opened ) { return false; }
	flybox_opened	= true;
	var outer	= d.getElementById("flybox_container");
	var box	= d.getElementById("flybox_box");
	var ttl	= d.getElementById("flybox_title");
	var cnt	= d.getElementById("flybox_main");
	if( !outer || !box || !cnt ) { return false; }
	if( ! width ) { width = 600; }
	if( ! height ) { height = 500; }
	if( ! title ) { title = ""; }
	if( ! html ) { html = ""; }
	var page_size	= get_screen_preview_size();
	box.style.width	= width + "px";
	box.style.height	= height + "px";
	var left	= Math.round((page_size[0] - width) / 2);
	var top	= Math.round((page_size[1] - height) / 2);
	left	= Math.max(left, 10);
	top	= Math.max(top, 10);
	box.style.left	= left + "px";
	box.style.top	= top + "px";
	ttl.innerHTML	= title;
	setTimeout( function() { outer.style.display = "block"; }, 1 );
	setTimeout( function() { cnt.innerHTML	= html; }, 1 );
	//setTimeout( function() { if(msgbox_close) { msgbox_close(); } }, 50 );
	//setTimeout( function() { if(postform_topmsg_close) { postform_topmsg_close(); } }, 50 );
}
function flybox_close()
{
	flybox_opened	= false;
	d.getElementById("flybox_container").style.display	= "none";
	setTimeout( function(){ d.getElementById("flybox_main").innerHTML = ""; }, 1 );
	if( navigator.appName.toLowerCase().indexOf("opera") != -1 ) {
		setTimeout( function(){ d.body.innerHTML += ""; }, 2 );
	}
}

function flybox_open_att_image(width, height, title, postid)
{
	width	= Math.max(width, 400);
	var html	= '<iframe src="'+siteurl+'getattachment/tp:image/pid:'+postid+'" style="width:'+(width+10)+'px; height:'+(height+41)+'px;" border="0" frameborder="0" style="border:0px solid;" scrolling="no"></iframe>';
	return flybox_open(width+34, height+129, title, html);
}
function flybox_open_att_videoembed(width, height, title, postid)
{
	var html	= '<iframe src="'+siteurl+'getattachment/tp:videoembed/pid:'+postid+'" style="width:'+(width+10)+'px; height:'+(height+41)+'px;" border="0" frameborder="0" style="border:0px solid;" scrolling="no"></iframe>';
	return flybox_open(width+34, height+129, title, html);
}


function privmsg_usrfilter_setusr(username, check_first)
{
	d.privform.privusr_inp.blur();
	d.privform.privusr_inp.disabled	= true;
	d.privform.privusr_inp.style.cursor	= "wait";
	var f_ok	= function() {
		var url	= w.location.href.toString();
		if( ! url ) { return; }
		url	= url.replace(/usr\:[a-z0-9а-я_-]+(\/)?/i, "");
		url	= url.replace(/pg\:[0-9]+(\/)?/i, "");
		url	= url.replace(/\/+$/, "");
		url	+= "/usr:"+username;
		w.location.href	= url;
	};
	var f_err	= function() {
		d.privform.privusr_inp.disabled	= false;
		d.privform.privusr_inp.style.cursor	= "text";
		d.privform.privusr_inp.focus();
		setTimeout( function() { d.privform.privusr_inp.style.color = "#f00"; }, 1 );
		setTimeout( function() { d.privform.privusr_inp.style.color = "#000"; }, 400 );
		setTimeout( function() { d.privform.privusr_inp.style.color = "#f00"; }, 800 );
		setTimeout( function() { d.privform.privusr_inp.style.color = "#000"; }, 1200 );
	};
	if( check_first ) {
		var req = ajax_init(true);
		if( ! req ) { return; }
		req.onreadystatechange	= function() {
			if( req.readyState != 4  ) { return; }
			if( ! req.responseXML ) { return; }
			var data	= req.responseXML.getElementsByTagName("result");
			if( !data || !data[0] ) {
				f_err();
				return;
			}
			data	= data[0].firstChild;
			if( !data ) {
				f_err();
				return;
			}
			username	= data.nodeValue;
			f_ok();
		}
		req.open("POST", siteurl+"ajax/checkname/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("datatype=username&word="+encodeURIComponent(username));
		return;
	}
	f_ok();
}
function privmsg_usrfilter_reset()
{
	d.privform.privusr_inp.value	= "";
	d.privform.privusr_inp.disabled	= false;
	d.privform.privusr_inp.style.cursor	= "text";
	d.getElementById("pmfilterok").style.display	= "none";
	d.getElementById("pmfilter").style.display	= "block";
	d.privform.privusr_inp.focus();
}


var posts_topbtns_hd	= {};
var posts_topbtns_sh	= {};
function show_post_topbtns(pid)
{
	var div	= document.getElementById("post_btns_top_"+pid);
	if( ! div ) { return; }
	if( posts_topbtns_hd[pid] ) {
		clearTimeout(posts_topbtns_hd[pid]);
	}
	posts_topbtns_hd[pid]	= null;
	posts_topbtns_sh[pid]	= setTimeout( function() { div.style.display = "block"; }, 100 );
}
function hide_post_topbtns(pid, fast)
{
	var div	= document.getElementById("post_btns_top_"+pid);
	if( ! div ) { return; }
	if( posts_topbtns_hd[pid] ) {
		return;
	}
	if( posts_topbtns_sh[pid] ) {
		clearTimeout(posts_topbtns_sh[pid]);
	}
	posts_topbtns_sh[pid]	= null;
	if( fast ) {
		div.style.display = "none";
		return;
	}
	posts_topbtns_hd[pid]	= setTimeout( function() { div.style.display = "none"; }, 200 );
}


postcomments_open_state	= [];	// 0 -> closed, 1 => open, 2 => in progress
function postcomments_open(post_id)
{
	var state	= postcomments_open_state[post_id];
	if( state == 1 ) {
		postcomments_close(post_id);
		//d.getElementById("postcomments_"+post_id+"_textarea").focus();
		return;
	}
	if( state == 2 ) {
		return;
	}
	var p	= d.getElementById("post_"+post_id);
	if( ! p ) {
		return;
	}
	p.style.height	= "auto";
	p.style.overflow	= "hidden";
	var dv	= d.getElementById("postcomments_"+post_id);
	var h	= parseInt(dv.clientHeight, 10);
	if( isNaN(h) || h==0 ) {
		tmp	= dv.cloneNode(true);
		tmp.id	= "asdfgh"+(Math.round(Math.random()*1000));
		tmp.style.visibility	= "hidden";
		tmp.style.display		= "block";
		dv.parentNode.appendChild(tmp);
		h	= parseInt(tmp.clientHeight, 10);
		tmp.style.display	= "none";
		tmp.parentNode.removeChild(tmp);
	}
	if( h == 0 ) {
		pf_htmlobjects_shown["postcomments_"+post_id]	= true;
		dv.style.display	= "block";
		dv.style.height	= "auto";
	}
	else {
		postform_htmlobject_show(dv.id, h, function(){ dv.style.height="auto"; try{ d.getElementById("postcomments_"+post_id+"_textarea").focus(); } catch(e) {} } );
	}
	try{ d.getElementById("postcomments_"+post_id+"_textarea").focus(); } catch(e) { }
	postcomments_open_state[post_id]	= 1;
	postcomments_mark(post_id);
}
function postcomments_mark(post_id)
{
	var req = ajax_init(false);
	if( req ) {
		req.onreadystatechange	= function() {
			if( req.readyState != 4  ) { return; }
			var cnt	= d.getElementById("post_newcomments_"+post_id);
			if( cnt ) {
				cnt.style.display	= "none";
			}
		}
		req.open("POST", siteurl+"ajax/post-comments-mark/ajaxtp:txt/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("postid="+encodeURIComponent(post_id));
	}
}
function postcomments_close(post_id, callback_after)
{
	var state	= postcomments_open_state[post_id];
	if( state != 1 ) {
		return;
	}
	postcomments_collapse(post_id);
	pf_htmlobjects_shown["postcomments_"+post_id]	= true;
	postform_htmlobject_hide("postcomments_"+post_id, function() { postcomments_open_state[post_id] = 0; if(callback_after) { callback_after(); } } );
}
function postcomments_expand(post_id)
{
	var slim	= d.getElementById("postcomments_"+post_id+"_slimform");
	if( slim ) {
		slim.style.display	= "none";
		d.getElementById("postcomments_"+post_id+"_bigform").style.display	= "block";
		d.getElementById("postcomments_"+post_id+"_textarea").focus();
	}
}
function postcomments_collapse(post_id)
{
	var slim	= d.getElementById("postcomments_"+post_id+"_slimform");
	if( slim ) {
		d.getElementById("postcomments_"+post_id+"_textarea").blur();
		d.getElementById("postcomments_"+post_id+"_bigform").style.display	= "none";
		slim.style.display	= "block";
	}
}
function postcomments_submit(post_id)
{
	if( postcomments_open_state[post_id] == 2 ) {
		return;
	}
	var txt	= d.getElementById("postcomments_"+post_id+"_textarea");
	var btn	= d.getElementById("postcomments_"+post_id+"_submitbtn");
	txt.value	= trim(txt.value);
	if( txt.value === "" ) {
		txt.focus();
		return;
	}
	txt.disabled	= true;
	btn.disabled	= true;
	txt.style.cursor	= "wait";
	btn.style.cursor	= "wait";
	btn.blur();
	postcomments_open_state[post_id]	= 2;
	var req = ajax_init(false);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseText ) { return; }
		var res	= trim(req.responseText);
		if( res != "OK" ) {
			return;
		}
		postcomments_open_state[post_id]	= 1;
		if( d.getElementById("viewpost") ) {
			viewpost_synchronize();
		}
		else {
			posts_synchronize_single(post_id);
		}
		postcomments_open_state[post_id] = 0;
	}
	req.open("POST", siteurl+"ajax/post-comment/ajaxtp:txt/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("postid="+encodeURIComponent(post_id)+"&message="+encodeURIComponent(txt.value));
}
function postcomment_delete(post_id, comment_id, confirm_msg, callback_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( confirm_msg ) {
		if( ! confirm(confirm_msg) ) { return; }
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		if( callback_after ) {
			callback_after();
		}
		if( d.getElementById("viewpost") ) {
			viewpost_synchronize();
		}
		else {
			posts_synchronize_single(post_id);
		}
		if(msgbox_close) { msgbox_close(); };
		if(postform_topmsg_close) { postform_topmsg_close(); };
	}
	req.open("POST", siteurl+"ajax/delcomment/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("postid="+encodeURIComponent(post_id)+"&commentid="+encodeURIComponent(comment_id));
	d.getElementById("postcomment_"+comment_id).style.cursor	= "wait";
}


function textarea_autoheight(textarea)
{
	var mn	= 40;
	var mx	= 390;
	if( !textarea || !textarea.nodeName || textarea.nodeName!="TEXTAREA" ) {
		return;
	}
	if( !textarea.id ) {
		textarea.id	= "tmptxtarea_"+Math.round(Math.random()*10000);
	}
	dv	= d.getElementById("tmptxtdv_"+textarea.id);
	if( !dv ) {
		var dv	= d.createElement("DIV");
		dv.id		= "tmptxtdv_"+textarea.id;
		dv.className	= textarea.className;
		dv.style.width		= textarea.clientWidth + "px";
		dv.style.overflow		= "auto";
		dv.style.whiteSpace	= "pre-wrap";
		dv.style.visibility	= "hidden";
		dv.style.display		= "block";
		dv.style.position		= "absolute";
		textarea.parentNode.appendChild(dv);
	}
	dv.innerHTML	= "";
	dv.appendChild(d.createTextNode(textarea.value+"\n"));
	var	h = parseInt(dv.clientHeight, 10);
	if( isNaN(h) ) {
		return;
	}
	if( h <= mn ) {
		h	= mn;
		textarea.style.overflow	= "hidden";
	}
	else if( h >= mx ) {
		h	= mx;
		textarea.style.overflow	= "auto";
	}
	else {
		textarea.style.overflow	= "hidden";
	}
	textarea.style.height	= h + "px";
}



if( ! pf_htmlobjects_shown ) {
	pf_htmlobjects_shown	= {};
}
var last_extshare_openbox	= false;
var last_extshare_tmout		= false;
function extshare_openbox(tmpid)
{
	if( pf_htmlobjects_shown["extshare_tmpbox_open_"+tmpid] == undefined ) {
		pf_htmlobjects_shown["extshare_tmpbox_open_"+tmpid]	= 0;
	}
	if( pf_htmlobjects_shown["extshare_tmpbox_open_"+tmpid] != 0 ) {
		return true;
	}
	if( last_extshare_openbox ) {
		last_extshare_openbox.style.display	= "none";
		pf_htmlobjects_shown[last_extshare_openbox.id]	= 0;
		last_extshare_openbox	= false;
	}
	var lnk	= d.getElementById("extshare_link_"+tmpid);
	if( !lnk ) { return false; }
	var c	= obj_find_coords(lnk);
	if( !c || c[0]==0 || c[1]==0 ) {
		return false;
	}
	var bx	= d.getElementById("extshare_tmpbox_"+tmpid);
	if( !bx ) { return false; }
	
	var bx2	= d.getElementById("extshare_tmpbox_open_"+tmpid);
	if( bx2 ) {
		bx2.style.display	= "none";
	}
	else {
		bx2	= bx.cloneNode(true);
		bx2.id	= "extshare_tmpbox_open_"+tmpid;
		bx2.style.display	= "none";
		d.body.appendChild(bx2);
	}
	bx2.onmouseover	= function() { extshare_keepopen(); };
	bx2.onmouseout	= function() { extshare_closebox(tmpid); };
	bx2.style.position	= "absolute";
	bx2.style.left	= c[0]+"px";
	bx2.style.top	= (c[1]+15)+"px";
	var i, lnks	= bx2.getElementsByTagName("A");
	for(i=0; i<lnks.length; i++) {
		lnks[i].onmouseover	= function() { extshare_keepopen(); };
	}
	if( disable_animations ) {
		bx2.style.display	= "block";
		pf_htmlobjects_shown[bx2.id]	= 1;
	}
	else {
		var h	= parseInt(bx2.clientHeight, 10);
		if( isNaN(h) || h==0 ) {
			bx2.style.visiblity	= "hidden";
			bx2.style.display	= "block";
			h	= parseInt(bx2.clientHeight, 10);
			bx2.style.display	= "none";
			bx2.style.visibility	= "visible";
		}
		if( h == 0 ) {
			bx2.style.display	= "block";
			pf_htmlobjects_shown[bx2.id]	= 1;
		}
		else {
			postform_htmlobject_show(bx2.id, h );
		}
	}
	last_extshare_openbox	= bx2;
}
function extshare_closebox(tmpid)
{
	if( last_extshare_tmout ) {
		clearTimeout( last_extshare_tmout );
	}
	var bx2	= d.getElementById("extshare_tmpbox_open_"+tmpid);
	last_extshare_tmout	= setTimeout( function() {
		if( disable_animations ) {
			bx2.style.display	= "none";
			pf_htmlobjects_shown[bx2.id]	= 0;
		}
		else {
			pf_htmlobjects_shown[bx2.id]	= 1;
			postform_htmlobject_hide(bx2.id);
		}
		last_extshare_openbox	= false;
		last_extshare_tmout	= false;
	}, 300);
}
function extshare_keepopen()
{
	setTimeout( function() {
		if( last_extshare_tmout ) {
			clearTimeout( last_extshare_tmout );
		}
	}, 50);
}