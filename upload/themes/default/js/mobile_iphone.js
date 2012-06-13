var d	= document;
var w	= window;
var _d	= document;
var _w	= window;
var siteurl	= "";


function toggle_menu(b) {
	var lnk	= d.getElementById("menubtn");
	var menuc	= d.getElementById("menu_container");
	var menu	= d.getElementById("menu");
	var ovr	= d.getElementById("blackoverlay");
	ovr.onclick	= function() {
		toggle_menu(false);
	};
	var size_overlay	= function() {
		ovr.style.height	= (parseInt(d.body.clientHeight,10)-41)+"px";
	};
	size_overlay();
	d.body.removeEventListener("orientationchange", size_overlay, false);
	d.body.addEventListener("orientationchange", size_overlay, false);
	toggle_dropmenu(false);
	toggle_listfilter(false);
	np_sharewith(false);
	b	= b===undefined ? menuc.style.display=="none" : b;
	if( ! b ) {
		menuc.style.display	= "none";
		menu.style.display	= "none";
		lnk.className		= "";
	}
	else {
		menu_update_orientation();
		menuc.style.display	= "block";
		menu.style.display	= "block";
		lnk.className		= "clicked";
	}
}
function toggle_dropmenu(b) {
	var lnk  = d.getElementById("fdropper");
	var menu = d.getElementById("dropmenu");
	if( menu ) {
		b	= b===undefined ? menu.style.display=="none" : b;
		menu.style.display = b ? "block" : "none";
	}
	if( lnk ) {
		lnk.className = b ? "dropped" : "";
	}
}
function toggle_listfilter(b) {
	var lnk  = d.getElementById("listfilterchosen");
	var menu = d.getElementById("listfilteroptions");
	if( menu ) {
		b	= b===undefined ? menu.style.display=="none" : b;
		menu.style.display = b ? "block" : "none";
	}
	if( lnk ) {
		lnk.className = b ? "dropped" : "";
	}
}
function menu_update_orientation() {
	if( w.orientation === undefined ) { return; }
	var o	= w.orientation;
	var m	= d.getElementById("menu");
	if( ! m ) { return; }
	if( o == 0 || o == 180 ) {
		m.className	= "";
	}
	else if( o == 90 || o == -90 ) {
		m.className	= "rotated";
	}	
}

w.addEventListener("load", function() {
	setInterval(keep_session, 300000);
	d.body.addEventListener("orientationchange", menu_update_orientation, false);
}, false);

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

function my_scroll_page_to(h) {
	if( d.body.scrollTop === undefined ) {
		w.scroll(0, h);
		return;
	}
	var start_scroll	= parseInt( typeof(w.pageYOffset)=='number' ? w.pageYOffset : d.body.scrollTop, 10);
	if( isNaN( start_scroll ) ) {
		w.scroll(0, h);
		return;
	}
	if( start_scroll == h ) {
		return;
	}
	var step	= start_scroll>h ? -12 : 12;
	var breakf	= false;
	var func	= function() {
		start_scroll	+= step;
		w.scroll(0, start_scroll);
		if( start_scroll == h ) {
			return;
		}
		if( step > 0 && start_scroll > h ) {
			return;
		}
		if( step < 0 && start_scroll < h ) {
			return;
		}
		if( breakf ) {
			return;
		}
		setTimeout( func, 1 );
	}
	func();
	d.body.addEventListener("orientationchange", function(){ breakf = true; }, false);
}


