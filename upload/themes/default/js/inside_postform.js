var d = document;
var w = window;

var pf_autoopen		= false;

var pf_hotkeyopen_loadgroup	= false;
var pf_hotkeyopen_loaduser	= false;

var pf_hotkeys_enabled	= false;
if( d.addEventListener ) {
	d.addEventListener("load", postform_set_hotkeys, false);
	w.addEventListener("load", postform_set_hotkeys, false);
}
else if( d.attachEvent ) {
	d.attachEvent("onload", postform_set_hotkeys);
	w.attachEvent("onload", postform_set_hotkeys);
}
function postform_set_hotkeys() {
	if( pf_hotkeys_enabled ) {
		return;
	}
	pf_hotkeys_enabled	= true;
	if( pf_autoopen ) {
		postform_open();
	}
	var f_onkeypress	= function(e) {
		if( !e && _w.event ) { e = _w.event; }
		if( !e ) { return; }
		var code = e.charCode ? e.charCode : e.keyCode;
		if( pf_hotkeys_enabled && (code==112 || code==1087) && !e.ctrlKey && !e.altKey && pf_open_state==0 && !flybox_opened ) {
			if( pf_hotkeyopen_loadgroup ) {
				postform_open( ({groupname:pf_hotkeyopen_loadgroup}) );
			}
			else if( pf_hotkeyopen_loaduser ) {
				postform_open( ({username:pf_hotkeyopen_loaduser}) );
			}
			else {
				postform_open();
			}
			if( e.preventDefault ) { e.preventDefault(); } else { e.returnValue = false; }
		}
		if( flybox_opened && code==27 ) {
			flybox_close();
		}
	};
	var i, all=d.getElementsByTagName("INPUT");
	for(i=0; i<all.length; i++) {
		postform_forbid_hotkeys_conflicts(all[i]);
	}
	all = d.getElementsByTagName("TEXTAREA");
	for(i=0; i<all.length; i++) {
		postform_forbid_hotkeys_conflicts(all[i]);
	}
	if( d.addEventListener ) {
		d.addEventListener("keypress", f_onkeypress, false);
	}
	else if( d.attachEvent ) {
		d.attachEvent("onkeypress", f_onkeypress);
	}
	if( d.post_form && d.post_form.message ) {
		var f_onkpr	= function(e) {
			if( !e && _w.event ) { e = _w.event; }
			if( !e ) { return; }
			var code = e.charCode ? e.charCode : e.keyCode;
			if( (code==10 || code==13) && e.ctrlKey ) {
				d.post_form.message.blur();
				postform_submit();
				if( e.preventDefault ) { e.preventDefault(); } else { e.returnValue = false; }
			}
		};
		if( d.post_form.message.addEventListener ) {
			d.post_form.message.addEventListener("keypress", f_onkpr, false);
		}
		else if( d.post_form.message.attachEvent ) {
			d.post_form.message.attachEvent("onkeypress", f_onkpr);
		}
	}
}
function postform_forbid_hotkeys_conflicts(inp)
{
	if( inp.addEventListener ) {
		inp.addEventListener("focus", function(){pf_hotkeys_enabled=false;}, false);
		inp.addEventListener("blur", function(){pf_hotkeys_enabled=true;}, false);
	}
	else if( inp.attachEvent ) {
		inp.attachEvent("onfocus", function(){pf_hotkeys_enabled=false;});
		inp.attachEvent("onblur", function(){pf_hotkeys_enabled=true;});
	}
}

var pf_open_state		= 0;	// 0 -> closed, 1 => open, 2 => in progress
var pf_post_state		= 0;	// 0 -> none, 1 => posted, 2 => loading, 3 => error
var pf_attach_state	= 0;	// 0 -> closed, 1 => open, 2 => in progress
var pf_attach_state_tp	= "";	// if open or in progress -> which type
var pf_msg_max_length	= 160;
var pf_changes		= 0;	// if there were any changes to form fields
var pf_close_confirm	= "";	// confirm when closing form
var pf_rmatch_confirm	= "";	// confirm when removing attachments
var pf_data	= {};

