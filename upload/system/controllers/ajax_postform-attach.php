<?php
	
	$this->load_langfile('inside/global.php');
	
	echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
	
	if( !$this->network->id ) {
		echo '<result></result>';
		return;
	}
	if( !$this->user->is_logged ) {
		echo '<result></result>';
		return;
	}
	
	$post_temp_id	= isset($_POST['post_temp_id']) ? trim($_POST['post_temp_id']) : '';
	$attach_type	= isset($_POST['at_type']) ? trim($_POST['at_type']) : '';
	$attach_data	= isset($_POST['data']) ? trim($_POST['data']) : '';
	$s	= & $this->user->sess;
	
	if( empty($post_temp_id) || empty($attach_type) || empty($attach_data) ) {
		echo '<result></result>';
		return;
	}
	
	if( ! isset($s['POSTFORM_TEMP_POSTS']) ) {
		$s['POSTFORM_TEMP_POSTS']	= array();
	}
	if( ! isset($s['POSTFORM_TEMP_POSTS'][$post_temp_id]) ) {
		$s['POSTFORM_TEMP_POSTS'][$post_temp_id]	= new newpost();
	}
	$p	= & $s['POSTFORM_TEMP_POSTS'][$post_temp_id];
	
	if( $attach_type == 'link' && $C->ATTACH_LINK_DISABLED==0 )
	{
		if( $l = $p->attach_link($attach_data) ) {
			//$txt	= preg_replace('/^(http|https|ftp)\:\/\//iu', '', $l->link);
			//$txt	= str_cut_link($txt,16);
			$txt	= str_cut_link($l->link,16);
			echo '<result><status>OK</status>';
			echo '<attach text="'.htmlspecialchars('<a href="'.$l->link.'" target="_blank" onfocus="this.blur();" title="'.htmlspecialchars($l->link).'">'.htmlspecialchars($txt).'</a>').'" />';
			echo '</result>';
			return;
		}
		echo '<result><status>ERROR</status><message>'.htmlspecialchars('<span style="color:red;">'.$this->lang('pf_atchbx_err_link').'</span>').'</message></result>';
		return;
	}
	if( $attach_type == 'videoembed' && $C->ATTACH_VIDEO_DISABLED==0 )
	{
		if( $v = $p->attach_videoembed($attach_data) ) {
			$txt	= preg_replace('/^(http|https|ftp)\:\/\//iu', '', $v->orig_url);
			$txt	= str_cut_link($txt,16);
			echo '<result><status>OK</status>';
			echo '<attach text="'.htmlspecialchars('<a href="'.$v->orig_url.'" target="_blank" onfocus="this.blur();" title="'.htmlspecialchars($v->orig_url).'">'.htmlspecialchars($txt).'</a>').'" />';
			echo '</result>';
			return;
		}
		echo '<result><status>ERROR</status><message>'.htmlspecialchars('<span style="color:red;">'.$this->lang('pf_atchbx_err_videmb').'</span>').'</message></result>';
		return;
	}
	if( $attach_type == 'file' && $C->ATTACH_FILE_DISABLED==0 )
	{
		if( !isset($s['POSTFORM_TEMP_FILES']) || !isset($s['POSTFORM_TEMP_FILES'][$attach_data]) ) {
			echo '<result><status>WAIT</status></result>';
			return;
		}
		$f	= & $s['POSTFORM_TEMP_FILES'][$attach_data];
		if( $f ) {
			if( $ff = $p->attach_file($C->TMP_DIR.$f->tempfile, $f->filename) ) {
				rm($C->TMP_DIR.$f->tempfile);
				unset($s['POSTFORM_TEMP_FILES'][$attach_data]);
				echo '<result><status>OK</status>';
				echo '<attach text="'.htmlspecialchars('<a href="'.$C->SITE_URL.'getfile/tmpid:'.$post_temp_id.'/'.htmlspecialchars($ff->title).'" onfocus="this.blur();" title="'.htmlspecialchars($ff->title).'">'.htmlspecialchars(str_cut_link($ff->title,16)).'</a>').'" />';
				echo '</result>';
				return;
			}
		}
		echo '<result><status>ERROR</status><message>'.htmlspecialchars('<span style="color:red;">'.$this->lang('pf_atchbx_err_file').'</span>').'</message></result>';
		return;
	}
	if( $attach_type == 'image' && $C->ATTACH_IMAGE_DISABLED==0 )
	{
		$image_is_from	= substr($attach_data, 0, 4);
		$attach_data	= substr($attach_data, 4);
		if($image_is_from == "url|") {
			if( $i = $p->attach_image($attach_data, urldecode(basename($attach_data))) ) {
				echo '<result><status>OK</status>';
				echo '<attach text="'.htmlspecialchars('<a href="'.$C->TMP_URL.$i->file_original.'" target="_blank" onfocus="this.blur();" title="'.htmlspecialchars($i->title).'">'.htmlspecialchars(str_cut($i->title,16)).'</a>').'" />';
				echo '</result>';
				return;
			}
		}
		elseif($image_is_from == "upl|") {
			if( !isset($s['POSTFORM_TEMP_FILES']) || !isset($s['POSTFORM_TEMP_FILES'][$attach_data]) ) {
				echo '<result><status>WAIT</status></result>';
				return;
			}
			$i	= & $s['POSTFORM_TEMP_FILES'][$attach_data];
			if( $i ) {
				if( $ii = $p->attach_image($C->TMP_DIR.$i->tempfile, $i->filename) ) {
					rm($C->TMP_DIR.$i->tempfile);
					unset($s['POSTFORM_TEMP_FILES'][$attach_data]);
					echo '<result><status>OK</status>';
					echo '<attach text="'.htmlspecialchars('<a href="'.$C->TMP_URL.$ii->file_original.'" target="_blank" onfocus="this.blur();" title="'.htmlspecialchars($ii->title).'">'.htmlspecialchars(str_cut($ii->title,16)).'</a>').'" />';
					echo '</result>';
					return;
				}
			}
		}
		echo '<result><status>ERROR</status><message>'.htmlspecialchars('<span style="color:red;">'.$this->lang('pf_atchbx_err_img').'</span>').'</message></result>';
		return;
	}
	
	echo '<result></result>';
	return;
	
?>