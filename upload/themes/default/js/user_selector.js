var d = document;
var w = window;

var UserSelector	= function()
{
	this.form_input	= false;
	this.container	= false;
	this.avatars_url	= "";
	this.texts	= ({taball:"",tabsel:"",searchinp:""});
	this.data	= [];
	this.onload	= function() { };
	this.seltab	= "all";
	this.selnum	= 0;
	this.perpg	= 45;
	this.pgback	= "« back";
	this.pgnext	= "next »";
};

UserSelector.prototype.init	= function()
{
	if( !this.form_input || !this.container || this.data.length==0 ) {
		this.onload();
		return false;
	}
	var obj	= this;
	var div1	= d.createElement("DIV");
	div1.className	= "filterreguserlist";
	var inp1	= d.createElement("INPUT");
	inp1.id	= "userselector_searchinp";
	inp1.type	= "text";
	inp1.value	= this.texts.searchinp;
	inp1.onkeyup	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e ) { return; }
		var code = e.charCode ? e.charCode : e.keyCode;
		if( code==27 ) {
			this.value	= obj.texts.searchinp;
			obj.search("");
			this.blur();
			return false;
		}
		obj.search(this.value);
	};
	inp1.onfocus	= function() {
		this.value	= obj.trim(this.value);
		if( this.value == obj.texts.searchinp ) {
			this.value	= "";
		}
	};
	inp1.onblur	= function() {
		this.value	= obj.trim(this.value);
		if( this.value == "" ) {
			this.value	= obj.texts.searchinp;
		}
	};
	if( w.postform_forbid_hotkeys_conflicts !== undefined ) {
		postform_forbid_hotkeys_conflicts(inp1);
	}
	div1.appendChild(inp1);
	var lnk1	= d.createElement("A");
	var lnk2	= d.createElement("A");
	lnk1.id	= "userselectortab_all";
	lnk2.id	= "userselectortab_sel";
	lnk1.href	= "javascript:;";
	lnk2.href	= "javascript:;";
	lnk1.onclick	= function() { obj.tab("all"); this.blur(); };
	lnk2.onclick	= function() { obj.tab("sel"); this.blur(); };
	var lnkb1	= d.createElement("B");
	var lnkb2	= d.createElement("B");
	lnkb1.appendChild(d.createTextNode(this.texts.taball));
	lnkb2.appendChild(d.createTextNode(this.texts.tabsel+" ("));
	var lnkb2s	= d.createElement("SPAN");
	lnkb2s.id	= "userselectortab_sel_num";
	lnkb2s.appendChild(d.createTextNode("0"));
	lnkb2.appendChild(lnkb2s);
	lnkb2.appendChild(d.createTextNode(")"));
	lnk1.appendChild(lnkb1);
	lnk2.appendChild(lnkb2);
	div1.appendChild(lnk2);
	div1.appendChild(lnk1);
	this.container.appendChild(div1);
	var div2	= d.createElement("DIV");
	div2.className	= "users";
	var div3	= d.createElement("DIV");
	div3.className	= "theusers";
	div3.id	= "userselector_cnt";
	div2.appendChild(div3);
	this.container.appendChild(div2);
	var i, c;
	for(i=0; i<this.data.length; i++) {
		c	= this.data[i];
		if( c[5] == 1 ) {
			this.form_input.value	= this.form_input.value + ","+c[0]+",";
			this.selnum	++;
		}
		c[10]	= c[1]+", "+c[2]+", "+c[3];
		c[10]	= c[10].toLowerCase();
		c[11]	= false;
	}
	d.getElementById("userselectortab_sel_num").innerHTML	= this.selnum;
	setTimeout( function() { 
		obj.tab(obj.seltab);
		obj.onload();
	}, 1);
	return true;
};
UserSelector.prototype.initdiv	= function(indx)
{
	var c	= this.data[indx];
	if( ! c ) { return; }
	if( c[11] ) { return; }
	var obj	= this;
	var dv	= d.createElement("DIV");
	dv.id	= "uselector_cnt_"+c[0];
	dv.className	= "selectableuser";
	if( c[5] == 1 ) {
		dv.className	= "selectableuser slctd";
	}
	dv.setAttribute("usel", c[5]==1 ? "1" : "0");
	dv.setAttribute("uindx", indx);
	dv.onclick	= function() {
		var i = this.getAttribute("uindx");
		var c = obj.data[i];
		if( c[5] == 0 ) {
			c[5]	= 1;
			obj.form_input.value	= obj.form_input.value + ","+c[0]+",";
			this.className	= "selectableuser slctd";
			d.getElementById("uselector_chk_"+c[0]).checked = true;
			obj.selnum ++;
			d.getElementById("userselectortab_sel_num").innerHTML	= Math.max(obj.selnum, 0);
		}
		else {
			c[5]	= 0;
			obj.form_input.value	= obj.form_input.value.replace(","+c[0]+",", "");
			this.className	= "selectableuser";
			d.getElementById("uselector_chk_"+c[0]).checked = false;
			if( obj.seltab == "sel" ) {
				this.style.visibility	= "hidden";
				var sdf = this;
				setTimeout( function() { sdf.style.display = "none"; sdf.style.visibility = "visible"; }, 200 );
			}
			obj.selnum --;
			d.getElementById("userselectortab_sel_num").innerHTML	= Math.max(obj.selnum, 0);
		}
	};
	dv.style.display	= "block";
	var inp	= d.createElement("INPUT");
	inp.id	= "uselector_chk_"+c[0];
	inp.type	= "checkbox";
	inp.checked	= c[5]==1 ? true : false;
	inp.onfocus	= function() { this.blur(); }
	dv.appendChild(inp);
	var img	= d.createElement("IMG");
	img.src	= this.avatars_url+c[4];
	dv.appendChild(img);
	var dvv	= d.createElement("DIV");
	dvv.className	= "selectableuserside";
	var b	= d.createElement("B");
	b.id	= "uselector_unm_"+c[0];
	b.setAttribute("utxt", c[1]);
	b.innerHTML	= c[1];
	dvv.appendChild(b);
	var s	= d.createElement("STRONG");
	s.id	= "uselector_fnm_"+c[0];
	s.setAttribute("utxt", c[2]);
	s.innerHTML	= c[2];
	dvv.appendChild(s);
	dv.appendChild(dvv);
	c[11]	= dv;
};