function postform_open(load_data)
{
	if( pf_open_state == 1 ) {
		if( pf_attach_state == 2 ) {
			return;
		}
		var open_another	= false;
		if( load_data && load_data.username && (pf_data.share_with_type!="user" || pf_data.share_with_xtra!=load_data.username) ) {
			open_another	= true;
		}
		else if( load_data && load_data.groupname && (pf_data.share_with_type!="group" || pf_data.share_with_xtra!=load_data.groupname) ) {
			open_another	= true;
		}
		else if( load_data && load_data.editpost && load_data.editpost!=pf_data.existing_post_id ) {
			open_another	= true;
		}
		else if( (!load_data || !(load_data.username||load_data.groupname||load_data.editpost)) && pf_data.share_with_type!="all" ) {
			open_another	= true;
		}
		if( open_another ) {
			postform_close_withconfirm( function(){ postform_open(load_data); } );
		}
		else {
			d.post_form.message.focus();
		}
		scroll(0,0);
		return;
	}
	if( pf_open_state != 0 ) {
		return;
	}
	var pf_container	= d.getElementById("postform");
	if( ! pf_container ) {
		return false;
	}
	scroll(0,0);
	if( pf_post_state == 0 ) {
		postform_open_step2(load_data);
		return true;
	}
	if( pf_post_state == 1  ) {
		postform_statusmsg_clearTimeout();
		pf_post_state	= 0;
		postform_htmlobject_hide("pf_postedok", function() { postform_open_step2(load_data); });
		return true;
	}
	return false;
}
function postform_open_step2(load_data)
{
	d.post_form.reset();
	d.post_form.message.disabled	= false;
	pf_data	= ({
		temp_id:	postform_generate_tmpid(10),
		existing_post_id:	"",		// "" -> new post, type_id -> editing post
		share_with_type:	"all",	// "all" or "user" or "group" or empty if editing post
		share_with_xtra:	"",		// if user or group -> id here
		message:		"",		// message
		attachments:	0,		// count
		at_link:		[],		// attached link
		at_image:		[],		// attached image
		at_videoembed:	[],		// embeded video
		at_file:		[],		// attached file
		atchimgtab:		"upl"		// tab: upl or url
	});
	if( load_data && load_data.username ) {
		pf_data.share_with_type	= "user";
		pf_data.share_with_xtra	= load_data.username;
	}
	else if( load_data && load_data.groupname ) {
		pf_data.share_with_type	= "group";
		pf_data.share_with_xtra	= load_data.groupname;
	}
	else if( load_data && load_data.editpost ) {
		pf_data.existing_post_id	= load_data.editpost;
		pf_data.share_with_type	= "";
		if( postcomments_open_state ) {
			var state	= postcomments_open_state[load_data.editpost];
			if( state == 2 ) {
				return;
			}
		}
	}
	else {
		var btn	= d.getElementById("postform_open_button");
		if(btn) {
			btn.style.display	= "none";
		}
	}
	if( load_data && load_data.mention ) {
		pf_data.message	= "@"+load_data.mention+" ";
		d.post_form.message.value	= pf_data.message;
	}
	postform_attachimage_tab(pf_data.atchimgtab);
	pf_changes	= 0;
	d.getElementById("attachbtn_link").style.display	= "block";
	d.getElementById("attachbtn_image").style.display	= "block";
	d.getElementById("attachbtn_videoembed").style.display	= "block";
	d.getElementById("attachbtn_file").style.display	= "block";
	d.getElementById("attachok_link").style.display	= "none";
	d.getElementById("attachok_image").style.display	= "none";
	d.getElementById("attachok_videoembed").style.display	= "none";
	d.getElementById("attachok_file").style.display	= "none";
	if( pf_data.existing_post_id == "" ) {
		d.getElementById("pf_title_newpost").style.display	= "block";
		d.getElementById("pf_title_edtpost").style.display	= "none";
		d.getElementById("sharewith_user").style.display	= "none";
		d.getElementById("sharewith_group").style.display	= "none";
		d.getElementById("sharewith").style.display	= "block";
		d.getElementById("selectedupdateoption").style.display	= "block";
		if( pf_data.share_with_type == "all" ) {
			var span	= d.getElementById("selectedupdateoption").firstChild;
			span.innerHTML	= span.getAttribute("defaultvalue");
		}
		else {
			d.getElementById("selectedupdateoption").firstChild.innerHTML = postform_str_cut(pf_data.share_with_xtra,30);
		}
		d.getElementById("postbtn_newpost").style.display	= "block";
		d.getElementById("postbtn_edtpost").style.display	= "none";
	}
	else {
		d.getElementById("pf_title_newpost").style.display	= "none";
		d.getElementById("pf_title_edtpost").style.display	= "block";
		d.getElementById("sharewith_user").style.display	= "none";
		d.getElementById("sharewith_group").style.display	= "none";
		d.getElementById("sharewith").style.display	= "none";
		d.getElementById("selectedupdateoption").style.display	= "block";
		d.getElementById("postbtn_newpost").style.display	= "none";
		d.getElementById("postbtn_edtpost").style.display	= "block";
		pf_post_state	= 2;
		postform_htmlobject_show("pf_loading", 36, postform_open_step2_loadpost);
		return;
	}
	postform_open_step3();
}
function postform_open_step2_loadpost()
{
	var tmp_load_post_err	= function() {
		pf_post_state	= 0;
		postform_htmlobject_hide("pf_loading");
		pf_data	= {};
	};
	var req = ajax_init(true);
	if( ! req ) {
		return tmp_load_post_err();
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseXML ) {
			return tmp_load_post_err();
		}
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] ) {
			return tmp_load_post_err();
		}
		data	= data[0].getElementsByTagName("post");
		if( !data || !data[0] ) {
			return tmp_load_post_err();
		}
		data	= data[0];
		var postid	= data.getAttribute("id");
		var message	= data.getAttribute("message");
		if( !postid || !message || postid!=pf_data.existing_post_id ) {
			return tmp_load_post_err();
		}
		pf_data.message	= message;
		d.post_form.message.value	= message;
		data	= data.getElementsByTagName("attach");
		var i, tp, id, txt;
		for(i=0; i<data.length; i++) {
			tp	= data[i].getAttribute("type");
			id	= data[i].getAttribute("id");
			txt	= data[i].getAttribute("text");
			if( !tp || !id || !txt ) {
				continue;
			}
			pf_data["at_"+tp]	= [id, txt];
			pf_data.attachments	++;
			d.getElementById("attachbtn_"+tp).style.display	= "none";
			d.getElementById("attachok_"+tp).style.display	= "block";
			d.getElementById("attachok_"+tp+"_txt").innerHTML	= txt;
		}
		postform_open_step3();
	}
	req.open("POST", siteurl+"ajax/postform-loadpost/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("postid="+encodeURIComponent(pf_data.existing_post_id));
}
function postform_open_step3()
{
	pf_open_state	= 2;
	if( pf_post_state == 2 ) {
		pf_post_state	= 0;
		postform_htmlobject_hide("pf_loading");
	}
	d.getElementById("postform").style.display	= "block";
	postform_htmlobject_show("pf_mainpart", 114, postform_open_step4);
	if( msgbox_close ) {
		msgbox_close();
	}
}
function postform_open_step4()
{
	pf_open_state	= 1;
	d.post_form.message.focus();
	postform_validate(d.post_form.message);
	postform_validate_advanced(d.post_form.message);
}

