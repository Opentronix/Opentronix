			<a href="<?= $C->SITE_URL.$D->u->username ?>">
				<img src="<?= $C->IMG_URL.'avatars/thumbs1/'.$D->u->avatar ?>"/>
				<div>
					<strong><?= htmlspecialchars($D->u->username) ?></strong>
					<?= htmlspecialchars($D->u->fullname) ?>
				</div>
				<span></span>	
			</a>