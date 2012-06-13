<div class="<?= $D->u->list_index%2==0 ? 'post' : 'post dark' ?>" style="background-image: url('<?= $C->IMG_URL ?>avatars/thumbs3/<?= $D->u->avatar ?>');">
	<div class="postttl"><a href="<?= $C->SITE_URL.$D->u->username ?>" title="<?= htmlspecialchars($D->u->fullname) ?>" class="user"><?= $D->u->username ?></a></div>
	<p class="message"><?= htmlspecialchars($D->u->fullname) ?></p>
</div>
<hr />