function postform_attachbox_open(at_type, h)
{
	if( pf_open_state != 1 ) {
		return;
	}
	if( pf_attach_state == 1 ) {
		if( pf_attach_state_tp == at_type ) {
			return;
		}
		postform_attachbox_close( function() { postform_attachbox_open_step2(at_type, h); } );
		return;
	}
	if( pf_attach_state == 2 ) {
		return;
	}
	postform_attachbox_open_step2(at_type, h);
}
function postform_attachbox_open_step2(at_type, h)
{
	d.getElementById("attachboxcontent_link").style.display	= "none";
	d.getElementById("attachboxcontent_image").style.display	= "none";
	d.getElementById("attachboxcontent_videoembed").style.display	= "none";
	d.getElementById("attachboxcontent_file").style.display	= "none";
	d.getElementById("attachboxcontent_"+at_type).style.display	= "block";
	d.getElementById("attachboxcontent_link_ftr").className	= "submitattachment";
	d.getElementById("attachboxcontent_image_ftr").className	= "submitattachment";
	d.getElementById("attachboxcontent_videoembed_ftr").className	= "submitattachment";
	d.getElementById("attachboxcontent_file_ftr").className	= "submitattachment";
	d.post_form.atch_link.disabled	= false;
	d.post_form.atch_image_upl.disabled	= false;
	d.post_form.atch_image_url.disabled	= false;
	d.post_form.atch_file.disabled	= false;
	d.post_form.atch_videoembed.disabled	= false;
	if( at_type == "image" ) {
		var ttldiv	= d.getElementById("attachboxtitle_image_url");
		ttldiv.innerHTML	= ttldiv.getAttribute("defaultvalue");
		ttldiv	= d.getElementById("attachboxtitle_image_upl");
		ttldiv.innerHTML	= ttldiv.getAttribute("defaultvalue");
	}
	else {
		var ttldiv	= d.getElementById("attachboxtitle_"+at_type);
		ttldiv.innerHTML	= ttldiv.getAttribute("defaultvalue");
	}
	pf_attach_state_tp	= at_type;
	pf_attach_state	= 2;
	postform_htmlobject_show("attachbox", h, function() { postform_attachbox_open_step3(at_type); });
}
function postform_attachbox_open_step3(at_type)
{
	pf_attach_state	= 1;
	d.getElementById("attachbtn_"+at_type).className = "attachbtn pressed";
	d.getElementById("attachbox").className="a_"+at_type;
	switch(at_type) {
		case "link":
			d.post_form.atch_link.focus();
			break;
		case "videoembed":
			d.post_form.atch_videoembed.focus();
			break;
		case "file":
			d.post_form.atch_file.focus();
			break;
		case "image":
			d.post_form["atch_image_"+pf_data.atchimgtab].focus();
			break;
	}
}