function load_more_results(div_id, current_results, all_results) {
	current_results	= parseInt(current_results, 10);
	all_results		= parseInt(all_results, 10);
	var url	= w.location.href.toString();
	if( ! url ) { return; }
	if( url.substr(0, siteurl.length) == siteurl ) {
		url	= url.substr(siteurl.length);
		if( url.indexOf("#") != -1 ) {
			url	= url.substr(0, url.indexOf("#"));
		}
		url	= siteurl+"from:ajax/"+url+"/start_from:"+(current_results+1)+"/r:"+Math.round(Math.random()*1000);
	}
	else {
		url	= url.replace(/^http(s)?\:\/\//, "");
		url	= url.substr(url.indexOf("/"));
		if( url.indexOf("#") != -1 ) {
			url	= url.substr(0, url.indexOf("#"));
		}
		url	= siteurl+"from:ajax/"+url+"/r:"+Math.round(Math.random()*1000);
	}
	var req	= ajax_init(false);
	if( ! req ) { return false; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		var txt	= trim(req.responseText);
		var num	= txt.match(/^OK\:([0-9]+)\:/g);
		if( ! num ) { return; }
		num	= num.toString().match(/([0-9]+)/);
		num	= parseInt(num, 10);
		if( ! num ) { return; }
		txt	= txt.replace(/^OK\:([0-9]+)\:/, "");
		txt	= trim(txt);
		var dv	= d.createElement("DIV");
		dv.innerHTML	= txt;
		d.getElementById(div_id).appendChild(dv);
		setTimeout( function() { my_scroll_page_to(dv.offsetTop-30); }, 5);
		if( current_results+num+1 >= all_results ) {
			d.getElementById("loadmore").style.display	= "none";
		}
		d.getElementById("loadmorelink").onclick	= function() {
			load_more_results(div_id, current_results+num+1, all_results);
		};
		d.getElementById("loadmoreloader").style.display	= "none";
	}
	req.open("GET", url, true);
	req.send("");
	d.getElementById("loadmoreloader").style.display	= "block";
	d.getElementById("loadmorelink").blur();
}



function user_follow(username, thislink, unfollow_linkid, msg_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.display	= "none";
		if(unfollow_linkid) {
			d.getElementById(unfollow_linkid).style.display	= "block";
		}
		if(msg_after) {
			alert(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=on&username="+encodeURIComponent(username));
}
function user_unfollow(username, thislink, follow_linkid, msg_after, confirm_msg)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( ! confirm(confirm_msg) ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.display	= "none";
		d.getElementById(follow_linkid).style.display	= "block";
		if(msg_after) {
			alert(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=off&username="+encodeURIComponent(username));
}
function group_follow(groupname, thislink, unfollow_linkid, msg_after)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.display	= "none";
		d.getElementById(unfollow_linkid).style.display	= "block";
		var tmp	= d.getElementById("lnknp");
		if( tmp ) {
			tmp.style.display	= "";
		}
		if(msg_after) {
			alert(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=on&groupname="+encodeURIComponent(groupname));
}
function group_unfollow(groupname, thislink, follow_linkid, msg_after, confirm_msg)
{
	var req = ajax_init(false);
	if( ! req ) { return; }
	if( ! confirm(confirm_msg) ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( req.responseText != "OK" ) { return; }
		thislink.style.display	= "none";
		d.getElementById(follow_linkid).style.display	= "block";
		var tmp	= d.getElementById("lnknp");
		if( tmp ) {
			tmp.style.display	= "none";
		}
		if(msg_after) {
			alert(msg_after);
		}
	}
	req.open("POST", siteurl+"ajax/follow/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("type=off&groupname="+encodeURIComponent(groupname));
}

var pferr_user1	= "";
var pferr_user2	= "";
var pferr_user3	= "";
var pferr_msg	= "";
var current_usr	= "";
var pf_msg_max_length	= 160;
function np_sharewith(b) {
	var dv	= d.getElementById("swdropmenu");
	if( ! dv ) { return; }
	b	= b===undefined ? dv.style.display=="none" : b;
	dv.style.display	= b ? "block" : "none";
}
function np_sharewith_all(txt) {
	d.pf.sharewith.value	= "all";
	d.getElementById("swinput").style.display	= "none";
	d.getElementById("sharewith_sel").innerHTML	= txt;
	d.getElementById("swdropper").style.display	= "block";
	np_sharewith(false);
	d.pf.message.focus();
}
function np_sharewith_group(title) {
	d.pf.sharewith.value	= "group:"+title;
	d.getElementById("swinput").style.display	= "none";
	d.getElementById("sharewith_sel").innerHTML	= title;
	d.getElementById("swdropper").style.display	= "block";
	np_sharewith(false);
	d.pf.message.focus();
}
function np_sharewith_user(username) {
	d.pf.sharewith.value	= "user";
	d.getElementById("swdropper").style.display	= "none";
	d.pf.sharewith_inp.value	= username;
	d.getElementById("swinput").style.display	= "block";
	np_sharewith(false);
	d.pf.sharewith_inp.focus();
}
function pf_textarea_blur() {
	if( !d.pf ) { return; }
	d.pf.message.value	= trim(d.pf.message.value);
	d.pf.sharewith.value	= trim(d.pf.sharewith.value);
	if( d.pf.sharewith.value == "user" && d.pf.sharewith_inp.value != "" ) {
		d.pf.sharewith_inp.value	= trim(d.pf.sharewith_inp.value);
		var req = ajax_init(true);
		if( ! req ) { return; }
		req.onreadystatechange	= function() {
			if( req.readyState != 4  ) { return; }
			if( req.responseXML ) {
				var data	= req.responseXML.getElementsByTagName("result");
				if( data && data[0] ) {
					data	= data[0].firstChild;
					if( ! data ) {
						alert(pferr_user2.replace("#USERNAME#",d.pf.sharewith_inp.value));
					}
				}
			}
		}
		req.open("POST", siteurl+"ajax/checkname/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("datatype=username&word="+encodeURIComponent(d.pf.sharewith_inp.value));
		return;
	}
}
var pf_submit_in_progress	= false;
function pf_submit() {
	setTimeout( function() {
		if( !d.pf ) { return; }
		if( pf_submit_in_progress ) { return; }
		if( pf_attach_in_progress ) { return; }
		if( pf_attachbox_opened ) { pf_attachbox_submit(); return; }
		pf_submit_in_progress	= true;
		d.pf.message.value	= trim(d.pf.message.value);
		if( d.pf.message.value == "" && pferr_msg ) {
			alert(pferr_msg);
			pf_submit_in_progress	= false;
			return;
		}
		if( d.pf.sharewith.value == "user" ) {
			d.pf.sharewith_inp.value	= trim(d.pf.sharewith_inp.value);
			if( d.pf.sharewith_inp.value == "" ) {
				alert(pferr_user1);
				pf_submit_in_progress	= false;
				return;
			}
			if( d.pf.sharewith_inp.value.toLowerCase() == current_usr.toLowerCase() ) {
				alert(pferr_user3);
				pf_submit_in_progress	= false;
				return;
			}
			var req = ajax_init(true);
			if( ! req ) { return; }
			req.onreadystatechange	= function() {
				if( req.readyState != 4  ) { return; }
				if( req.responseXML ) {
					var data	= req.responseXML.getElementsByTagName("result");
					if( data && data[0] ) {
						data	= data[0].firstChild;
						if( data ) {
							d.pf.sharewith_inp.value	= data.nodeValue;
							pf_submit_step2();
						}
						else {
							alert(pferr_user2.replace("#USERNAME#",d.pf.sharewith_inp.value));
							pf_submit_in_progress	= false;
						}
					}
				}
				return;
			}
			req.open("POST", siteurl+"ajax/checkname/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("datatype=username&word="+encodeURIComponent(d.pf.sharewith_inp.value));
			return;
		}
		pf_submit_step2();
	}, 5);
}
function pf_submit_step2() {
	if( !d.pf ) { return; }
	if( !pf_submit_in_progress ) { return; } 
	d.pf.onsubmit	= function() { return true; };
	d.pf.submit();
}
var pf_attachbox_opened	= false;
var pf_attach_in_progress	= false;
var pf_atchimage_tab	= "upl";
function pf_attachbox_open(which) {
	if( pf_attachbox_opened ) { return; }
	if( pf_submit_in_progress ) { return; }
	pf_attachbox_opened	= which;
	var ovr	= d.getElementById("blackoverlay2");
	ovr.onclick	= pf_attachbox_close;
	var size_overlay2	= function() {
		ovr.style.height	= (parseInt(d.body.clientHeight,10)-41)+"px";
	};
	size_overlay2();
	d.body.removeEventListener("orientationchange", size_overlay2, false);
	d.body.addEventListener("orientationchange", size_overlay2, false);
	d.getElementById("pf_attach_link").style.display		= "none";
	d.getElementById("pf_attach_link_on").style.display		= "none";
	d.getElementById("pf_attach_image").style.display		= "none";
	d.getElementById("pf_attach_image_on").style.display		= "none";
	d.getElementById("pf_attach_file").style.display		= "none";
	d.getElementById("pf_attach_file_on").style.display		= "none";
	d.getElementById("pf_attach_videoembed").style.display	= "none";
	d.getElementById("pf_attach_videoembed_on").style.display	= "none";
	d.getElementById("pf_attachbox_container").style.display	= "block";
	if( d.pf["attached_"+which].value != "1" ) {
		d.getElementById("pf_attach_"+which).style.display		= "block";
		if( which == "image" ) {
			d.getElementById("pf_attach_image_upl").style.display	= "none";
			d.getElementById("pf_attach_image_url").style.display	= "none";
			d.getElementById("pf_attach_image_"+pf_atchimage_tab).style.display	= "block";
			d.getElementById("pf_attach_image_lnk_upl").className	= "";
			d.getElementById("pf_attach_image_lnk_url").className	= "";
			d.getElementById("pf_attach_image_lnk_"+pf_atchimage_tab).className	= "onattab";
			d.getElementById("atchinp_image_"+pf_atchimage_tab).value	= "";
			d.getElementById("atchinp_image_"+pf_atchimage_tab).disabled	= false;
			d.getElementById("atchinp_image_"+pf_atchimage_tab).focus();
		}
		else {
			d.getElementById("atchinp_"+which).value	= "";
			d.getElementById("atchinp_"+which).disabled	= false;
			d.getElementById("atchinp_"+which).focus();
		}
	}
	else {
		d.getElementById("atchinp_"+which+"_on").innerHTML	= d.pf["attached_"+which].getAttribute("atchtext");
		d.getElementById("pf_attach_"+which+"_on").style.display		= "block";
	}
	obj_class_add( d.getElementById("attbtn_"+which), "pressed" );
	if( which == "file" ) {
		obj_class_add( d.getElementById("attbtns"), "lastpressed" );
	}
}
function pf_attachbox_submit() {
	if( !pf_attachbox_opened ) { return; }
	if( pf_attach_in_progress ) { return; }
	if( pf_attachbox_opened=="link" || pf_attachbox_opened=="videoembed" ) {
		var inp	= d.getElementById("atchinp_"+pf_attachbox_opened);
		inp.value	= trim(inp.value);
		if( inp.value == "" ) {
			inp.focus();
			return;
		}
		inp.disabled	= true;
		var f_error	= function() {
			pf_attach_in_progress	= false;
			inp.disabled	= false;
			inp.focus();
			d.getElementById("postattacher").className	= "";
		};
		var req = ajax_init(true);
		if( ! req ) { f_error(); return; }
		req.onreadystatechange	= function() {
			if( req.readyState != 4  ) { return; }
			if( ! req.responseXML ) { f_error(); return; }
			var data	= req.responseXML.getElementsByTagName("result");
			if( !data || !data[0] ) { f_error(); return; }
			data	= data[0];
			var status	= data.getElementsByTagName("status");
			if( !status || !status[0] ) { f_error(); return; }
			status	= status[0].firstChild.nodeValue;
			if( status != "OK" ) {
				var message	= data.getElementsByTagName("message");
				if( message && message[0] ) {
					message	= message[0].firstChild.nodeValue;
					if( message ) {
						alert(message);
					}
				}
				f_error();
				return;
			}
			data	= data.getElementsByTagName("attach");
			if( !data || !data[0] ) { f_error(); return; }
			data	= data[0].getAttribute("text");
			if( !data ) { f_error(); return; }
			d.pf["attached_"+pf_attachbox_opened].value	= "1";
			d.pf["attached_"+pf_attachbox_opened].setAttribute("atchtext", data);
			pf_attach_in_progress	= false;
			obj_class_add( d.getElementById("attbtn_"+pf_attachbox_opened), "full" );
			pf_attachbox_close();
			d.getElementById("postattacher").className	= "";
			return;
		}
		req.open("POST", siteurl+"ajax/postform-attach/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.send("post_temp_id="+encodeURIComponent(d.pf.post_temp_id.value)+"&at_type="+pf_attachbox_opened+"&data="+encodeURIComponent(inp.value));
		pf_attach_in_progress	= true;
		d.getElementById("postattacher").className	= "postattach_loading";
		return;
	}
	else if( pf_attachbox_opened=="file" ) {
		var inp	= d.getElementById("atchinp_file");
		if( inp.value == "" ) {
			inp.focus();
			return;
		}
		var f_error	= function() {
			pf_attach_in_progress	= false;
			inp.disabled	= false;
			inp.focus();
			d.getElementById("postattacher").className	= "";
		};
		var frmkey	= pf_submit_hidden_uplform( d.getElementById("atchinp_file") );
		inp.disabled	= true;
		var f_check	= function() {
			var req = ajax_init(true);
			if( ! req ) { return; }
			req.onreadystatechange	= function() {
				if( req.readyState != 4  ) { return; }
				if( ! req.responseXML ) { f_error(); return; }
				var data	= req.responseXML.getElementsByTagName("result");
				if( !data || !data[0] ) { f_error(); return; }
				data	= data[0];
				var status	= data.getElementsByTagName("status");
				if( !status || !status[0] ) { f_error(); return; }
				status	= status[0].firstChild.nodeValue;
				if( status == "WAIT" ) {
					setTimeout(f_check, 1000);
					return;
				}
				if( status != "OK" ) {
					var message	= data.getElementsByTagName("message");
					if( message && message[0] ) {
						alert(message[0].firstChild.nodeValue);
					}
					f_error();
					return;
				}
				data	= data.getElementsByTagName("attach");
				if( !data || !data[0] ) { f_error(); return; }
				data	= data[0].getAttribute("text");
				if( !data ) { f_error(); return; }
				d.pf["attached_file"].value	= "1";
				d.pf["attached_file"].setAttribute("atchtext", data);
				pf_attach_in_progress	= false;
				obj_class_add( d.getElementById("attbtn_file"), "full" );
				pf_attachbox_close();
				d.getElementById("postattacher").className	= "";
				return;
			}
			req.open("POST", siteurl+"ajax/postform-attach/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("post_temp_id="+encodeURIComponent(d.pf.post_temp_id.value)+"&at_type=file&data="+encodeURIComponent(frmkey));
		};
		setTimeout(f_check, 1);
		pf_attach_in_progress	= true;
		d.getElementById("postattacher").className	= "postattach_loading";
		return;
	}
	else if( pf_attachbox_opened=="image" ) {
		var inp	= d.getElementById("atchinp_image_"+pf_atchimage_tab);
		if( inp.value == "" ) {
			inp.focus();
			return;
		}
		var f_error	= function() {
			pf_attach_in_progress	= false;
			inp.disabled	= false;
			inp.focus();
			d.getElementById("postattacher").className	= "";
		};
		var frmkey;
		if(pf_atchimage_tab == "upl") {
			frmkey	= pf_submit_hidden_uplform(inp);
		}
		inp.disabled	= true;
		var f_check	= function() {
			var req = ajax_init(true);
			if( ! req ) { f_error(); return; }
			req.onreadystatechange	= function() {
				if( req.readyState != 4  ) { return; }
				if( ! req.responseXML ) { f_error(); return; }
				var data	= req.responseXML.getElementsByTagName("result");
				if( !data || !data[0] ) { f_error(); return; }
				data	= data[0];
				var status	= data.getElementsByTagName("status");
				if( !status || !status[0] ) { f_error(); return; }
				status	= status[0].firstChild.nodeValue;
				if( pf_atchimage_tab=="upl" && status=="WAIT" ) {
					setTimeout(f_check, 1000);
					return;
				}
				if( status != "OK" ) {
					var message	= data.getElementsByTagName("message");
					if( message && message[0] ) {
						alert(message[0].firstChild.nodeValue);
					}
					f_error();
					return;
				}
				data	= data.getElementsByTagName("attach");
				if( !data || !data[0] ) { f_error(); return; }
				data	= data[0].getAttribute("text");
				if( !data ) { f_error(); return; }
				d.pf["attached_image"].value	= "1";
				d.pf["attached_image"].setAttribute("atchtext", data);
				pf_attach_in_progress	= false;
				obj_class_add( d.getElementById("attbtn_image"), "full" );
				pf_attachbox_close();
				d.getElementById("postattacher").className	= "";
				return;
			}
			var dt	= pf_atchimage_tab=="upl" ? ("upl|"+frmkey) : ("url|"+inp.value);
			req.open("POST", siteurl+"ajax/postform-attach/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("post_temp_id="+encodeURIComponent(d.pf.post_temp_id.value)+"&at_type=image&data="+encodeURIComponent(dt));
		}
		setTimeout(f_check, 1);
		pf_attach_in_progress	= true;
		d.getElementById("postattacher").className	= "postattach_loading";
		return;
	}
}
var pf_attachdel_confirm	= "";
function pf_attachbox_delete() {
	if( ! pf_attachbox_opened ) { return; }
	if( d.pf["attached_"+pf_attachbox_opened].value != "1" ) { return; }
	if( ! confirm(pf_attachdel_confirm) ) { return; }
	d.pf["attached_"+pf_attachbox_opened].value	= 0;
	d.pf["attached_"+pf_attachbox_opened].setAttribute("atchtext", "");
	d.getElementById("pf_attach_"+pf_attachbox_opened+"_on").style.display		= "none";
	d.getElementById("pf_attach_"+pf_attachbox_opened).style.display		= "block";
	if( pf_attachbox_opened == "image" ) {
		d.getElementById("atchinp_image_"+pf_atchimage_tab).value	= "";
		d.getElementById("atchinp_image_"+pf_atchimage_tab).disabled	= false;
		d.getElementById("atchinp_image_"+pf_atchimage_tab).focus();
	}
	else {
		d.getElementById("atchinp_"+pf_attachbox_opened).value	= "";
		d.getElementById("atchinp_"+pf_attachbox_opened).disabled	= false;
		d.getElementById("atchinp_"+pf_attachbox_opened).focus();
	}
	obj_class_del( d.getElementById("attbtn_"+pf_attachbox_opened), "full" );
}
function pf_attachbox_imgtab(tb) {
	if( pf_attachbox_opened != "image" ) { return; }
	if( pf_attach_in_progress ) { return; }
	pf_atchimage_tab	= tb;
	d.getElementById("pf_attach_image_upl").style.display	= "none";
	d.getElementById("pf_attach_image_url").style.display	= "none";
	d.getElementById("pf_attach_image_"+tb).style.display	= "block";
	d.getElementById("pf_attach_image_lnk_upl").className	= "";
	d.getElementById("pf_attach_image_lnk_url").className	= "";
	d.getElementById("pf_attach_image_lnk_"+tb).className	= "onattab";
}
function pf_attachbox_close() {
	if( pf_attach_in_progress ) { return false; }
	obj_class_del( d.getElementById("attbtn_"+pf_attachbox_opened), "pressed" );
	if( pf_attachbox_opened == "file" ) {
		obj_class_del( d.getElementById("attbtns"), "lastpressed" );
	}
	pf_attachbox_opened	= false;
	d.getElementById("pf_attachbox_container").style.display	= "none";
}

function pf_validate(area) {
	if( area ) {
		var v	= area.value;
		if( v.length > pf_msg_max_length ) {
			area.value	= v.substr(0, pf_msg_max_length);
		}
	}
	setTimeout( function() { pf_validate(area); }, 194 );
}
function pf_validate_advanced(area) {
	if( area ) {
		var v	= area.value;
		var n	= false;
		while( v.indexOf("\n")!=-1 || v.indexOf("\r")!=-1 ) {
			v	= v.replace(/\r\n|\n|\r/, " ");
			n	= true;
		}
		if( n ) {
			while( v.indexOf("  ")!=-1 ) {
				v	= v.replace("  ", " ");
			}
		}
		if( v.length > pf_msg_max_length ) {
			v	= v.substr(0, pf_msg_max_length);
			n	= true;
		}
		if( n ) {
			area.value	= v;
		}
	}
	setTimeout( function() { pf_validate_advanced(area); }, 1281 );
}

function pf_submit_hidden_uplform(fileinput) {
	var ifr	= d.createElement("IFRAME");
	ifr.name	= pf_generate_tmpid(10);
	ifr.id	= ifr.name;
	ifr.style.display	= "none";
	d.body.appendChild(ifr);
	try { w.frames[ifr.name].name	= ifr.name } catch(e) {}
	var frm	= d.createElement("FORM");
	frm.method	= "POST";
	frm.action	= siteurl+"ajax/postform-attachupl";
	frm.enctype	= "multipart/form-data";
	frm.encoding	= "multipart/form-data";
	frm.target	= ifr.name;
	frm.name	= pf_generate_tmpid(10);
	frm.style.display	= "none";
	var inp1	= d.createElement("INPUT");
	inp1.type	= "hidden";
	inp1.name	= "keyy";
	inp1.value	= pf_generate_tmpid(10);
	frm.appendChild(inp1);
	var inp2	= fileinput.cloneNode(true);
	inp2.name	= "file";
	inp2.disabled	= false;
	if(inp2.value == fileinput.value) {
		frm.appendChild(inp2);
		d.body.appendChild(frm);
		frm.submit();
	}
	else {
		var container	= fileinput.parentNode;
		var oldname		= fileinput.name;
		var fileinput2	= fileinput;
		fileinput2.name	= "file";
		fileinput2.disabled	= false;
		frm.appendChild(fileinput2);
		d.body.appendChild(frm);
		frm.submit();
		fileinput.name = oldname;
		fileinput.disabled	= true;
		container.appendChild(fileinput);
	}
	return inp1.value;
}
function pf_generate_tmpid(len, let) {
	if( !len ) { len = 10; }
	if( !let ) { let = "abcdefghijklmnopqrstuvwxyz0123456789"; }
	var i, word = "";
	for(i=0; i<len; i++) {
		word	+= let.charAt(Math.round(Math.random()*(let.length-1)));
	}
	return word;
}


function obj_class_add(obj, cl) {
	if( !obj ) { return false; }
	if( !obj.className ) { obj.className = ""; }
	var tmp	= obj.className.split(" ");
	if(cl in tmp) {
		return true;
	}
	tmp[tmp.length]	= cl;
	obj.className	= trim(tmp.join(" "));
}
function obj_class_del(obj, cl) {
	if( !obj ) { return false; }
	if( !obj.className ) { obj.className = ""; }
	var tmp	= obj.className.split(" ");
	for(var i=0; i<tmp.length; i++) {
		if(tmp[i]==cl || tmp[i]==="") {
			delete tmp[i];
		}
	}
	obj.className	= trim(tmp.join(" "));
}


function footer_simpleversion_link(txt)
{
	var tmp	= d.createElement("SPAN");
	var tmp2	= d.createElement("B");
	var tmp3	= d.createTextNode("· ");
	tmp2.appendChild(tmp3);
	tmp.appendChild(tmp2);
	var tmp2	= d.createElement("A");
	tmp2.href	= "javascript:;";
	tmp2.appendChild(d.createTextNode(txt));
	tmp2.onclick	= function() {
		var tmp	= w.location.href.toString();
		var tmp1	= tmp.replace(/http(s)?\:\/\//, "");
		tmp1	= tmp1.replace(/\/.*$/, "");
		d.cookie	= "mobitouch=0;path=/;domain=."+tmp1;
		tmp	= tmp.replace(/\#.*$/, "");
		w.location.href	= tmp;
	};
	tmp.appendChild(tmp2);
	d.getElementById("ftr").appendChild(tmp);
}
