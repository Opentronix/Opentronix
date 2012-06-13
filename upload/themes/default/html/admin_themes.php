<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">
							<?php $this->load_template('admin_leftmenu.php') ?>
						</div>
						<div id="settings_right">
							<script type="text/javascript">
								var seltheme	= "<?= $C->THEME ?>";
								function sel_theme(th) {
									d.getElementById("theme_"+seltheme).className	= "theme";
									d.getElementById("radio_"+seltheme).checked	= false;
									seltheme	= th;
									d.getElementById("theme_"+th).className	= "theme selected";
									d.getElementById("radio_"+th).checked	= true;
								}
							</script>
							<div class="ttl">
								<div class="ttl2">
									<h3><?= $this->lang('admbrnd_th_title') ?></h3>
								</div>
							</div>
							<?php if( $D->changetheme_flag ) { ?>
								<?php if( ! $D->changetheme_warn ) { ?>
									<?= okbox($this->lang('admbrnd_th_theme_ok1'), $this->lang('admbrnd_th_theme_ok2'), TRUE, 'margin-top:5px; margin-bottom:5px;') ?>
								<?php } else { ?>
									<?= okbox($this->lang('admbrnd_th_theme_ok2'), $this->lang('admbrnd_th_theme_ok3',array('#A1#'=>'<a href="'.$C->SITE_URL.'admin/networkbranding">','#A2#'=>'</a>')), TRUE, 'margin-top:5px; margin-bottom:5px;') ?>
								<?php } ?>
							<?php } ?>
							<form method="post" action="<?= $C->SITE_URL ?>admin/themes">
								<?php foreach($D->themes as $n=>$t) { ?>
								<div id="theme_<?= $n ?>" class="theme<?= $n==$C->THEME ? ' selected' : '' ?>">
									<div class="themeselector">
										<input type="radio" id="radio_<?= $n ?>" onclick="if(this.checked){sel_theme('<?= $n ?>')};" onfocus="this.blur();" name="set_theme" <?= $n==$C->THEME?'checked="checked"':'' ?> value="<?= $n ?>" />
									</div>
									<a href="javascript:;" onclick="sel_theme('<?= $n ?>');" onfocus="this.blur();" class="themeimage">
										<div style="width:200px; min-height:70px;">
											<?php if( !empty($t->image) ) { ?>
											<img src="<?= $C->SITE_URL.'themes/'.$n.'/'.$t->image ?>" alt="" />
											<?php } ?>
										</div>
									</a>
									<div class="themeinfo">
										<h3><?= htmlspecialchars($t->name) ?></h3>
										<div class="thememeta"><?= $this->lang('admbrnd_th_theme_author') ?> <a href="<?= htmlspecialchars($t->author_url) ?>" target="_blank"><?= htmlspecialchars($t->author_name) ?></a></div>
										<p><?= htmlspecialchars($t->description) ?></p>
									</div>
								</div>
								<?php } ?>
								<div id="submittheme">
									<input type="submit" value="<?= $this->lang('admbrnd_th_theme_select') ?>" style="padding:4px; font-weight:bold;" />
								</div>
							</form>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>