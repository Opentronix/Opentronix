<?php
		
	$this->load_template('header.php');
	
?>	
		<div id="pagebody" style="margin:0px; border-top:1px solid #fff;">
			<div>		
				<div class="ttl">
					<div class="ttl2"><h3><?= $this->lang('terms_title', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></h3></div>
				</div>
				<div class="greygrad" style="margin-top:5px;">
					<div class="greygrad2">
						<div class="greygrad3">
							<?= nl2br($D->terms) ?>	
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>