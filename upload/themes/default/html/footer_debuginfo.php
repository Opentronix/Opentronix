<?php
	
	$loadavg	= '';
	if( function_exists('sys_getloadavg') ) {
		$sys_loadavg	= sys_getloadavg();
		$loadavg	= implode(', ', $sys_loadavg);
		if( $sys_loadavg[0] > 2 ) { $loadavg = '<span style="color: red;">'.$loadavg.'</span>'; }
	}
	$memusage	= round(memory_get_usage()/(1024*1024),2);
	$pageload	= number_format(microtime(TRUE)-$GLOBALS['SCRIPT_START_TIME'], 3, '.', '');
	$sql1	= $GLOBALS['db1'] ? $GLOBALS['db1']->get_debug_info() : FALSE;
	$sqlnum = $sqltime = 0;
	if( $sql1 ) { $sqlnum += count($sql1->queries); $sqltime += floatval($sql1->time); }
	if( $sqltime > 1 ) { $sqltime = '<span style="color: red;">'.$sqltime.'</span>'; }
	$mch	= $GLOBALS['cache']->get_debug_info();
	$mchnum	= count($mch->queries);
	$mchtime	= floatval($mch->time);
	
?>
		<div style="width: 980px; padding-bottom: 20px; margin: 0px auto;">
			<script type="text/javascript">
				function show_sql_details() {
					document.getElementById("mch_details_div").style.display	= "none";
					with(document.getElementById("sql_details_div").style)
						display	= display=="none" ? "" : "none";
				}
				function show_mch_details() {
					document.getElementById("sql_details_div").style.display	= "none";
					with(document.getElementById("mch_details_div").style)
						display	= display=="none" ? "" : "none";
				}
			</script>
			<div style="width: 100%; padding-bottom: 10px; font-size: 10px; color: #888; text-align: center;">
				<a href="javascript:;" onclick="show_sql_details(); scroll(0, 100000);" style="color: #888; text-decoration: underline; font-size: 10px;">DB Queries: <?= $sqlnum ?></a> (<?= $sqltime ?>s)
				| <a href="javascript:;" onclick="show_mch_details(); scroll(0, 100000);" style="color: #888; text-decoration: underline; font-size: 10px;">CACHE Queries: <?= $mchnum ?></a> (<?= $mchtime ?>s)
				| Script Execution Time: <?= $pageload ?> | Memory usage: <?= $memusage ?>MB | Server Load Average: <?= $loadavg ?>
			</div>
			<table id="sql_details_div" style="display: none; width: 980px; border-collapse: collapse; background-color: #eee;" border="1" bordercolor="#888888">
	<?php if($sql1) { foreach($sql1->queries as $query) { if( floatval($query->time) > 0.5 ) { $query->time = '<span style="color: red;">'.$query->time.'</span>'; } ?>
				<tr><td valign="top" style="text-align:left; width: 50px; padding: 2px; color: #888; font-size: 10px"><?= $query->time ?></td>
					<td valign="top" style="text-align:left; padding: 2px; color: #888; font-size: 10px"><?= $query->query ?></td></tr>
	<?php }} ?>
			</table>
			<table id="mch_details_div" style="display: none; width: 980px; border-collapse: collapse; background-color: #eee;" border="1" bordercolor="#888888">
	<?php foreach($mch->queries as $query) { ?>
				<tr><td valign="top" style="text-align:left; width: 50px; padding: 2px; color: #888; font-size: 10px;"><?= $query->time ?></td>
					<td valign="top" style="text-align:left; padding: 2px; color: #888; font-size: 10px;"><?= $query->action ?></td>
					<td valign="top" style="text-align:left; padding: 2px; color: #888; font-size: 10px;"><?= $query->key ?></td>
					<td valign="top" style="text-align:left; padding: 2px; color: #888; font-size: 10px;"><?= $query->result ?></td></tr>
	<?php } ?>
			</table>
		</div>