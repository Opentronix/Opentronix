<?php

	$this->load_langfile('outside/footer.php');
	$this->load_langfile('inside/footer.php');

?>
				</div>
			</div>
			<?php if( $this->user->is_logged ) { ?>
			<div id="footer">
				<div class="linkcol">
					<h4><?= $this->lang('ftrlinks_section_general') ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>"><?= $this->lang('ftrlinks_sgn_dashboard') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL.$this->user->info->username ?>"><?= $this->lang('ftrlinks_sgn_profile') ?></a></div>
					<?php if( $this->user->info->is_network_admin ) { ?>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>admin"><?= $this->lang('ftrlinks_sgn_admin') ?></a></div>
					<?php } else { ?>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>settings"><?= $this->lang('ftrlinks_sgn_settings') ?></a></div>
					<?php } ?>
				</div>
				<div class="linkcol" style="width:190px;">
					<h4><?= $this->lang('ftrlinks_section_groups') ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>groups/tab:my"><?= $this->lang('ftrlinks_sgr_mygroups') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>groups/tab:all"><?= $this->lang('ftrlinks_sgr_allgroups') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>groups/new"><?= $this->lang('ftrlinks_sgr_newgroup') ?></a></div>
				</div>
				<div class="linkcol">
					<h4><?= $this->lang('ftrlinks_section_findpeople') ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>members"><?= $this->lang('os_ftrlinks_sf_members', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>invite"><?= $this->lang('os_ftrlinks_sf_invitemail') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>invite/personalurl"><?= $this->lang('os_ftrlinks_sf_invitelink') ?></a></div>
				</div>
				<div class="linkcol">
					<h4><?= $this->lang('ftrlinks_section_aboutus', array('#SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?></h4>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>contacts"><?= $this->lang('os_ftrlinks_sa_support') ?></a></div>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>faq"><?= $this->lang('os_ftrlinks_sa_faq') ?></a></div>
					<?php if( isset($C->TERMSPAGE_ENABLED,$C->TERMSPAGE_CONTENT) && $C->TERMSPAGE_ENABLED==1 && !empty($C->TERMSPAGE_CONTENT) ) { ?>
					<div class="ftrlink"><a href="<?= $C->SITE_URL ?>terms"><?= $this->lang('os_ftrlinks_sa_terms') ?></a></div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
			<div id="footercorners"><div id="footercorners2"></div></div>
			<div id="subfooter">
				<div id="sfleft">
					<b><?= htmlspecialchars($C->OUTSIDE_SITE_TITLE) ?></b>
					&middot;
					<a href="<?= $C->SITE_URL ?>contacts"><?= $this->lang('ftr_contacts') ?></a>
				</div>
				<div id="sfright">
					<span style="color:#888;">
						Opentronix is Powered by <a href="http://blogtronix.com" target="_blank" style="color:#666;">Blogtronix</a>
					</span>
				</div>
			</div>
		</div>
		<div id="flybox_container" style="display:none;">
			<div class="flyboxbackgr"></div>
			<div class="flybox" id="flybox_box">
				<div class="flyboxttl">
					<div class="flyboxttl_left"><b id="flybox_title"></b></div>
					<div class="flyboxttl_right"><a href="javascript:;" title="<?= $this->lang('post_atchbox_close') ?>" onfocus="this.blur();" onclick="flybox_close();"></a></div>
				</div>
				<div class="flyboxbody"><div class="flyboxbody2" id="flybox_main"></div></div>
				<div class="flyboxftr"><div class="flyboxftr2"></div></div>
			</div>
		</div>
		<?php if( isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) { ?>
			<script type="text/javascript">
				try { FB.XFBML.Host.parseDomTree(); } catch(e) {}
			</script>
		<?php } ?>

		<?php
			// Important - do not remove this:
			$this->load_template('footer_cronsimulator.php');
			if( $C->DEBUG_MODE ) { $this->load_template('footer_debuginfo.php'); }
		?>

		<?php
			@include( $C->INCPATH.'../themes/include_in_footer.php' );
		?>

	</body>
</html>
