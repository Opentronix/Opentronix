<?php
	
	if( !$this->user->is_logged ) {
		return;
	}
	
	if( !isset($_POST['keyy']) ) {
		return;
	}
	if( !isset($_FILES['file']) ) {
		return;
	}
	$key	= trim($_POST['keyy']);
	$file	= (object) $_FILES['file'];
	
	if( empty($key) ) {
		return;
	}
	if( ! isset($this->user->sess['POSTFORM_TEMP_FILES']) ) {
		$this->user->sess['POSTFORM_TEMP_FILES']	= array();
	}
	if( ! isset($this->user->sess['POSTFORM_TEMP_FILES'][$key])  ) {
		$this->user->sess['POSTFORM_TEMP_FILES'][$key]	= new stdClass;
	}
	$data	= & $this->user->sess['POSTFORM_TEMP_FILES'][$key];
	
	if( ! is_uploaded_file($file->tmp_name) ) {
		$data	= FALSE;
		return;
	}
	
	$ext	= '';
	$pos	= strpos($file->name, '.');
	if( FALSE !== $pos ) {
		$ext	= '.'.mb_strtolower(mb_substr($file->name,$pos+1));
	}
	$tempfile	= time().rand(1000000,9999999).$ext;
	move_uploaded_file($file->tmp_name, $C->TMP_DIR.$tempfile);
	if( ! file_exists($C->TMP_DIR.$tempfile) ) {
		$data	= FALSE;
		return;
	}
	chmod($C->TMP_DIR.$tempfile, 0777);
	$data	= (object) array (
		'tempfile'	=> $tempfile,
		'filename'	=> $file->name,
		'filesize'	=> filesize($C->TMP_DIR.$tempfile),
	);
	return;
	
?>