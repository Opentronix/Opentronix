<?php
	
	$PAGE_TITLE	= 'Installation - Step 4';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	$options	= array (
		'memcached'		=> class_exists('Memcached',FALSE)||class_exists('Memcache',FALSE) ? TRUE : FALSE,
		'apc'			=> function_exists('apc_cache_info') ? TRUE : FALSE,
		'mysqlheap'		=> FALSE,
		'filesystem'	=> TRUE,
	);
	$tmp	= my_mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
	if( $tmp ) {
		$tmp	= my_mysql_get_server_info($tmp);
		$tmp	= str_replace('.','',substr($tmp, 0, 5));
		if( intval($tmp) >= 503 ) {
			$options['mysqlheap']	= TRUE;
		}
	}
	
	$is_upgrade	= isset($OLDC->CACHE_MECHANISM, $OLDC->CACHE_KEYS_PREFIX, $OLDC->CACHE_MEMCACHE_HOST, $OLDC->CACHE_MEMCACHE_PORT);
	if( $is_upgrade ) {
		$s['CACHE_MECHANISM']	= $OLDC->CACHE_MECHANISM;
		$s['CACHE_EXPIRE']	= isset($OLDC->CACHE_EXPIRE) ? $OLDC->CACHE_EXPIRE : 60*60;
		$s['CACHE_KEYS_PREFIX']	= $OLDC->CACHE_KEYS_PREFIX;
		$s['CACHE_MEMCACHE_HOST']	= $OLDC->CACHE_MEMCACHE_HOST;
		$s['CACHE_MEMCACHE_PORT']	= $OLDC->CACHE_MEMCACHE_PORT;
		if( $s['CACHE_MECHANISM']=='apc' || $s['CACHE_MECHANISM']=='memcached' || $s['CACHE_MECHANISM']=='mysqlheap' || $s['CACHE_MECHANISM']=='filesystem' ) {
			if( $options[$s['CACHE_MECHANISM']] == TRUE ) {
				$error	= FALSE;
				if( $s['CACHE_MECHANISM']=='memcached' ) {
					$tmp	= class_exists('Memcached',FALSE) ? new Memcached() : new Memcache();
					$tmp	= $tmp->addServer($s['CACHE_MEMCACHE_HOST'], intval($s['CACHE_MEMCACHE_PORT']));
					if( ! $tmp ) {
						$error	= TRUE;
					}
				}
				if( ! $error ) {
					$_SESSION['INSTALL_STEP']	= 4;
					header('Location: ?next&r='.rand(0,99999));
				}
			}
		}
	}
	
	if( ! isset($s['CACHE_EXPIRE']) ) {
		$s['CACHE_EXPIRE']	= 60*60;
	}
	if( ! isset($s['CACHE_KEYS_PREFIX']) ) {
		$s['CACHE_KEYS_PREFIX']	= substr(md5(time().rand()),0,5).'~';
	}
	
	if( ! isset($s['CACHE_MECHANISM']) ) {
		$s['CACHE_MECHANISM']	= '';
	}
	if( ! isset($s['CACHE_MEMCACHE_HOST']) ) {
		$s['CACHE_MEMCACHE_HOST']	= '';
	}
	if( ! isset($s['CACHE_MEMCACHE_PORT']) ) {
		$s['CACHE_MEMCACHE_PORT']	= '';
	}
	
	$submit	= FALSE;
	$error	= FALSE;
	$errmsg	= '';
	if( isset($_POST['CACHE_MECHANISM']) ) {
		$_SESSION['INSTALL_STEP']	= 3;
		$submit	= TRUE;
		$s['CACHE_MECHANISM']	= $_POST['CACHE_MECHANISM'];
		if( ! $options[$s['CACHE_MECHANISM']] ) {
			$error	= TRUE;
			$errmsg	= 'Please choose one from the list.';
		}
		if( ! $error && $s['CACHE_MECHANISM']=="memcached" ) {
			$s['CACHE_MEMCACHE_HOST']	= trim($_POST['CACHE_MEMCACHE_HOST']);
			$s['CACHE_MEMCACHE_PORT']	= trim($_POST['CACHE_MEMCACHE_PORT']);
			if( empty($s['CACHE_MEMCACHE_HOST']) || empty($s['CACHE_MEMCACHE_PORT']) ) {
				$error	= TRUE;
				$errmsg	= 'Enter Memcached host & port.';
			}
			else {
				$tmp	= class_exists('Memcached',FALSE) ? new Memcached() : new Memcache();
				$tmp	= $tmp->addServer($s['CACHE_MEMCACHE_HOST'], intval($s['CACHE_MEMCACHE_PORT']));
				if( ! $tmp ) {
					$error	= TRUE;
					$errmsg	= 'Cannot connect to Memcached server.';
				}
			}
		}
		if( ! $error ) {
			$_SESSION['INSTALL_STEP']	= 4;
			header('Location: ?next');
		}
	}
	
	$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Cache Settings</h3>
								</div>
							</div>
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<p>In order to work fast enough, '.SITE_TITLE.' needs caching mechanism on your system.<br />Choose one:</p>
										<script type="text/javascript">
											function toggle_memcached_form() {
												var i, els	= document.f.getElementsByTagName("INPUT");
												var ismc	= false;
												for(i=0; i<els.length; i++) {
													if( els[i].type != "radio" ) { continue; }
													if( els[i].name != "CACHE_MECHANISM" ) { continue; }
													if( ! els[i].checked ) { continue; }
													if( els[i].value != "memcached" ) { continue; }
													if( els[i].disabled ) { continue; }
													ismc	= true;
													break;
												}
												document.getElementById("form_memcached_row").style.display = ismc ? "" : "none";
											}
										</script>';
	
	if( $error ) {
		$html	.= errorbox('Error', $errmsg);
	}
	
	$html	.= '
										<form name="f" method="post" action="">
										<table id="setform" cellpadding="5" style="width:100%;">
											<tr>
												<td width="220">
													<label>
														<input type="radio" name="CACHE_MECHANISM" value="memcached" '.($options['memcached']?'':'disabled="disabled"').' '.($s['CACHE_MECHANISM']=='memcached'?'checked="checked"':'').' onclick="toggle_memcached_form();" onchange="toggle_memcached_form();" />
														<b>Memcached</b>
													</label>
												</td>
												<td style="color:#666;">'.($options['memcached']?'recommended':'not available').'</td>
											</tr>
											<tr id="form_memcached_row" style="'.($s['CACHE_MECHANISM']=='memcached'&&$options['memcached'] ? '' : 'display:none;').'">
												<td colspan="2" style="padding:0px;">
													<table cellpadding="3">
														<tr>
															<td>Memcached Host:</td>
															<td><input type="text" class="forminput" name="CACHE_MEMCACHE_HOST" value="'.htmlspecialchars($s['CACHE_MEMCACHE_HOST']).'" style="width:140px; padding:2px;" /></td>
															<td>usually "localhost"</td>
														</tr>
														<tr>
															<td>Memcached Post:</td>
															<td><input type="text" class="forminput" name="CACHE_MEMCACHE_PORT" value="'.htmlspecialchars($s['CACHE_MEMCACHE_PORT']).'" style="width:140px; padding:2px;" /></td>
															<td>usually "11211"</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td>
													<label>
														<input type="radio" name="CACHE_MECHANISM" value="apc" '.($options['apc']?'':'disabled="disabled"').' '.($s['CACHE_MECHANISM']=='apc'?'checked="checked"':'').' onclick="toggle_memcached_form();" onchange="toggle_memcached_form();" />
														<b>APC Alterntive PHP Cache</b>
													</label>
												</td>
												<td style="color:#666;">'.($options['apc']?'recommended':'not available').'</td>
											</tr>
											<tr>
												<td>
													<label>
														<input type="radio" name="CACHE_MECHANISM" value="filesystem" '.($options['filesystem']?'':'disabled="disabled"').' '.($s['CACHE_MECHANISM']=='filesystem'?'checked="checked"':'').' onclick="toggle_memcached_form();" onchange="toggle_memcached_form();" />
														<b>FileSystem Storage</b>
													</label>
												</td>
												<td style="color:#666;">'.($options['filesystem']?'':'not available').'</td>
											</tr>
											<tr>
												<td>
													<label>
														<input type="radio" name="CACHE_MECHANISM" value="mysqlheap" '.($options['mysqlheap']?'':'disabled="disabled"').' '.($s['CACHE_MECHANISM']=='mysqlheap'?'checked="checked"':'').' onclick="toggle_memcached_form();" onchange="toggle_memcached_form();" />
														<b>MySQL Memory Table</b>
													</label>
												</td>
												<td style="color:#666;">'.($options['mysqlheap']?'not recommended':'not available').'</td>
											</tr>
											<tr>
												<td style="padding-top: 20px;"><input type="submit" name="submit" value="Continue" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
										</form>
									</div>
								</div>
							</div>';
	
?>