								<div class="group">				
									<a href="<?= userlink($D->u->username) ?>" class="groupavatar" title="<?= htmlspecialchars($D->u->fullname) ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->u->avatar ?>" style="width:50px; height:50px;" /></a>
									<div class="groupinfo">
										<a href="<?= userlink($D->u->username) ?>" class="groupname" title="<?= htmlspecialchars($D->u->fullname) ?>"><?= str_cut($D->u->username,18) ?></a>
										<div class="followbtnsbox">
										<?php if( ! $this->user->is_logged ) { ?>
										<?php } elseif($D->u->id!=$this->user->id && !$this->user->if_follow_user($D->u->id)) { ?>
											<a href="javascript:;" id="usr_followbtn_<?= $D->u->id ?>" onclick="user_follow('<?= $D->u->username ?>',this,'usr_unfollowbtn_<?= $D->u->id ?>','<?= addslashes($this->lang('msg_follow_user_on',array('#USERNAME#'=>$D->u->username))) ?>');" class="followusr" onfocus="this.blur();"><b><?= $this->lang('usrlist_tltp_follow') ?></b></a>
											<a href="javascript:;" style="display:none;" id="usr_unfollowbtn_<?= $D->u->id ?>" onclick="user_unfollow('<?= $D->u->username ?>',this,'usr_followbtn_<?= $D->u->id ?>','<?= addslashes($this->lang('user_unfollow_confirm',array('#USERNAME#'=>$D->u->username))) ?>','<?= addslashes($this->lang('msg_follow_user_off',array('#USERNAME#'=>$D->u->username))) ?>');" class="unfollowusr" onfocus="this.blur();"><b><?= $this->lang('usrlist_tltp_unfollow') ?></b></a>
										<?php } elseif($D->u->id!=$this->user->id) { ?>
											<a href="javascript:;" style="display:none;" id="usr_followbtn_<?= $D->u->id ?>" onclick="user_follow('<?= $D->u->username ?>',this,'usr_unfollowbtn_<?= $D->u->id ?>','<?= addslashes($this->lang('msg_follow_user_on',array('#USERNAME#'=>$D->u->username))) ?>');" class="followusr" onfocus="this.blur();"><b><?= $this->lang('usrlist_tltp_follow') ?></b></a>
											<a href="javascript:;" id="usr_unfollowbtn_<?= $D->u->id ?>" onclick="user_unfollow('<?= $D->u->username ?>',this,'usr_followbtn_<?= $D->u->id ?>','<?= addslashes($this->lang('user_unfollow_confirm',array('#USERNAME#'=>$D->u->username))) ?>','<?= addslashes($this->lang('msg_follow_user_off',array('#USERNAME#'=>$D->u->username))) ?>');" class="unfollowusr" onfocus="this.blur();"><b><?= $this->lang('usrlist_tltp_unfollow') ?></b></a>
										<?php } ?>
										</div>
										<div class="groupdesc"><?= htmlspecialchars(str_cut($D->u->position,25)) ?></div>
										<div class="grouptext">
											<?= $D->u->num_followers ?> <?= $this->lang($D->u->num_followers==1?'usrlist_numfollowers1':'usrlist_numfollowers') ?>,
											<?= $D->u->num_posts ?> <?= $this->lang($D->u->num_posts==1?'usrlist_numposts1':'usrlist_numposts') ?>
										</div>
									</div>
								</div>