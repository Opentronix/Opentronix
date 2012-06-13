<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">
							<?php $this->load_template('admin_leftmenu.php') ?>
						</div>
						<div id="settings_right">
							<?php if( 0 == count($D->charts) ) { ?>
								<div class="ttl">
									<div class="ttl2">
										<h3><?= $this->lang('admtitle_statistics') ?></h3>
										<div id="postfilter">
											<a href="javascript:;" onclick="dropdiv_open('postfilteroptions');" id="postfilterselected" onfocus="this.blur();"><span><?= $D->seldate ?></span></a>
											<div class="postfilteroptions" id="postfilteroptions" style="width:130px; display:none;">
												<?php $j=0; foreach($D->dates as $dt) { $j++; ?>
												<a href="<?= $C->SITE_URL ?>admin/statistics/<?= $dt->lnk ?>" style="white-space:nowrap; text-align:right; <?= $j==count($D->dates)?'border-bottom:0px;':'' ?>"><?= $dt->txt ?></a>
												<?php } ?>
											</div>
											<span><?= $this->lang('admstat_filter') ?></span>
										</div>
									</div>
								</div>
								<?= msgbox($this->lang('admstat_nostat_ttl'), $this->lang('admstat_nostat_txt'), FALSE, 'margin-top:5px; margin-bottom:300px;') ?>
							<?php } else { ?>
							<script type="text/javascript">
								var ofc_data	= [];
								function ofc_datafunc(ofc_id) {
									return ofc_data[ofc_id];
								}
							</script>
							<?php foreach($D->charts as $i=>$chart) { ?>
								<script type="text/javascript">
									ofc_data[<?= $i ?>]	= '<?= json_encode($chart->json) ?>';
									swfobject.embedSWF("<?= $C->IMG_URL ?>swf/open-flash-chart.swf", "ofc_chart_<?= $i ?>", "750", "150", "9.0.0", false, {"get-data": "ofc_datafunc", "id": <?= $i ?>, "loading": "<?= $this->lang('admstat_loading') ?>"}, {"wmode": "opaque"} );
								</script>
								<div class="ttl">
									<div class="ttl2">
										<h3><?= $chart->title ?></h3>
										<div id="postfilter">
											<a href="javascript:;" onclick="dropdiv_open('postfilteroptions<?= $i ?>');" id="postfilterselected" onfocus="this.blur();"><span><?= $D->seldate ?></span></a>
											<div class="postfilteroptions" id="postfilteroptions<?= $i ?>" style="width:130px; display:none;">
												<?php $j=0; foreach($D->dates as $dt) { $j++; ?>
												<a href="<?= $C->SITE_URL ?>admin/statistics/<?= $dt->lnk ?>" style="white-space:nowrap; text-align:right; <?= $j==count($D->dates)?'border-bottom:0px;':'' ?>"><?= $dt->txt ?></a>
												<?php } ?>
											</div>
											<span><?= $this->lang('admstat_filter') ?></span>
										</div>
									</div>
								</div>
								<div style="height:150px; margin-top:10px; margin-bottom:10px;">
									<div id="ofc_chart_<?= $i ?>"></div>
								</div>
							<?php } ?>
							<?php } ?>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>