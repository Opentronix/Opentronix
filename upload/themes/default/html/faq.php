<?php
	$this->load_template('header.php');
?>

<div id="settings">

	<div id="settings_left">				
		<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('faq_title') ?></h3></div></div>
		<div class="sidenav">
			<?php
			for($i = 1; $i <= intval($this->lang('faqpb_cats_number')); $i++)
			{	
			?>
				<a href="<?= $C->SITE_URL ?>faq/show:<?= $i ?>" <?php if($D->choosen_param == $i) echo 'class="onsidenav"'; ?> > <?= htmlspecialchars($D->data_arr[$i]->title) ?> </a>	
			<?php 
			}
			?>
		</div>

		<div class="greygrad" style="margin-top:10px;"><div class="greygrad2"><div class="greygrad3">
			<?= $this->lang('cant_understand') ?>
			<div class="klear"></div>
			<a href="<?= $C->SITE_URL ?>contacts" class="ubluebtn"><b><?= $this->lang('cont_us') ?></b></a>
		</div></div></div>

	</div>
	<div id="settings_right">
				
		<div class="ttl"><div class="ttl2"><h3><?= htmlspecialchars($D->data_arr[$D->choosen_param]->title) ?></h3></div></div>
			
			<?php
			for($i = 1; $i <= intval($this->lang('faqpb_c'.$D->choosen_param.'_posts_number')); $i++ )
			{
			?>
			
				<div class="faqq">
					<h3><?= htmlspecialchars($D->data_arr[$D->choosen_param]->topics[$D->choosen_param.'-'.$i]->title) ?></h3>
					<div class="greygrad"><div class="greygrad2"><div class="greygrad3" style="padding-bottom:0px;">
						
						<?php
						if(htmlspecialchars($D->data_arr[$D->choosen_param]->topics[$D->choosen_param.'-'.$i]->image) != '')
						{
						?>
							<div class="faqimg" style="width:auto; margin-bottom:10px;">
								<img src="<?= $C->SITE_URL.'themes/'.$C->THEME.'/'.str_replace('design/faq', 'imgs/faq', $D->data_arr[$D->choosen_param]->topics[$D->choosen_param.'-'.$i]->image) ?>">
								<span><?= htmlspecialchars($D->data_arr[$D->choosen_param]->topics[$D->choosen_param.'-'.$i]->imgtxt) ?></span>
							</div>
						<?php
						}
						?>
						
						<p>
							<?= $D->data_arr[$D->choosen_param]->topics[$D->choosen_param.'-'.$i]->text ?>
						</p>
						
					</div></div></div>
				</div>
				
			<?php
			}
			?>
	</div>
	
</div>

<?php
	$this->load_template('footer.php');
?>