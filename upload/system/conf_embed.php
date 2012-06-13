<?php

	global $C;
	
	$C->NEWPOST_EMBEDVIDEO_SOURCES	= array
	(
		'youtube'	=> (object) array
		(
			'src_url_pattern'		=> '/^http(s)?\:\/\/(www\.|de\.)?youtube\.com\/watch\?v\=([a-z0-9-\_]{3,})/i',
			'src_url_matchnum'	=> 3,
			'src_emb_pattern'		=> '/http(s)?\:\/\/(www\.)?youtube\.com\/v\/([a-z0-9-\_]{3,})/i',
			'src_emb_matchnum'	=> 3,
			'embed_w'		=> 425,
			'embed_h'		=> 355,
			'embed_code'	=> '<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/###ID###&hl=en&rel=0&color1=0x006699&color2=0x54abd6" /><param name="wmode" value="opaque" /><embed src="http://www.youtube.com/v/###ID###&hl=en&rel=0&color1=0x006699&color2=0x54abd6" type="application/x-shockwave-flash" width="425" height="355" wmode="opaque"></embed></object>',
			'embed_thumb'	=> 'http://i.ytimg.com/vi/###ID###/default.jpg',
			'insite_url'	=> 'http://youtube.com/watch?v=###ID###',
		),
		'facebook'	=> (object) array
		(
			'src_url_pattern'		=>  '/^http(s)?\:\/\/(www\.)?facebook\.com\/video\/video\.php\?v=([0-9]{1,})$/i',
			'src_url_matchnum'	=> 3,
			'src_emb_pattern'		=> '/http(s)?\:\/\/(www\.)?facebook\.com\/v\/([0-9]{1,})/i',
			'src_emb_matchnum'	=> 3,
			'embed_w'		=> 640,
			'embed_h'		=> 480,
			'embed_code'	=> '<object width="640" height="480"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.facebook.com/v/###ID###" /><embed src="http://www.facebook.com/v/###ID###" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="480" wmode="opaque"></embed></object>',
			'embed_thumb'	=> '',
			'insite_url'	=> 'http://www.facebook.com/video/video.php?v=###ID###',
		),
		'metacafe'	=> (object) array
		(
			'src_url_pattern'		=> '/^http(s)?\:\/\/(www\.)?metacafe\.com\/watch\/([a-z0-9-]{3,})/i',
			'src_url_matchnum'	=> 3,
			'src_emb_pattern'		=> '/http(s)?\:\/\/(www\.)?metacafe\.com\/fplayer\/([a-z0-9-]{3,})/i',
			'src_emb_matchnum'	=> 3,
			'embed_w'		=> 400,
			'embed_h'		=> 345,
			'embed_code'	=> '<embed src="http://www.metacafe.com/fplayer/###ID###/.swf" width="400" height="345" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="opaque"></embed>',
			'embed_thumb'	=> 'http://s2.mcstatic.com/thumb/###ID###/0/0/directors_cut/0/0/0.jpg',
			'insite_url'	=> 'http://metacafe.com/watch/###ID###',
		),
		'myspace'	=> (object) array
		(
			'src_url_pattern'		=> '/^http(s)?\:\/\/(www\.)?(myspacetv\.com|myspace\.tv|vids\.myspace\.com)\/index\.cfm\?fuseaction\=vids\.individual\&videoid\=([a-z0-9-]{3,})/i',
			'src_url_matchnum'	=> 4,
			'src_emb_pattern'		=> '/flashvars\=\"m\=([a-z0-9-]{3,})/i',
			'src_emb_matchnum'	=> 1,
			'embed_w'		=> 430,
			'embed_h'		=> 346,
			'embed_code'	=> '<embed src="http://lads.myspace.com/videos/vplayer.swf" flashvars="m=###ID###&v=2&type=video" type="application/x-shockwave-flash" width="430" height="346" wmode="opaque"></embed>',
			'embed_thumb'	=> '',
			'insite_url'	=> 'http://vids.myspace.com/index.cfm?fuseaction=vids.individual&VideoID=###ID###',
		),
		'revver'	=> (object) array
		(
			'src_url_pattern'		=> '/^http(s)?\:\/\/(www\.)?(revver\.com)\/video\/([a-z0-9-]{3,})/i',
			'src_url_matchnum'	=> 4,
			'src_emb_pattern'		=> '/mediaid(\=|\:)([a-z0-9-]{3,})/i',
			'src_emb_matchnum'	=> 2,
			'embed_w'		=> 480,
			'embed_h'		=> 392,
			'embed_code'	=> '<object width="480" height="392" data="http://flash.revver.com/player/1.0/player.swf?mediaId=###ID###" type="application/x-shockwave-flash"><param name="Movie" value="http://flash.revver.com/player/1.0/player.swf?mediaId=###ID###"></param><param name="FlashVars" value="allowFullScreen=false"></param><param name="AllowFullScreen" value="false"></param><param name="AllowScriptAccess" value="never"></param><embed type="application/x-shockwave-flash" src="http://flash.revver.com/player/1.0/player.swf?mediaId=872962" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="never" flashvars="allowFullScreen=false" allowfullscreen="false" height="392" width="480"></embed></object>',
			'embed_thumb'	=> 'http://frame.revver.com/frame/120x90/###ID###.jpg',
			'insite_url'	=> 'http://revver.com/video/###ID###',
		),
		'vimeo'	=> (object) array 
		(
			'src_url_pattern'		=> '/^http(s)?\:\/\/(www\.)?(vimeo\.com)\/([a-z0-9-]{3,})/i',
			'src_url_matchnum'	=> 4,
			'src_emb_pattern'		=> '/clip_id\=([a-z0-9-]{3,})/i',
			'src_emb_matchnum'	=> 1,
			'embed_w'		=> 400,
			'embed_h'		=> 225,
			'embed_code'	=> '<object width="400" height="225"><param name="allowfullscreen" value="false" /><param name="allowscriptaccess" value="never" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=###ID###&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=0" /><embed src="http://www.vimeo.com/moogaloop.swf?clip_id=###ID###&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=0" type="application/x-shockwave-flash" allowfullscreen="false" allowscriptaccess="never" width="400" height="225"></embed></object>',
			'embed_thumb'	=> '',
			'insite_url'	=> 'http://vimeo.com/###ID###',
		),
		'rutube'	=> (object) array
		(
			'src_url_pattern'		=> '/^http?\:\/\/rutube\.ru\/tracks\/([0-9]{1,}).html\?v=([0-9a-z]{1,})$/i',
			'src_url_matchnum'	=> 2,
			'src_emb_pattern'		=> '/http?\:\/\/video\.rutube\.ru\/([0-9a-z]{1,})/i',
			'src_emb_matchnum'	=> 1,
			'embed_w'		=> 470,
			'embed_h'		=> 353,
			'embed_code'	=> '<OBJECT width="470" height="353"><PARAM name="movie" value="http://video.rutube.ru/###ID###"></PARAM><PARAM name="wmode" value="window"></PARAM><PARAM name="allowFullScreen" value="true"></PARAM><EMBED src="http://video.rutube.ru/###ID###" type="application/x-shockwave-flash" wmode="window" width="470" height="353" allowFullScreen="true" ></EMBED></OBJECT>',
			'embed_thumb'	=> 'http://img.rutube.ru/thumbs/###ID###-2.jpg',
			'insite_url'	=> 'http://video.rutube.ru/###ID###',
		),
		'vbox7'	=> (object) array
		(
			'src_url_pattern'		=> '/^http(s)?\:\/\/(www\.)?(vbox7\.com|zazz\.bg)\/play\:([a-z0-9-]{3,})/i',
			'src_url_matchnum'	=> 4,
			'src_emb_pattern'		=> '/http(s)?\:\/\/i47\.vbox7\.com\/player\/ext\.swf\?vid\=([a-z0-9-]{3,})/i',
			'src_emb_matchnum'	=> 2,
			'embed_w'		=> 450,
			'embed_h'		=> 403,
			'embed_code'	=> '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="450" height="403"><param name="movie" value="http://i47.vbox7.com/player/ext.swf?vid=###ID###" /><param name="quality" value="high" /><param name="wmode" value="opaque" /><embed src="http://i47.vbox7.com/player/ext.swf?vid=###ID###" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="450" height="403" wmode="opaque"></embed></object>',
			'embed_thumb'	=> 'http://i47.vbox7.com/p/###ID###4.jpg',
			'insite_url'	=> 'http://vbox7.com/play:###ID###',
		),
	);
	
?>