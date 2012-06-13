<?php
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('mobile/newpost.php');
	
	echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
	
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
			echo '<result><status>OK</status>';
			echo '<attach text="'.htmlspecialchars($l->link).'" />';
			echo '</result>';
			return;
		}
		echo '<result><status>ERROR</status><message>'.$this->lang('iphone_pf_atchbx_err_link').'</message></result>';
		return;
	}
	if( $attach_type == 'videoembed' && $C->ATTACH_VIDEO_DISABLED==0 )
	{
		if( $v = $p->attach_videoembed($attach_data) ) {
			$txt	= preg_replace('/^(http|https|ftp)\:\/\//iu', '', $v->orig_url);
			$txt	= str_cut_link($txt,16);
			echo '<result><status>OK</status>';
			echo '<attach text="'.htmlspecialchars($v->orig_url).'" />';
			echo '</result>';
			return;
		}
		echo '<result><status>ERROR</status><message>'.$this->lang('iphone_pf_atchbx_err_videmb').'</message></result>';
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
				echo '<attach text="'.htmlspecialchars($ff->title).'" />';
				echo '</result>';
				return;
			}
		}
		echo '<result><status>ERROR</status><message>'.$this->lang('iphone_pf_atchbx_err_file').'</message></result>';
		return;
	}
	if( $attach_type == 'image' && $C->ATTACH_IMAGE_DISABLED==0 )
	{
		$image_is_from	= substr($attach_data, 0, 4);
		$attach_data	= substr($attach_data, 4);
		if($image_is_from == "url|") {
			if( $i = $p->attach_image($attach_data, urldecode(basename($attach_data))) ) {
				echo '<result><status>OK</status>';
				echo '<attach text="'.htmlspecialchars($i->title).'" />';
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
					echo '<attach text="'.htmlspecialchars($ii->title).'" />';
					echo '</result>';
					return;
				}
			}
		}
		echo '<result><status>ERROR</status><message>'.$this->lang('iphone_pf_atchbx_err_img').'</message></result>';
		return;
	}
	
	echo '<result></result>';
	return;
	
?>