function postform_close(callback_after)
{
	if( pf_open_state != 1 ) {
		return;
	}
	if( pf_attach_state == 2 ) {
		return;
	}
	if( pf_attach_state == 1 ) {
		postform_attachbox_close();
	}
	d.post_form.message.blur();
	pf_open_state	= 2;
	if( pf_post_state == 3  ) {
		postform_htmlobject_hide("pf_postederror");
		pf_post_state	= 0;
	}
	var f	= function() {
		pf_open_state	= 0;
		if( pf_post_state == 0 ) {
			d.getElementById("postform").style.display	= "none";
		}
		var btn	= d.getElementById("postform_open_button");
		if(btn) {
			btn.style.display	= "";
		}
		if(callback_after) {
			callback_after();
		}
	};
	postform_htmlobject_hide("pf_mainpart", f);
}
function postform_close_withconfirm(callback_after)
{
	if( pf_open_state != 1 ) {
		return;
	}
	if( pf_attach_state == 2 ) {
		return;
	}
	if( pf_data.message != d.post_form.message.value ) {
		pf_changes	++;
	}
	if( pf_changes == 0 ) {
		postform_close(callback_after);
		return;
	}
	if( ! confirm(pf_close_confirm) ) {
		return;
	}
	postform_close(callback_after);
}

function postform_attachbox_close(callback_after)
{
	if( pf_attach_state != 1 ) {
		return;
	}
	d.getElementById("attachbox").className	= "";
	d.getElementById("attachbtn_link").className	= "attachbtn";
	d.getElementById("attachbtn_image").className	= "attachbtn";
	d.getElementById("attachbtn_videoembed").className	= "attachbtn";
	d.getElementById("attachbtn_file").className	= "attachbtn";
	pf_attach_state	= 2;
	postform_htmlobject_hide("attachbox", function() { pf_attach_state = 0; if(callback_after) { callback_after(); } });
}

function postform_sharewith_finduser()
{
	pf_data.share_with_type	= "user";
	pf_data.share_with_xtra	= "";
	d.post_form.username.value	= "";
	d.getElementById("updateoptions").style.display	= "none";
	d.getElementById("sharewith_group").style.display	= "none";
	d.getElementById("selectedupdateoption").style.display	= "none";
	d.getElementById("sharewith_user").style.display	= "block";
	d.post_form.username.focus();
}
function postform_sharewith_findgroup()
{
	pf_data.share_with_type	= "group";
	pf_data.share_with_xtra	= "";
	d.post_form.groupname.value	= "";
	d.getElementById("updateoptions").style.display	= "none";
	d.getElementById("sharewith_user").style.display	= "none";
	d.getElementById("selectedupdateoption").style.display	= "none";
	d.getElementById("sharewith_group").style.display	= "block";
	d.post_form.groupname.focus();
}
function postform_sharewith_user(txt)
{
	pf_data.share_with_type	= "user";
	pf_data.share_with_xtra	= txt;
	d.getElementById("updateoptions").style.display	= "none";
	d.getElementById("selectedupdateoption").firstChild.innerHTML	= txt;
	d.getElementById("sharewith_user").style.display	= "none";
	d.getElementById("sharewith_group").style.display	= "none";
	d.getElementById("selectedupdateoption").style.display	= "block";
	d.post_form.message.focus();
}
function postform_sharewith_group(txt)
{
	pf_data.share_with_type	= "group";
	pf_data.share_with_xtra	= txt;
	d.getElementById("updateoptions").style.display	= "none";
	d.getElementById("selectedupdateoption").firstChild.innerHTML	= txt;
	d.getElementById("sharewith_user").style.display	= "none";
	d.getElementById("sharewith_group").style.display	= "none";
	d.getElementById("selectedupdateoption").style.display	= "block";
	d.post_form.message.focus();
}
function postform_sharewith_all(txt)
{
	pf_data.share_with_type	= "all";
	pf_data.share_with_xtra	= "";
	d.getElementById("updateoptions").style.display	= "none";
	d.getElementById("selectedupdateoption").firstChild.innerHTML	= txt;
	d.getElementById("sharewith_user").style.display	= "none";
	d.getElementById("sharewith_group").style.display	= "none";
	d.getElementById("selectedupdateoption").style.display	= "block";
	d.post_form.message.focus();
}

function postform_bgcheck_username()
{
	var word	= d.post_form.username.value;
	var req = ajax_init(true);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseXML ) { return; }
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] ) { return; }
		data	= data[0].firstChild;
		if( !data ) { return; }
		postform_sharewith_user(data.nodeValue);
	}
	req.open("POST", siteurl+"ajax/checkname/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("datatype=username&word="+encodeURIComponent(word));
}
function postform_bgcheck_groupname()
{
	var word	= d.post_form.groupname.value;
	var req = ajax_init(true);
	if( ! req ) { return; }
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseXML ) { return; }
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] ) { return; }
		data	= data[0].firstChild;
		if( !data ) { return; }
		postform_sharewith_group(data.nodeValue);
	}
	req.open("POST", siteurl+"ajax/checkname/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("datatype=groupname&word="+encodeURIComponent(word));
}

