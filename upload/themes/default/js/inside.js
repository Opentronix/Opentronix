var _d	= document;
var _w	= window;
var d		= document;
var w		= window;
var siteurl	= "/";

var disable_animations	= false;

var window_loaded	= false;
if( d.addEventListener ) {
	d.addEventListener("load", window_onload, false);
	w.addEventListener("load", window_onload, false);
}
else if( d.attachEvent ) {
	d.attachEvent("onload", window_onload);
	w.attachEvent("onload", window_onload);
}
function window_onload() {
	if( window_loaded ) {
		return;
	}
	window_loaded	= true;
	setInterval(keep_session, 300000);
	if(posts_synchronize) {
		setTimeout(posts_synchronize, 20000);
	}
	if(dbrd_check_tabs) {
		if( w.location.pathname && w.location.pathname.match("/dashboard") ) {
			setTimeout(dbrd_check_tabs, 13349);
		}
	}
}

function keep_session()
{
	var req = ajax_init();
	if( ! req ) { return; }
	req.onreadystatechange	= function() { };
	req.open("POST", siteurl+"ajax/keepsession/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send(null);
}

function ajax_init(is_xml)
{
	var req = false;
	if (w.XMLHttpRequest) {
		req = new XMLHttpRequest();
		if (req.overrideMimeType) {
			if( is_xml ) { req.overrideMimeType("application/xml"); }
			else { req.overrideMimeType("text/plain"); }
		}
	} else if (w.ActiveXObject) {
		try { req = new w.ActiveXObject("MSXML3.XMLHTTP"); } catch(exptn) {
		try { req = new w.ActiveXObject("MSXML2.XMLHTTP.3.0"); } catch(exptn) {
		try { req = new w.ActiveXObject("Msxml2.XMLHTTP"); } catch(exptn) {
		try { req = new w.ActiveXObject("Microsoft.XMLHTTP"); } catch(exptn) {
		}}}}
	}
	return req;
}

function obj_find_coords(obj)
{
	var X=0, Y=0;
	if( obj.offsetParent ) {
		X =	obj.offsetLeft;
		Y =	obj.offsetTop;
		if( obj.offsetParent ) {
			do {
				obj = obj.offsetParent;
				X +=	obj.offsetLeft;
				Y +=	obj.offsetTop;
			}
			while( obj.offsetParent );
		}
	}
	return [X,Y];
}
function get_screen_preview_size()
{
	var w=0, h=0;
	if( typeof( window.innerWidth ) == 'number' ) {
		w	= window.innerWidth;
		h	= window.innerHeight;
	}
	else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		w	= document.documentElement.clientWidth;
		h	= document.documentElement.clientHeight;
	}
	else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		w	= document.body.clientWidth;
		h	= document.body.clientHeight;
	}
	return [w, h];
}
function get_screen_scroll()
{
	var x=0, y=0;
	if( typeof( window.pageYOffset ) == 'number' ) {
		x	= window.pageXOffset;
		y	= window.pageYOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		x	= document.body.scrollLeft;
		y	= document.body.scrollTop;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		y	= document.documentElement.scrollTop;
		x	= document.documentElement.scrollLeft;
	}
	return [x, y];
}
function preload_img()
{
	var tmp	= [];
	for(var i=0; i<arguments.length; i++) {
		tmp[i]	= new Image();
		tmp[i].src	= arguments[i];
	}
}



var dropdivs	= {};
var dropdiv_dropstep_px	= 10;
var dropdiv_dropstep_tm	= 1;

