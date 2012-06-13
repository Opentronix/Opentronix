								<div class="group">
									<?php if( $D->g->is_private ) { ?>
									<div class="pgavatar" style="background-image: url('<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->g->avatar ?>');"><a href="<?= $C->SITE_URL ?><?= $D->g->groupname ?>" title="<?= htmlspecialchars($D->g->title) ?>"></a></div>
									<?php } else { ?>
									<a href="<?= userlink($D->g->groupname) ?>" class="groupavatar" title="<?= htmlspecialchars($D->g->title) ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->g->avatar ?>" style="width:50px; height:50px;" /></a>
									<?php } ?>
									<div class="groupinfo">
										<a href="<?= userlink($D->g->groupname) ?>" class="groupname" title="<?= htmlspecialchars($D->g->title) ?>"><?= htmlspecialchars(str_cut($D->g->title,18)) ?></a>
										<div class="followbtnsbox">
										<?php if( ! $this->user->is_logged ) { ?>
										<?php } elseif( ! $this->user->if_follow_group($D->g->id)) { ?>
											<a href="javascript:;" id="grp_followbtn_<?= $D->g->id ?>" onclick="group_follow('<?= $D->g->groupname ?>',this,'grp_unfollowbtn_<?= $D->g->id ?>','<?= addslashes($this->lang('msg_follow_group_on',array('#GROUP#'=>$D->g->title))) ?>');" class="followusr" onfocus="this.blur();"><b><?= $this->lang('grplist_tltp_follow') ?></b></a>
											<a href="javascript:;" style="display:none;" id="grp_unfollowbtn_<?= $D->g->id ?>" onclick="group_unfollow('<?= $D->g->groupname ?>',this,'grp_followbtn_<?= $D->g->id ?>','<?= addslashes($this->lang('group_unfollow_confirm',array('#GROUP#'=>$D->g->title))) ?>','<?= addslashes($this->lang('msg_follow_group_off',array('#GROUP#'=>$D->g->title))) ?>');" class="unfollowusr" onfocus="this.blur();"><b><?= $this->lang('grplist_tltp_unfollow') ?></b></a>
										<?php } else { ?>
											<a href="javascript:;" style="display:none;" id="grp_followbtn_<?= $D->g->id ?>" onclick="group_follow('<?= $D->g->groupname ?>',this,'grp_unfollowbtn_<?= $D->g->id ?>','<?= addslashes($this->lang('msg_follow_group_on',array('#GROUP#'=>$D->g->title))) ?>');" class="followusr" onfocus="this.blur();"><b><?= $this->lang('grplist_tltp_follow') ?></b></a>
											<?php if( $this->user->if_can_leave_group($D->g->id) ) { ?>
											<a href="javascript:;" id="grp_unfollowbtn_<?= $D->g->id ?>" onclick="group_unfollow('<?= $D->g->groupname ?>',this,'grp_followbtn_<?= $D->g->id ?>','<?= addslashes($this->lang('group_unfollow_confirm',array('#GROUP#'=>$D->g->title))) ?>','<?= addslashes($this->lang('msg_follow_group_off',array('#GROUP#'=>$D->g->title))) ?>');" class="unfollowusr" onfocus="this.blur();"><b><?= $this->lang('grplist_tltp_unfollow') ?></b></a>
											<?php } else { ?>
											<div id="grp_unfollowbtn_<?= $D->g->id ?>" style="display:none; visiblity:hidden; position:absolute; z-index:-1;"></div>
											<?php } ?>
										<?php } ?>
										</div>
										<div class="groupdesc" title="<?= htmlspecialchars($D->g->about_me) ?>"><?= htmlspecialchars(str_cut($D->g->about_me,30)) ?></div>
										<div class="grouptext">
											<?= $D->g->num_followers ?> <?= $this->lang($D->g->num_followers==1?'grplist_numfollowers1':'grplist_numfollowers') ?>,
											<?= $D->g->num_posts ?> <?= $this->lang($D->g->num_posts==1?'grplist_numposts1':'grplist_numposts') ?>
										</div>
									</div>
								</div>