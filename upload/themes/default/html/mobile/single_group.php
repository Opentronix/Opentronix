<div class="<?= $D->g->list_index%2==0 ? 'post' : 'post dark' ?>" style="background-image: url('<?= $C->IMG_URL ?>avatars/thumbs3/<?= $D->g->avatar ?>');">
	<div class="postttl"><a href="<?= $C->SITE_URL.$D->g->groupname ?>" title="<?= htmlspecialchars($D->g->title) ?>" class="user"><?= htmlspecialchars($D->g->title) ?></a></div>
	<p class="message"><?= empty($D->g->about_me) ? '&nbsp;' : htmlspecialchars(str_cut($D->g->about_me,50)) ?></p>
</div>
<hr />