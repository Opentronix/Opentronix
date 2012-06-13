							<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('adm_menu_title') ?></h3></div></div>
							<div class="sidenav">
								<a href="<?= $C->SITE_URL ?>admin/statistics" class="<?= $this->request[1]=='statistics' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_statistics') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/general" class="<?= $this->request[1]=='general' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_general') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/themes" class="<?= $this->request[1]=='themes' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_themes') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/networkbranding" class="<?= $this->request[1]=='networkbranding' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_networkbranding') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/termsofuse" class="<?= $this->request[1]=='termsofuse' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_termsofuse') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/administrators" class="<?= $this->request[1]=='administrators' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_administrators') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/editusers" class="<?= $this->request[1]=='editusers' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_editusers') ?></a>
								<a href="<?= $C->SITE_URL ?>admin/suspendusers" class="<?= $this->request[1]=='suspendusers'||$this->request[1]=='deleteuser' ? 'onsidenav' : '' ?>"><?= $this->lang('admmenu_suspendusers') ?></a>
							</div>