function postform_attach_submit(callback_if_ok)
{
	if( pf_open_state!=1 || pf_attach_state!=1 ) {
		return;
	}
	if( pf_attach_state_tp == "image" ) {
		if( d.post_form["atch_image_"+pf_data.atchimgtab].value == "" ) {
			d.post_form["atch_image_"+pf_data.atchimgtab].focus();
			return;
		}
		d.post_form["atch_image_"+pf_data.atchimgtab].disabled	= true;
	}
	else {
		if( d.post_form["atch_"+pf_attach_state_tp].value == "" ) {
			d.post_form["atch_"+pf_attach_state_tp].focus();
			return;
		}
		d.post_form["atch_"+pf_attach_state_tp].disabled	= true;
	}
	d.getElementById("attachboxcontent_"+pf_attach_state_tp+"_ftr").className	= "submitattachment loading";
	pf_attach_state	= 2;
	pf_changes	++;
	var f_error	= function(errmsg) {
		pf_attach_state	= 1;
		if( pf_attach_state_tp == "image" ) {
			d.post_form["atch_image_"+pf_data.atchimgtab].disabled	= false;
			d.getElementById("attachboxcontent_image_ftr").className = "submitattachment";
			var dv	= d.getElementById("attachboxtitle_image_"+pf_data.atchimgtab);
			dv.innerHTML	= errmsg ? errmsg: dv.getAttribute("defaultvalue");
			d.post_form["atch_image_"+pf_data.atchimgtab].focus();
		}
		else {
			d.post_form["atch_"+pf_attach_state_tp].disabled	= false;
			d.getElementById("attachboxcontent_"+pf_attach_state_tp+"_ftr").className = "submitattachment";
			var dv	= d.getElementById("attachboxtitle_"+pf_attach_state_tp);
			dv.innerHTML	= errmsg ? errmsg: dv.getAttribute("defaultvalue");
			d.post_form["atch_"+pf_attach_state_tp].focus();
		}
	};
	var f_ok	= function(attxt) {
		if( ! pf_data["at_"+pf_attach_state_tp][0] ) {
			pf_data.attachments	++;
		}
		pf_data["at_"+pf_attach_state_tp]	= [-1, attxt];
		d.getElementById("attachbtn_"+pf_attach_state_tp).style.display	= "none";
		d.getElementById("attachok_"+pf_attach_state_tp).style.display	= "block";
		d.getElementById("attachok_"+pf_attach_state_tp+"_txt").innerHTML	= attxt;
		var tmpf	= function() {
			pf_attach_state	= 0;
			if( pf_attach_state_tp == "image" ) {
				d.post_form["atch_image_"+pf_data.atchimgtab].value	= "";
			}
			else {
				d.post_form["atch_"+pf_attach_state_tp].value	= "";
			}
			d.getElementById("attachbtn_"+pf_attach_state_tp).className	= "attachbtn";
			if( callback_if_ok ) {
				callback_if_ok();
			}
		};
		postform_htmlobject_hide("attachbox", tmpf);
		d.post_form.message.focus();
	};
	switch( pf_attach_state_tp ) {
		case "link":
		case "videoembed":
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
						f_error(message[0].firstChild.nodeValue); return;
					}
					f_error();
					return;
				}
				data	= data.getElementsByTagName("attach");
				if( !data || !data[0] ) { f_error(); return; }
				data	= data[0].getAttribute("text");
				if( !data ) { f_error(); return; }
				f_ok(data);
			}
			req.open("POST", siteurl+"ajax/postform-attach/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			req.send("post_temp_id="+encodeURIComponent(pf_data.temp_id)+"&at_type="+pf_attach_state_tp+"&data="+encodeURIComponent(d.post_form["atch_"+pf_attach_state_tp].value));
			break;
		case "file":
			var frmkey	= postform_submit_hidden_uplform(d.post_form.atch_file);
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
							f_error(message[0].firstChild.nodeValue); return;
						}
						f_error();
						return;
					}
					data	= data.getElementsByTagName("attach");
					if( !data || !data[0] ) { f_error(); return; }
					data	= data[0].getAttribute("text");
					if( !data ) { f_error(); return; }
					f_ok(data);
				}
				req.open("POST", siteurl+"ajax/postform-attach/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
				req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				req.send("post_temp_id="+encodeURIComponent(pf_data.temp_id)+"&at_type=file&data="+encodeURIComponent(frmkey));
			};
			setTimeout(f_check, 1);
			break;
		case "image":
			var frmkey;
			if(pf_data.atchimgtab == "upl") {
				frmkey	= postform_submit_hidden_uplform(d.post_form.atch_image_upl);
			}
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
					if( pf_data.atchimgtab=="upl" && status=="WAIT" ) {
						setTimeout(f_check, 1000);
						return;
					}
					if( status != "OK" ) {
						var message	= data.getElementsByTagName("message");
						if( message && message[0] ) {
							f_error(message[0].firstChild.nodeValue); return;
						}
						f_error();
						return;
					}
					data	= data.getElementsByTagName("attach");
					if( !data || !data[0] ) { f_error(); return; }
					data	= data[0].getAttribute("text");
					if( !data ) { f_error(); return; }
					f_ok(data);
				}
				var dt	= pf_data.atchimgtab=="upl" ? ("upl|"+frmkey) : ("url|"+d.post_form.atch_image_url.value);
				req.open("POST", siteurl+"ajax/postform-attach/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
				req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				req.send("post_temp_id="+encodeURIComponent(pf_data.temp_id)+"&at_type=image&data="+encodeURIComponent(dt));
			}
			setTimeout(f_check, 1);
			break;
		default:
			return;
	}
}