UserSelector.prototype.tab	= function(tb)
{
	d.getElementById("userselectortab_all").className	= "";
	d.getElementById("userselectortab_sel").className	= "";
	d.getElementById("userselectortab_"+tb).className	= "slctd";
	d.getElementById("userselector_searchinp").value	= this.texts.searchinp;
	this.seltab	= tb;
	this.search("");
};
UserSelector.prototype.search	= function(txt)
{
	txt	= this.trim(txt);
	txt	= txt.toLowerCase();
	if( txt == this.texts.searchinp ) {
		txt	= "";
	}
	if( txt.length < 2 ) {
		txt	= "";
	}
	var usrs	= [];
	var i, j, c, dv, tmp, str, pos;
	for(i=0; i<this.data.length; i++) {
		c	= this.data[i];
		if( this.seltab=="sel" && c[5]==0 ) {
			continue;
		}
		if( txt == "" ) {
			if( c[11] ) {
				dv	= c[11];
				tmp	= dv.childNodes[2].childNodes[0];
				tmp.innerHTML	= tmp.getAttribute("utxt");
				tmp	= dv.childNodes[2].childNodes[1];
				tmp.innerHTML	= tmp.getAttribute("utxt");
			}
			usrs[usrs.length]	= i;
			continue;
		}
		if( c[10].indexOf(txt) != -1 ) {
			if( ! c[11] ) {
				this.initdiv(i);
			}
			dv	= c[11];
			tmp	= dv.childNodes[2].childNodes[0];
			str	= tmp.getAttribute("utxt");
			pos	= str.toLowerCase().indexOf(txt);
			if( pos != -1 ) {
				str	= str.substr(0,pos) + "<span>" + str.substr(pos,txt.length) + "</span>" + str.substr(pos+txt.length);
			}
			tmp.innerHTML	= str;
			tmp	= dv.childNodes[2].childNodes[1];
			str	= tmp.getAttribute("utxt");
			pos	= str.toLowerCase().indexOf(txt);
			if( pos != -1 ) {
				str	= str.substr(0,pos) + "<span>" + str.substr(pos,txt.length) + "</span>" + str.substr(pos+txt.length);
			}
			tmp.innerHTML	= str;
			usrs[usrs.length]	= i;
		}
	}
	var obj	= this;
	var cnt	= d.getElementById("userselector_cnt");
	var tmpfunc	= function(start_from) {
		while(cnt.firstChild) {
			cnt.removeChild(cnt.firstChild);
		}
		if( usrs.length == 0 ) {
			return;
		}
		if( ! start_from ) {
			start_from	= 0;
		}
		if( usrs[start_from] === undefined ) {
			return;
		}
		var repl	= d.createElement("DIV");
		for(i=start_from, j=0; i<usrs.length; i++) {
			if( ! obj.data[usrs[i]][11]  ) {
				obj.initdiv(i);
			}
			repl.appendChild( obj.data[usrs[i]][11] );
			if( ++j == obj.perpg ) {
				break;
			}
		}
		if( j < usrs.length ) {
			var a;
			a	= d.createElement("DIV");
			a.className	= "klear";
			repl.appendChild(a);
			if( start_from > 0 ) {
				a	= d.createElement("A");
				a.href	= "javascript:;";
				a.onclick	= function() {
					tmpfunc( Math.max(0, start_from-obj.perpg-1) );
				};
				a.innerHTML	= obj.pgback;
				repl.appendChild(a);
			}
			if( start_from + obj.perpg < usrs.length ) {
				if( start_from > 0 ) {
					a	= d.createElement("SPAN");
					a.innerHTML	= "&nbsp;&middot;&nbsp;";
					a.style.color	= "#888";
					repl.appendChild(a);
				}
				a	= d.createElement("A");
				a.href	= "javascript:;";
				a.onclick	= function() {
					tmpfunc( Math.min(start_from+obj.perpg+1, usrs.length-1) );
				};
				a.innerHTML	= obj.pgnext;
				repl.appendChild(a);
			}
		}
		cnt.parentNode.scrollTop	= 0;
		setTimeout( function() { cnt.appendChild(repl); }, 1 );
	};
	setTimeout( function() { tmpfunc(0); }, 1 );
};

UserSelector.prototype.trim	= function(txt)
{
	if( typeof(txt) != "string" ) { return txt; }
	txt	= txt.replace(/^\s+/, "");
	txt	= txt.replace(/\s+$/, "");
	return txt;
};