function dropdiv_open(div_id, height_offset)
{
	if( dropdivs[div_id] == 1 ) {
		return dropdiv_close(div_id);
	}
	if( dropdivs[div_id] == 2 ) {
		return false;
	}
	var div	= _d.getElementById(div_id);
	if( !div ) {
		return false;
	}
	if( disable_animations ) {
		dropdivs[div_id]		= 1;
		div.style.display		= "block";
		if( _d.addEventListener ) {
			_d.addEventListener("mouseup", function(){dropdiv_close(div_id);}, false);
		}
		else if( _d.attachEvent ) {
			_d.attachEvent("onmouseup", function(){dropdiv_close(div_id);} );
		}
		return true;
	}
	var height	= parseInt(div.style.height, 10);
	if( !height ) {
		div.style.visiblity	= "hidden";
		div.style.display		= "block";
		height	= parseInt(div.clientHeight, 10);
		div.style.display		= "none";
		div.style.visiblity	= "visible";
		if( height_offset ) {
			height	+= height_offset;
		}
	}
	if( !height ) {
		return false;
	}
	var h	= 0;
	var func	= function() {
		div.style.height	= h+"px";
		if( h >= height ) {
			dropdivs[div_id]	= 1;
			div.style.height	= height+"px";
			div.style.overflow	= div.getAttribute("orig_overflow");
			if( _d.addEventListener ) {
				_d.addEventListener("mouseup", function(){dropdiv_close(div_id);}, false);
			}
			else if( _d.attachEvent ) {
				_d.attachEvent("onmouseup", function(){dropdiv_close(div_id);} );
			}
			return true;
		}
		h	+= dropdiv_dropstep_px;
		setTimeout( func, dropdiv_dropstep_tm );
	};
	var tmp = div.getAttribute("orig_overflow");
	if( ! tmp ) {
		tmp	= div.style.overflow ? div.style.overflow : "visible";
	}
	div.setAttribute("orig_overflow", tmp);
	div.style.overflow	= "hidden";
	div.style.display		= "block";
	dropdivs[div_id]	= 2;
	func();
}

function dropdiv_close(div_id, do_it_fast)
{
	if( dropdivs[div_id] == 0 ) {
		return true;
	}
	if( dropdivs[div_id] == 2 ) {
		return false;
	}
	var div	= _d.getElementById(div_id);
	if( !div ) {
		return false;
	}
	if( disable_animations || do_it_fast ) {
		div.style.display	= "none";
		dropdivs[div_id]	= 0;
		return;
	}
	var h	= parseInt(div.style.height, 10);
	var orig_h	= h;
	var func	= function() {
		div.style.height	= Math.max(0,h)+"px";
		if( h <= 0 ) {
			div.style.display	= "none";
			div.style.height	= orig_h+"px";
			dropdivs[div_id]	= 0;
			return true;
		}
		h	-= dropdiv_dropstep_px;
		setTimeout( func, dropdiv_dropstep_tm );
	};
	div.style.overflow	= "hidden";
	dropdivs[div_id]	= 2;
	func();
}

function hdr_search_settype(val, txt)
{
	_d.getElementById("search_drop_lnk").innerHTML	= txt;
	_d.search_form.lookin.value	= val;
	_d.search_form.lookfor.focus();
}

function trim(str)
{
	if( typeof(str) != "string" ) {
		return str;
	}
	str	= str.replace(/^\s+/, "");
	str	= str.replace(/\s+$/, "");
	return str;
}

function ltrim(str)
{
	if( typeof(str) != "string" ) {
		return str;
	}
	str	= str.replace(/^\s+/, "");
	return str;
}

function userpage_top_tooltip(txt)
{
	var dv	= d.getElementById("usrpg_top_tooltip");
	if( !dv || !dv.firstChild ) {
		return;
	}
	if( txt == "" ) {
		dv.style.display	= "none";
		dv.firstChild.innerHTML	= "";
	}
	else {
		dv.firstChild.innerHTML	= txt;
		dv.style.display	= "block";
	}
}