function postform_attach_remove(at_type)
{
	if( pf_open_state!=1 || pf_data.attachments==0 || !pf_data["at_"+at_type][0] ) {
		return;
	}
	if( ! confirm(pf_rmatch_confirm) ) {
		return;
	}
	pf_data.attachments	--;
	pf_data["at_"+at_type]	= [];
	if( at_type == "image" ) {
		d.post_form["atch_image_"+pf_data.atchimgtab].value	= "";
	}
	else {
		d.post_form["atch_"+at_type].value	= "";
	}
	d.getElementById("attachok_"+at_type).style.display	= "none";
	d.getElementById("attachbtn_"+at_type).style.display	= "block";
	pf_changes	++;
}

function postform_attachimage_tab(tb)
{
	if( pf_attach_state == 2 ) {
		return;
	}
	var ttldiv	= d.getElementById("attachboxtitle_image_url");
	ttldiv.innerHTML	= ttldiv.getAttribute("defaultvalue");
	ttldiv	= d.getElementById("attachboxtitle_image_upl");
	ttldiv.innerHTML	= ttldiv.getAttribute("defaultvalue");
	d.getElementById("attachform_img_upl_btn").className	= "";
	d.getElementById("attachform_img_url_btn").className	= "";
	d.getElementById("attachform_img_"+tb+"_btn").className	= "onlitetab";
	d.getElementById("attachform_img_upl_div").style.display	= "none";
	d.getElementById("attachform_img_url_div").style.display	= "none";
	d.getElementById("attachform_img_"+tb+"_div").style.display	= "block";
	pf_data.atchimgtab	= tb;
	try{ d.post_form["atch_image_"+tb].focus(); } catch(e) {};
}

function postform_submit()
{
	if( pf_open_state!=1 || pf_attach_state==2 ) {
		return;
	}
	if( pf_data.message != d.post_form.message.value ) {
		pf_data.message	= d.post_form.message.value;
		pf_changes	++;
	}
	if( pf_data.message == "" ) {
		d.post_form.message.focus();
		return;
	}
	if( pf_attach_state == 1 ) {
		postform_attach_submit(  function() { if(pf_data.message==d.post_form.message.value) { postform_submit(); } }  );
		return;
	}
	d.post_form.message.disabled	= true;
	pf_open_state	= 2;
	if( pf_post_state == 1 ) {
		postform_statusmsg_clearTimeout();
		pf_post_state	= 2;
		postform_htmlobject_hide("pf_postedok", postform_submit_step2);
		return;
	}
	else if( pf_post_state == 3 ) {
		pf_post_state	= 2;
		postform_htmlobject_hide("pf_postederror", postform_submit_step2);
		return;
	}
	pf_post_state	= 2;
	postform_submit_step2();
}
function postform_submit_step2()
{
	postform_htmlobject_show("pf_posting", 36, postform_submit_step3);
}
function postform_submit_step3()
{
	postform_htmlobject_hide("pf_mainpart", postform_submit_step4);
}

