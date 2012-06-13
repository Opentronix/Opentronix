<?php
	
	function bb_apply_tags($text)
	{
		$text	= preg_replace( '#\[b\](.+?)\[/b\]#is', '<b>\\1</b>', $text );
		$text	= preg_replace( '#\[i\](.+?)\[/i\]#is', '<i>\\1</i>', $text );
		$text	= preg_replace( '#\[u\](.+?)\[/u\]#is', '<u>\\1</u>', $text );
		
		$text	= preg_replace( '#(^|\s)((http|https|news|ftp)://\w+[^\s\[\]]+)#ie', "bb_build_url('\\2', '\\2')", $text );
		$text = preg_replace( '#\[url\](\S+?)\[/url\]#ie', "bb_build_url('\\1', '\\1')", $text );
		$text = preg_replace( '#\[url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#ie', "bb_build_url('\\1', '\\2')", $text );
		$text = preg_replace( '#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#ie', "bb_build_url('\\1', '\\2')", $text );
		
		return $text;
	}
	
	function bb_build_url( $link, $txt )
	{
		$url	= array();
		$url['html']	= $link;
		$url['show']	= $txt;
		$url['st']	= '';
		$url['end']	= '';
		$skip_it = 0;
		
		if ( preg_match( "/([\.,\?]|&#33;)$/", $url['html'], $match) ) {
			$url['end'] .= $match[1];
			$url['html'] = preg_replace( "/([\.,\?]|&#33;)$/", "", $url['html'] );
			$url['show'] = preg_replace( "/([\.,\?]|&#33;)$/", "", $url['show'] );
		}
		
		$url['html'] = preg_replace( "/&amp;/" , "&" , $url['html'] );
		
		$url['html'] = preg_replace( "/javascript:/i", "java script&#58;", $url['html'] );
		
		if ( ! preg_match("#^(http|news|https|ftp|aim)://#", $url['html'] ) ) {
			$url['html'] = 'http://'.$url['html'];
		}
		
		if (preg_match( "/^img src/i", $url['show'] )) $skip_it = 1;
		
		$url['show'] = preg_replace( "/&amp;/" , "&" , $url['show'] );
		$url['show'] = preg_replace( "/javascript:/i", "javascript&#58;", $url['show'] );
		
		if ( (strlen($url['show']) -58 ) < 3 )  $skip_it = 1;
		
		if (!preg_match( "/^(http|ftp|https|news):\/\//i", $url['show'] )) $skip_it = 1;
		
		$show     = $url['show'];
		
		if ($skip_it != 1) {
			$stripped = preg_replace( "#^(http|ftp|https|news)://(\S+)$#i", "\\2", $url['show'] );
			$uri_type = preg_replace( "#^(http|ftp|https|news)://(\S+)$#i", "\\1", $url['show'] );
			$show = $uri_type.'://'.substr( $stripped , 0, 35 ).'...'.substr( $stripped , -15   );
		}
		return $url['st'] . '<a href="'.$url['html'].'" target="_blank">'.$show.'</a>' . $url['end'];
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/faq-pubnet.php');
	$D->page_title	= $this->lang('faq_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$data	= array();
	$num_cats	= intval($this->lang('faqpb_cats_number'));

	for($i=1; $i<=$num_cats; $i++) 
	{
		$data[$i]= (object) array(
			'title'	=> $this->lang('faqpb_c'.$i.'_title'),
			'topics'	=> array(),
		);
		$num_topics	= intval($this->lang('faqpb_c'.$i.'_posts_number'));
		for($j=1; $j<=$num_topics; $j++) 
		{
			$data[$i]->topics[$i.'-'.$j]	= (object) array(
					'title'	=> $this->lang('faqpb_c'.$i.'_p'.$j.'_title', array('#SITE_TITLE#'=>$C->SITE_TITLE)),
					'text'	=> nl2br(bb_apply_tags($this->lang('faqpb_c'.$i.'_p'.$j.'_text', array('#SITE_URL#'=>$C->SITE_URL, '#SITE_TITLE#'=>$C->SITE_TITLE)))),
					'image'	=> $this->lang('faqpb_c'.$i.'_p'.$j.'_image'),
					'imgtxt'	=> $this->lang('faq_c'.$i.'_p'.$j.'_imgtxt'),
			);
		}
	}
	$D->data_arr = &$data;

	if($this->param('show'))
	{
		$param = trim($this->param('show'));
		if(!preg_match('/^[0-9]*$/', $param) || ($param < 1 || $param > intval($this->lang('faqpb_cats_number')))) $param = 1;
	}
	else $param = 1;
		
	$D->choosen_param = $param;
	
	$this->load_template('faq.php');
	
?>