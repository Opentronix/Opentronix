<?php
	
	$this->load_langfile('mobile/footer.php');
	
?>
		<hr />
		
		<?php if( $this->user->is_logged ) { ?>
		<div id="ftr">
			<?php if( $this->request[0] != 'members' ) { ?>
			<a href="<?= $C->SITE_URL ?>members"><?= $this->lang('footer_nav_members') ?></a>
			<span>|</span>
			<?php } ?>
			<?php if( $this->request[0] != 'groups' ) { ?>
			<a href="<?= $C->SITE_URL ?>groups"><?= $this->lang('footer_nav_groups') ?></a>
			<span>|</span>
			<?php } ?>
			<a href="<?= $C->SITE_URL ?>signout" class="logout"><?= $this->lang('footer_nav_signout') ?></a>
		</div>
		<hr />
		<?php } ?>
		
		<div id="ftrtext">
			&copy; <a href="<?= $C->SITE_URL ?>"><?= $C->SITE_TITLE ?></a>
		</div>
		
		<script type="text/javascript"> footer_touchversion_link("<?= $this->lang('footer_mobi_touch') ?>"); </script>
		
	</body>
</html>