function postform_submit_step4()
{
	var req = ajax_init(true);
	if( ! req ) { return; }
	var p	= "post_temp_id="+encodeURIComponent(pf_data.temp_id)+"&message="+encodeURIComponent(pf_data.message);
	if( pf_data.existing_post_id != "" ) {
		p	+= "&editpost="+encodeURIComponent(pf_data.existing_post_id);
	}
	else if( pf_data.share_with_type == "user" ) {
		p	+= "&username="+encodeURIComponent(pf_data.share_with_xtra);
	}
	else if( pf_data.share_with_type == "group" ) {
		p	+= "&groupname="+encodeURIComponent(pf_data.share_with_xtra);
	}
	if( pf_data.at_link[0] ) {
		p	+= "&at_link="+encodeURIComponent(pf_data.at_link[0]);
	}
	if( pf_data.at_image[0] ) {
		p	+= "&at_image="+encodeURIComponent(pf_data.at_image[0]);
	}
	if( pf_data.at_file[0] ) {
		p	+= "&at_file="+encodeURIComponent(pf_data.at_file[0]);
	}
	if( pf_data.at_videoembed[0] ) {
		p	+= "&at_videoembed="+encodeURIComponent(pf_data.at_videoembed[0]);
	}
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseXML ) { return; }
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] ) { return; }
		data	= data[0];
		var status	= data.getElementsByTagName("status");
		var message	= data.getElementsByTagName("message");
		if( !status || !status[0] || !message || !message[0] ) {
			return;
		}
		status	= status[0].firstChild.nodeValue;
		message	= message[0].firstChild.nodeValue;
		if( status != "OK" ) {
			d.getElementById("pf_postederror_msg").innerHTML	= message;
			postform_htmlobject_hide("pf_posting");
			postform_htmlobject_show("pf_postederror", 36);
			postform_htmlobject_show("pf_mainpart", 114, function() { pf_open_state=1; pf_post_state=3; d.post_form.message.disabled=false; d.post_form.message.focus(); });
			return;
		}
		d.getElementById("pf_postedok_msg").innerHTML	= message;
		postform_htmlobject_hide("pf_posting");
		postform_htmlobject_show("pf_postedok", 36, function() { pf_open_state=0; pf_post_state=1; postform_statusmsg_setTimeout(); });
		var btn	= d.getElementById("postform_open_button");
		if(btn) {
			btn.style.display	= "";
		}
		if( posts_synchronize ) {
			posts_synchronize();
		}
		var pinf	= pf_data.existing_post_id;
		if( pinf != "" ) {
			pinf	= pinf.split("_");
			var tmp	= w.location.href.toString();
			tmp	= tmp.replace(/^http(s)?\:\/\//, "");
			tmp	= tmp.substr(tmp.indexOf("/"));
			var mtch	= "/view/"+(pinf[0]=="public"?"post":"priv")+":"+pinf[1];
			if( tmp.substr(0,mtch.length)==mtch ) {
				if( viewpost_synchronize ) {
					viewpost_synchronize();
				}
				else {
					w.location.href	= w.location.href.toString();
				}
			}
		}
	}
	req.open("POST", siteurl+"ajax/postform-submit/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send(p);
}

function postform_topmsg_close()
{
	if( pf_post_state == 1 ) {
		postform_statusmsg_clearTimeout();
		pf_post_state	= 0;
		postform_htmlobject_hide("pf_postedok", function() { if( pf_open_state == 0 ) { d.getElementById("postform").style.display = "none"; } });
	}
	else if( pf_post_state == 3 ) {
		pf_post_state	= 0;
		postform_htmlobject_hide("pf_postederror", function() { if( pf_open_state == 0 ) { d.getElementById("postform").style.display = "none"; } });
	}
}

