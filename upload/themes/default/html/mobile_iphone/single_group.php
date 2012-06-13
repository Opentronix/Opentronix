			<a href="<?= $C->SITE_URL.$D->g->groupname ?>">
				<img src="<?= $C->IMG_URL.'avatars/thumbs1/'.$D->g->avatar ?>"/>
				<div>
					<strong><?= htmlspecialchars($D->g->title) ?></strong>
					<?= $this->lang($D->g->is_private ? 'iphone_group_private' : 'iphone_group_public') ?>
					&middot;
					<?= $this->lang($D->g->num_followers==1 ? 'iphone_group_member1' : 'iphone_group_members', array('#NUM#'=>$D->g->num_followers)) ?>
				</div>
				<span></span>	
			</a>