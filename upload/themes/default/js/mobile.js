var d	= document;
var w	= window;


var msglen_max	= 160;
var msglen_counter_prefix	= "";
var msglen_counter_suffix	= "";

var msglen_tmout	= false;
function postform_validate()
{
	if( msglen_tmout ) {
		clearTimeout(msglen_tmout);
	}
	var t	= d.postform.message;
	var v	= t.value;
	var c	= d.getElementById("post_msglen");
	if( !t || !c ) {
		return;
	}
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
	if( v.charAt(0) == " " ) {
		v	= v.replace(/^\s+/, "");
		n	= true;
	}
	if( v.length > msglen_max ) {
		v	= v.substr(0, msglen_max);
		n	= true;
	}
	if( n && t.value != v ) {
		t.value	= v;
	}
	var r	= msglen_max - t.value.length;
	if( r < 0 ) {
		r	= 0;
	}
	c.innerHTML	= msglen_counter_prefix + r + msglen_counter_suffix;
	if( msglen_tmout ) {
		clearTimeout(msglen_tmout);
	}
	msglen_tmout	= setTimeout( postform_validate, 500 );
}



function footer_touchversion_link(txt)
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
		d.cookie	= "mobitouch=1;path=/;domain=."+tmp1;
		tmp	= tmp.replace(/\#.*$/, "");
		w.location.href	= tmp;
	};
	tmp.appendChild(tmp2);
	d.getElementById("ftrtext").appendChild(tmp);
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