function postform_validate(area)
{
	if( pf_open_state==1 && area ) {
		var v	= area.value;
		if( v.length > pf_msg_max_length ) {
			area.value	= v.substr(0, pf_msg_max_length);
		}
		d.getElementById("pf_chars_counter").innerHTML = (pf_msg_max_length - area.value.length);
	}
	setTimeout( function() { postform_validate(area); }, 289 );
}
function postform_validate_advanced(area)
{
	if( pf_open_state==1 && area ) {
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
	setTimeout( function() { postform_validate_advanced(area); }, 1981 );
}

var pf_htmlobjects_shown	= {};

function postform_htmlobject_show(div_id, orig_height, callback_after, steps, delay)
{
	if( pf_htmlobjects_shown[div_id] == undefined ) {
		pf_htmlobjects_shown[div_id]	= 0;
	}
	if( pf_htmlobjects_shown[div_id] != 0 ) {
		return true;
	}
	var div	= _d.getElementById(div_id);
	if( !div ) {
		return false;
	}
	if( disable_animations ) {
		div.style.display		= "block";
		div.style.overflow	= "visible";
		pf_htmlobjects_shown[div_id]	= 1;
		if( callback_after ) {
			callback_after();
		}
		return true;
	}
	if( ! steps ) { steps = 7; }
	if( ! delay ) { delay = 20; }
	var i	= 0;
	var func	= function() {
		i	++;
		if( i == steps ) {
			div.style.height		= orig_height + "px";
			div.style.opacity		= 1;
			div.style.overflow	= "visible";
			pf_htmlobjects_shown[div_id]	= 1;
			if( callback_after ) {
				callback_after();
			}
			return true;
		}
		if( ! isNaN(orig_height) ) {
			setTimeout( function() { div.style.height = Math.round(i*orig_height/steps)+"px"; }, 1  );
		}
		setTimeout( function() { div.style.opacity = 0.5+(i/2)/steps; }, 1  );
		setTimeout( func, delay );
	};
	div.style.overflow	= "hidden";
	div.style.height		= "0px";
	div.style.display		= "block";
	div.style.opacity		= 0.5;
	pf_htmlobjects_shown[div_id]	= 2;
	func();	
}

function postform_htmlobject_hide(div_id, callback_after, steps, delay)
{
	if( pf_htmlobjects_shown[div_id] != 1 ) {
		return true;
	}
	var div	= _d.getElementById(div_id);
	if( !div ) {
		return false;
	}
	if( disable_animations ) {
		div.style.display		= "none";
		pf_htmlobjects_shown[div_id]	= 0;
		if( callback_after ) {
			callback_after();
		}
		return true;
	}
	if( ! steps ) { steps = 7; }
	if( ! delay ) { delay = 20; }
	var orig_height	= parseInt(div.style.height, 10);
	var i	= steps;
	var func	= function() {
		i	--;
		if( i == 0 ) {
			div.style.height		= "0px";
			div.style.opacity		= 0.5;
			div.style.display		= "none";
			div.style.height	= "auto";
			pf_htmlobjects_shown[div_id]	= 0;
			if( callback_after ) {
				callback_after();
			}
			return true;
		}
		if( ! isNaN(orig_height) ) {
			setTimeout( function() { div.style.height = Math.round(i*orig_height/steps)+"px"; }, 1  );
		}
		setTimeout( function() { div.style.opacity = 0.5+(i/2)/steps; }, 1  );
		setTimeout( func, delay );
	};
	div.style.overflow	= "hidden";
	div.style.opacity		= 1;
	pf_htmlobjects_shown[div_id]	= 2;
	func();	
}

function postform_generate_tmpid(len, let)
{
	if( !len ) { len = 10; }
	if( !let ) { let = "abcdefghijklmnopqrstuvwxyz0123456789"; }
	var i, word = "";
	for(i=0; i<len; i++) {
		word	+= let.charAt(Math.round(Math.random()*(let.length-1)));
	}
	return word;
}

function postform_str_cut(str, len)
{
	if( str.length <= len ) {
		return str;
	}
	return str.substr(0, len-1)+"..";
}

function postform_is_valid_url(url)
{
	if( ! url.match(/^(ftp|http|https)\:\/\//) ) {
		url	= "http://" + url;
	}
	if( ! url.match(/^(ftp|http|https)\:\/\/((([a-z0-9.-]+\.)+[a-z]{2,4})|([0-9\.]{1,4}){4})(\/([a-zа-я0-9-_\—\:%\.\?\!\=\+\&\/\#\~\;\,\@]+)?)?$/i) ) {
		return false;
	}
	return true;
}

function postform_attach_pastelink(event, input, callback_if_ok)
{
	return false;
	
	if( !event && _w.event ) {
		event	= _w.event;
	}
	if( !event || !event.type ) {
		return false;
	}
	if( event.type == "paste" ) {
		setTimeout( function() {
			if( postform_is_valid_url(input.value) ) {
				callback_if_ok();
			}			
		}, 1 );
	}
	else if( event.type == "keyup" ) {
		var code = event.charCode ? event.charCode : event.keyCode;
		if( event.ctrlKey && code==86 && !event.altKey && !event.shiftKey ) {
			setTimeout( function() {
				if( postform_is_valid_url(input.value) ) {
					callback_if_ok();
				}			
			}, 1 );
		}
		if( event.shiftKey && code==45 && !event.altKey && !event.ctrlKey ) {
			setTimeout( function() {
				if( postform_is_valid_url(input.value) ) {
					callback_if_ok();
				}			
			}, 1 );
		}
	}
}

function postform_submit_hidden_uplform(fileinput)
{
	var ifr	= d.createElement("IFRAME");
	ifr.name	= postform_generate_tmpid(10);
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
	frm.name	= postform_generate_tmpid(10);
	frm.style.display	= "none";
	var inp1	= d.createElement("INPUT");
	inp1.type	= "hidden";
	inp1.name	= "keyy";
	inp1.value	= postform_generate_tmpid(10);
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

var postform_statusmsg_timeout	= false;
function postform_statusmsg_setTimeout()
{
	if( postform_statusmsg_timeout ) {
		return;
	}
	postform_statusmsg_timeout	= setTimeout( function() { postform_htmlobject_hide("pf_postedok"); }, 3000 );
}
function postform_statusmsg_clearTimeout()
{
	if( postform_statusmsg_timeout ) {
		clearTimeout( postform_statusmsg_timeout );
		postform_statusmsg_timeout	= false;
	}
}

function postform_mention(username, always_open)
{
	if( pf_open_state == 2 ) {
		return;
	}
	if( pf_open_state == 0 ) {
		postform_open( ({mention:username}) );
		return;
	}
	if( always_open ) {
		postform_open( ({mention:username}) );
		return;
	}
	var v	= d.post_form.message.value + " @" + username;
	v	= v.replace("  ", " ");
	v	= trim(v);
	if( v.length > pf_msg_max_length ) {
		d.post_form.message.focus();
		return;
	}
	v	+= " ";
	d.post_form.message.value	= v;
	d.post_form.message.focus();
}