function user_follow(username, thislink, unfollow_linkid, msg_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.cursor	= "pointer";
		thislink.style.display	= "none";
		if(unfollow_linkid) {
			d.getElementById(unfollow_linkid).style.display	= "block";
		}
		if(msg_after) {
			slim_msgbox(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=on&username="+encodeURIComponent(username));
	thislink.style.cursor	= "wait";
}
function user_unfollow(username, thislink, follow_linkid, confirm_msg, msg_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( ! confirm(confirm_msg) ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.cursor	= "pointer";
		thislink.style.display	= "none";
		d.getElementById(follow_linkid).style.display	= "block";
		if(msg_after) {
			slim_msgbox(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=off&username="+encodeURIComponent(username));
	thislink.style.cursor	= "wait";
}
function group_follow(groupname, thislink, unfollow_linkid, msg_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.cursor	= "pointer";
		thislink.style.display	= "none";
		d.getElementById(unfollow_linkid).style.display	= "block";
		if(msg_after) {
			slim_msgbox(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=on&groupname="+encodeURIComponent(groupname));
	thislink.style.cursor	= "wait";
}
function group_unfollow(groupname, thislink, follow_linkid, confirm_msg, msg_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( ! confirm(confirm_msg) ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.cursor	= "pointer";
		thislink.style.display	= "none";
		d.getElementById(follow_linkid).style.display	= "block";
		if(msg_after) {
			slim_msgbox(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=off&groupname="+encodeURIComponent(groupname));
	thislink.style.cursor	= "wait";
}


var msgbox_to_close	= {};
function msgbox_close(which) {
	if( which ) {
		if( msgbox_to_close[which] == true ) {
			pf_htmlobjects_shown[which]	= 1;
			postform_htmlobject_hide(which);
		}
		msgbox_to_close[which]	= null;
		return;
	}
	for(var i in msgbox_to_close) {
		if( msgbox_to_close[i] != true ) {
			continue;
		}
		msgbox_close(i);
	}
}

function slim_msgbox(msg)
{
	var dv	= d.getElementById("slim_msgbox");
	if( ! dv ) {
		return;
	}
	if( pf_htmlobjects_shown["slim_msgbox"] == 1 ) {
		postform_htmlobject_hide("slim_msgbox", function() { slim_msgbox(msg); } );
		return;
	}
	d.getElementById("slim_msgbox_msg").innerHTML	= msg;
	postform_htmlobject_show("slim_msgbox", 36, function() { msgbox_to_close.slim_msgbox = true; } );
}


var dbrd_tabs_to_check	= ["all", "@me", "private", "commented", "feeds"];
var dbrd_tabs_timeout	= false;
function dbrd_check_tabs()
{
	if( dbrd_tabs_to_check.length == 0 ) { return; }
	var req = ajax_init(false);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseText ) { return; }
		var txt	= ltrim(req.responseText);
		if( txt.substr(0,3) != "OK:" ) { return; }
		txt	= trim(txt.substr(3));
		txt	= txt.split("\n");
		if( txt.length > 0 ) {
			var i, j, tb, nm, dv;
			for(i=0; i<txt.length; i++) {
				txt[i]	= txt[i].split(":");
				if( txt[i].length != 2 ) {
					continue;
				}
				tb	= trim(txt[i][0]);
				nm	= parseInt(txt[i][1],10);
				if( tb!="all" && tb!="@me" && tb!="private" && tb!="commented" && tb!="feeds" ) {
					continue;
				}
				dv	= d.getElementById( tb=="@me" ? "dbrd_tab_mention" : ("dbrd_tab_"+tb) );
				if( dv && nm>0 && dv.parentNode.parentNode.className.indexOf("onitem")==-1 ) {
					dv.innerHTML	= nm;
					dv.style.display	= "block";
				}
				else if( dv && nm==0 ) {
					dv.innerHTML	= "";
					dv.style.display	= "none";
				}
			}
		}
		dbrd_tabs_timeout	= setTimeout( dbrd_check_tabs, 10000 );
	};
	req.open("POST", siteurl+"ajax/checktabs/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("checktabs="+encodeURIComponent(dbrd_tabs_to_check.join(",")));
	if(dbrd_tabs_timeout) {
		clearTimeout(dbrd_tabs_timeout);
		dbrd_tabs_timeout	= false;
	}
}


var dbrd_box_height	= false;
function dbrd_whattodo_show()
{
	if( ! dbrd_box_height ) {
		var dv	= d.getElementById("greentodo");
		dv.style.visibility	= "hidden";
		dv.style.display		= "block";
		dbrd_box_height	= parseInt(dv.clientHeight, 10);
		dv.style.display		= "none";
		dv.style.visibility	= "visible";
	}
	d.cookie	= "dbrd_whattodo_mnm=0;expires="+(new Date(new Date().getTime()+61*24*60*60*1000).toGMTString())+";path=/";
	pf_htmlobjects_shown["closedgtd"]	= 1;
	postform_htmlobject_hide("closedgtd", function() { postform_htmlobject_show("greentodo",dbrd_box_height) } );
	var req = ajax_init(false);
	if( req ) {
		req.onreadystatechange	= function() { }
		req.open("POST", siteurl+"from:ajax/dashboard/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("toggle_whattodo=1");
	}
}
function dbrd_whattodo_hide()
{
	dbrd_box_height	= parseInt(d.getElementById("greentodo").clientHeight, 10);
	pf_htmlobjects_shown["greentodo"]	= 1;
	postform_htmlobject_hide("greentodo", function() { postform_htmlobject_show("closedgtd",25) } );
	var req = ajax_init(false);
	if( req ) {
		req.onreadystatechange	= function() { }
		req.open("POST", siteurl+"from:ajax/dashboard/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("toggle_whattodo=0");
	}
}
function dbrd_hide_wrongaddr_warning()
{
	var h	= parseInt(d.getElementById("dbrd_wrongaddr_warning").clientHeight, 10);
	pf_htmlobjects_shown["dbrd_wrongaddr_warning"]	= 1;
	postform_htmlobject_hide("dbrd_wrongaddr_warning");
	var req = ajax_init(false);
	if( req ) {
		req.onreadystatechange	= function() { }
		req.open("POST", siteurl+"from:ajax/dashboard/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("hide_wrongaddr_warning=1");
	}
}

var dbrd_grpmenu_height	= false;
var dbrd_grpmenu_showst	= 0;
function dbrd_groupmenu_toggle()
{
	if( dbrd_grpmenu_showst == 2 ) {
		return;
	}
	if( ! dbrd_grpmenu_height ) {
		var dv	= d.getElementById("dbrd_menu_groups");
		if( dv.style.display != "none" ) {
			dbrd_grpmenu_height	= parseInt(dv.clientHeight, 10);
		}
		else {
			dv.style.visibility	= "hidden";
			dv.style.display		= "block";
			dbrd_grpmenu_height	= parseInt(dv.clientHeight, 10);
			dv.style.display		= "none";
			dv.style.visibility	= "visible";
		}
	}
	if( dbrd_grpmenu_showst == 0 ) {
		dbrd_grpmenu_showst	= 2;
		pf_htmlobjects_shown["dbrd_menu_groups"]	= 0;
		postform_htmlobject_show("dbrd_menu_groups", dbrd_grpmenu_height, function() { dbrd_grpmenu_showst = 1; } );
		d.getElementById("dbrd_menu_groupsbtn").className	= "dropio dropped";
		var req = ajax_init(false);
		if( req ) {
			req.onreadystatechange	= function() { }
			req.open("POST", siteurl+"from:ajax/dashboard/r:"+Math.round(Math.random()*1000), true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("toggle_grpmenu=1");
		}
	}
	else {
		dbrd_grpmenu_showst	= 2;
		pf_htmlobjects_shown["dbrd_menu_groups"]	= 1;
		postform_htmlobject_hide("dbrd_menu_groups", function() { dbrd_grpmenu_showst = 0; } );
		d.getElementById("dbrd_menu_groupsbtn").className	= "dropio";
		var req = ajax_init(false);
		if( req ) {
			req.onreadystatechange	= function() { }
			req.open("POST", siteurl+"from:ajax/dashboard/r:"+Math.round(Math.random()*1000), true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("toggle_grpmenu=0");
		}
	}
	
}

function srchposts_togglefilt(which)
{
	var lnk	= d.getElementById("srchposts_droplnk_"+which);
	var box	= d.getElementById("srchposts_dropbox_"+which);
	var ison	= box.style.display == "block";
	
	if( ison ) {
		lnk.className	= "sdropper";
		box.style.display	= "none"
	}
	else {
		lnk.className	= "sdropper dropppped";
		box.style.display	= "block"
	}
}
function save_search_on(ajax_url)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	var lnk_on	= d.getElementById("savesearch");
	var lnk_off	= d.getElementById("remsearch");
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		var txt	= ltrim(req.responseText);
		if( txt.substr(0,3) != "OK:" ) { return; }
		lnk_on.style.cursor	= "pointer";
		lnk_on.style.display	= "none";
		lnk_off.style.display	= "block";
	}
	req.open("POST", ajax_url+"/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("savesearch=on");
	lnk_on.style.cursor	= "wait";
}
function save_search_off(ajax_url, confirm_msg)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( ! confirm(confirm_msg) ) { return; }
	var lnk_on	= d.getElementById("savesearch");
	var lnk_off	= d.getElementById("remsearch");
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		var txt	= ltrim(req.responseText);
		if( txt.substr(0,3) != "OK:" ) { return; }
		lnk_off.style.cursor	= "pointer";
		lnk_off.style.display	= "none";
		lnk_on.style.display	= "block";
	}
	req.open("POST", ajax_url+"/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("savesearch=off");
	lnk_off.style.cursor	= "wait";
}

