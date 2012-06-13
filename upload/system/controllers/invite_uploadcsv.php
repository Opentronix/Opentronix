<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/invite.php');
	
	$D->page_title	= $this->lang('os_invite_ttl_uploadcsv', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	
	if( $this->param('get')=='loaded' && isset($this->user->sess['INVITE_EMAILS_LOADED']) && isset($_POST['emails']) )
	{
		$emails	= array();
		foreach($_POST['emails'] as $e) {
			if( isset( $this->user->sess['INVITE_EMAILS_LOADED'][$e] ) ) {
				$emails[$e]	= $this->user->sess['INVITE_EMAILS_LOADED'][$e];
			}
		}
		if( 0 == count($emails) ) {
			$this->redirect('invite/parsemail/tab:'.$this->param('tab'));
		}
		foreach($emails as $k=>$v) {
			if( empty($v) ) {
				$emails[$k]	= $k;
			}
		}
		$_POST['name']	= array();
		$_POST['email']	= array();
		foreach($emails as $k=>$v) {
			$_POST['name'][]	= $v;
			$_POST['email'][]	= $k;
		}
		unset($_POST['emails']);
		unset($this->user->sess['INVITE_EMAILS_LOADED']);
		require_once( $this->controllers.'invite.php' );
		return;
	}
	elseif( isset($_FILES['uplfile']) && is_uploaded_file($_FILES['uplfile']['tmp_name']) ) {
		$D->submit	= TRUE;
		$content	= file_get_contents($_FILES['uplfile']['tmp_name']);
		if( ! $content ) {
			$D->error	= TRUE;
			$D->errmsg	= 'inv_uplfile_err_readfile';
		}
		if( ! $D->error ) {
			$data	= array();
			if( preg_match('/BEGIN\:VCARD/iu', $content) ) {
				$vcards	= explode('BEGIN:VCARD', $content);
				foreach($vcards as $vcard) {
					$one	= trim($vcard);
					if( empty($vcard) ) {
						continue;
					}
					$tmp_N	= '';
					$tmp_FN	= '';
					$tmp_EMAIL	= '';
					$vcard	= explode("\n", $vcard);
					foreach($vcard as $one) {
						if( empty($one) ) { continue; }
						if( ! preg_match('/^(N|FN|EMAIL)(\:|\;)(.*)$/iu', $one, $matches) ) {
							continue;
						}
						${'tmp_'.$matches[1]}	= trim($matches[3]);
					}
					if( empty($tmp_EMAIL) ) {
						continue;
					}
					$name	= '';
					if( ! empty($tmp_FN) ) {
						$sdf	= explode(':', $tmp_FN);
						$sdf	= array_reverse($sdf);
						$name	= trim($sdf[0]);
					}
					elseif( ! empty($tmp_N) ) {
						$sdf	= explode(':', $tmp_N);
						$sdf	= array_reverse($sdf);
						$sdf	= trim($sdf[0]);
						$sdf	= explode(';', $sdf);
						$sdf	= array_reverse($sdf);
						$name	= implode(' ', $sdf);
					}
					$mail	= '';
					$sdf	= explode(':', $tmp_EMAIL);
					$sdf	= array_reverse($sdf);
					$mail	= trim($sdf[0]);
					if( empty($mail) ) {
						continue;
					}
					if( empty($name) ) {
						$name	= $mail;
					}
					if( ! is_valid_email($mail) ) {
						continue;
					}
					$data[$mail]	= $name;
				}
			}
			else {
				$csv_rows	= explode("\n", $content);
				$csv_cols	= array();
				$hdr	= $csv_rows[0];
				$hdr	= explode(',', $hdr);
				foreach($hdr as $sdf) {
					$csv_cols[]	= trim(trim($sdf, '"'));
				}
				unset($csv_rows[0]);
				$csv_rows2	= array();
				foreach($csv_rows as $row) {
					$row	= trim($row);
					if( empty($row) ) { continue; }
					$row	= explode(',', $row);
					foreach($row as &$field) {
						$field	= trim(trim($field, '"'));
					}
					$csv_rows2[]	= $row;
				}
				$indx_email	= FALSE;
				$indx_fname	= FALSE;
				$indx_lname	= FALSE;
				$indx_name	= FALSE;
				foreach( $csv_cols as $i=>$v ) {
					if( $v == 'First Name' || $v == 'First' ) {
						$indx_fname	= $i;
					}
					elseif( $v == 'Last Name' || $v == 'Last' ) {
						$indx_lname	= $i;
					}
					elseif( $v == 'Display Name' ) {
						$indx_name	= $i;
					}
					elseif( $v == 'Primary Email' || $v == 'Email' || $v == 'E-mail' || $v == 'E-mail Address' ) {
						$indx_email	= $i;
					}
				}
				if( FALSE !== $indx_email ) {
					$data	= array();
					foreach($csv_rows2 as $row) {
						$fname	= '';
						$lname	= '';
						$name		= '';
						$mail		= $row[$indx_email];
						if( FALSE !== $indx_fname ) { $fname = $row[$indx_fname]; }
						if( FALSE !== $indx_lname ) { $lname = $row[$indx_lname]; }
						if( FALSE !== $indx_name ) { $name = $row[$indx_name]; }
						if( empty($mail) || !is_valid_email($mail) ) {
							continue;
						}
						if( empty($name) ) {
							$name	= trim($fname.' '.$lname);
						}
						if( empty($name) ) {
							$name	= $mail;
						}
						$name	= preg_replace('/\"+/iu', ' ', $name);
						$name	= preg_replace('/\s+/', ' ', $name);
						$name	= trim($name);
						$data[$mail]	= $name;
					}
				}
			}		
		}
		if( !$D->error && count($data)==0 ) {
			$D->error	= TRUE;
			$D->errmsg	= 'inv_uplfile_err_parsefile';
		}
		if( !$D->error ) {
			$D->parsed_mails	= $data;
			$this->user->sess['INVITE_EMAILS_LOADED']	= $D->parsed_mails;
			$this->load_template('invite_choose_mails.php');
			return;
		}
	}
	
	$this->load_template('invite_uploadcsv.php');
	
?>