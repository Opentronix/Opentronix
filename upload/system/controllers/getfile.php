<?php
	
	if( !$this->network->id ) {
		exit;
	}
	
	if( $this->param('pid') )
	{
		$tmp	= trim($this->param('pid'));
		if( ! preg_match('/^(public|private)_([0-9]+)$/', $tmp, $m) ) {
			exit;
		}
		$p	= new post($m[1], $m[2]);
		if( $p->error ) {
			exit;
		}
		$tmp	= $p->post_attached;
		if( $this->param('tp')=='image' ) {
			if( ! isset($tmp['image']) ) {
				exit;
			}
			$tmp	= $tmp['image'];
			$file	= $C->IMG_DIR.'attachments/'.$this->network->id.'/'.$tmp->file_original;
			if( ! file_exists($file) ) {
				exit;
			}
			$cnttype	= 'application/octet-stream';
			list($w, $h, $tp)	= getimagesize($file);
			if( $tp && $tp = image_type_to_mime_type($tp) ) {
				$cnttype	= $tp;
			}
			header('Content-Description: File Transfer');
			header('Content-type: '.$cnttype);
			header('Content-Disposition: attachment; filename="'.$tmp->title.'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			readfile($file);
		}
		else {
			if( ! isset($tmp['file']) ) {
				exit;
			}
			$tmp	= $tmp['file'];
			$file	= $C->IMG_DIR.'attachments/'.$this->network->id.'/'.$tmp->file_original;
			if( ! file_exists($file) ) {
				exit;
			}
			header('Content-Description: File Transfer');
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$tmp->title.'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			readfile($file);
		}
		exit;
	}
	
	if( $this->param('tmpid') )
	{
		$tmp	= trim($this->param('tmpid'));
		$s	= & $this->user->sess;
		if( isset($s['POSTFORM_TEMP_POSTS']) && isset($s['POSTFORM_TEMP_POSTS'][$tmp]) ) {
			$tmp	= $s['POSTFORM_TEMP_POSTS'][$tmp]->get_attached();
			if( ! isset($tmp['file']) ) {
				exit;
			}
			$tmp	= $tmp['file'];
			$file	= $C->TMP_DIR.$tmp->file_original;
			if( ! file_exists($file) ) {
				exit;
			}
			header('Content-Description: File Transfer');
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$tmp->title.'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			readfile($file);
			exit;
		}
	}
	
	exit;
	
?>