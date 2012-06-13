var d = document;
var w = window;

var jserr_add_admin_invalid_user	= "";
var jserr_add_admin_not_member	= "";
var jsconfirm_admin_remove		= "";
var js_group_membercheck	= true;
var js_ipaddresscheck		= false;
var js_group_members	= "";
function group_admins_putintolist(username) {
	if( !d.admform || !d.admform.admins ) {
		return false;
	}
	if( d.admform.admins.value.toLowerCase().indexOf(","+username.toLowerCase()+",") != -1 ) {
		return true;
	}
	var dv	= d.createElement("DIV");
	dv.className	= "addadmins";
	dv.appendChild(d.createTextNode(username+" "));
	var a	= d.createElement("A");
	a.href	= "javascript:;";
	a.onfocus	= function() { this.blur(); };
	a.onclick	= function() {
		var msg	= jsconfirm_admin_remove.replace("#USERNAME#", username);
		if( msg != "" ) {
			if( ! confirm(msg) ) { return false; }
		}
		d.admform.admins.value	= d.admform.admins.value.replace(","+username+",", ",");
		d.admform.admins.value	= d.admform.admins.value.replace(",,", ",");
		if( d.admform.admins.value=="" || d.admform.admins.value=="," ) {
			if( d.getElementById("group_admins_link_empty_msg") ) {
				d.getElementById("group_admins_link_empty_msg").style.display = "block";
			}
		}
		this.parentNode.parentNode.removeChild(this.parentNode);
	};
	dv.appendChild(a);
	d.getElementById("group_admins_list").appendChild(dv);
	if( d.getElementById("group_admins_link_empty_msg") ) {
		d.getElementById("group_admins_link_empty_msg").style.display	= "none";
	}
	d.admform.admins.value	+= ","+username+",";
	d.admform.admins.value	= d.admform.admins.value.replace(",,", ",");
}
function group_admins_add()
{
	if( !d.admform || !d.admform.admins ) {
		return false;
	}
	var inp	= d.getElementById("addadmin_inp");
	var btn	= d.getElementById("addadmin_btn");
	if( !inp || !btn ) {
		return false;
	}
	if( inp.disabled || btn.disabled ) {
		return false;
	}
	if( input_hide_autocompletes ) {
		input_hide_autocompletes();
	}
	btn.blur();
	inp.value	= trim(inp.value);
	if( inp.value == "" ) {
		inp.focus();
		return false;
	}
	if( d.admform.admins.value.toLowerCase().indexOf(","+inp.value.toLowerCase()+",") != -1 ) {
		inp.value	= "";
		inp.focus();
		return true;
	}
	if( js_ipaddresscheck ) {
		var iperr	= false;
		if( ! inp.value.match(/^[0-9]{1,3}\.[0-9]{1,3}\.([0-9]{1,3}\.([0-9]{1,3})?)?$/) ) {
			iperr	= true;
		}
		else {
			var ipnums	= inp.value.split(".");
			if( parseInt(ipnums[0])>255 || parseInt(ipnums[1])>255 ) {
				iperr	= true;
			}
			else if( ipnums[2]!==undefined && ipnums[2]=="" && ipnums[3]!==undefined ) {
				iperr	= true;
			}
			else if( ipnums[2]!==undefined && ipnums[2]!="" && parseInt(ipnums[2])>255 ) {
				iperr	= true;
			}
			else if( ipnums[3]!==undefined && ipnums[3]!="" && parseInt(ipnums[3])>255 ) {
				iperr	= true;
			}
		}
		if( iperr ) {
			var msg	= jserr_add_admin_invalid_user;
			if( msg != "" ) {
				alert(msg);
			}
			return false;
		}
		group_admins_putintolist( inp.value );
		inp.disabled	= false;
		btn.disabled	= false;
		inp.style.cursor	= "";
		btn.style.cursor	= "";
		inp.value	= "";
		inp.focus();
		return true;
	}
	var req = ajax_init(true);
	req.onreadystatechange	= function() {
		if( req.readyState != 4  ) { return; }
		if( ! req.responseXML ) { return; }
		var data	= req.responseXML.getElementsByTagName("result");
		if( !data || !data[0] || !data[0].firstChild ) {
			var msg	= jserr_add_admin_invalid_user.replace("#USERNAME#", inp.value);
			if( msg != "" ) {
				alert( msg );
			}
		}
		else if( js_group_membercheck && js_group_members.toLowerCase().indexOf(","+inp.value.toLowerCase()+",") == -1 ) {
			var msg	= jserr_add_admin_not_member.replace("#USERNAME#", inp.value);
			if( msg != "" ) {
				alert( msg );
			}
		}
		else {
			group_admins_putintolist( data[0].firstChild.nodeValue );
		}
		inp.disabled	= false;
		btn.disabled	= false;
		inp.style.cursor	= "";
		btn.style.cursor	= "";
		inp.value	= "";
		inp.focus();
	}
	req.open("POST", siteurl+"ajax/checkname/ajaxtp:xml/r:"+Math.round(Math.random()*1000), true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send("datatype=username&word="+encodeURIComponent(inp.value));	
	inp.disabled	= true;
	btn.disabled	= true;
	inp.style.cursor	= "wait";
	btn.style.cursor	= "wait";
}

networdaddr_examples_open	= [];
function networdaddr_example(w)
{
	if( networdaddr_examples_open[w] ) {
		d.getElementById("lnk_"+w).style.fontWeight	= "normal";
		d.getElementById("box_"+w).style.display	= "none";
		networdaddr_examples_open[w]	= false;
		return;
	}
	d.getElementById("lnk_"+w).style.fontWeight	= "bold";
	d.getElementById("box_"+w).style.display	= "block";
	networdaddr_examples_open[w]	= true;
}