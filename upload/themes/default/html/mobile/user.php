<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div id="profilehdr">
		<img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$D->usr->avatar ?>" alt="<?= htmlspecialchars($D->usr->fullname) ?>" align="left" />
		<div id="profilehdrinfo">
			<h3><?= $D->usr->username ?></h3>
			<?= nl2br(htmlspecialchars($D->usr->position)) ?>
		</div>
		<div class="klear"></div>
	</div>
	<hr />
	<div id="select">
		<form method="get" action="<?= $C->SITE_URL.$D->usr->username ?>/">
			<select name="show" onchange="this.form.submit();">
				<option value="updates" <?= $D->show=='updates'?'selected="selected"':'' ?>><?= $this->lang('user_menu_updates') ?></option>
				<option value="info" <?= $D->show=='info'?'selected="selected"':'' ?>><?= $this->lang('user_menu_info') ?></option>
				<option value="groups" <?= $D->show=='groups'?'selected="selected"':'' ?>><?= $this->lang('user_menu_groups') ?></option>
				<option value="following" <?= $D->show=='following'?'selected="selected"':'' ?>><?= $this->lang('user_menu_following') ?></option>
				<option value="followers" <?= $D->show=='followers'?'selected="selected"':'' ?>><?= $this->lang('user_menu_followers') ?></option>
			</select>
			<input type="submit" value="<?= $this->lang('user_menu_submit') ?>" />
		</form>
	</div>
	<hr />
	<?php if( isset($_GET['do_follow']) ) { ?>
		<div class="okbox" style="margin-top:10px;"><?= $this->lang('user_dofollow', array('#USERNAME#'=>$D->usr->username)) ?></div>
		<hr />
	<?php } else if( isset($_GET['do_unfollow']) ) { ?>
		<div class="okbox" style="margin-top:10px;"><?= $this->lang('user_unfollow', array('#USERNAME#'=>$D->usr->username)) ?></div>
		<hr />
	<?php } ?>
	<?php if( $D->show == 'info' ) { ?>
		<?php if( !empty($D->usr->location) || !empty($D->birthdate) || !empty($D->i->website) || !empty($D->date_lastlogin) ) { ?>
		<table border="0" style="margin-top:2px; margin-bottom:2px;">
			<tr>
				<th align="left" colspan="2"><?= $this->lang('uinfo_sect_details') ?></th>
			</tr>
			<?php if( !empty($D->usr->location) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_location') ?></td>
				<td><?= htmlspecialchars($D->usr->location) ?></td>
			</tr>
			<?php } ?>
			<?php if( !empty($D->birthdate) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_birthday') ?></td>
				<td><?= $D->birthdate ?></td>
			</tr>
			<?php } ?>
			<?php if( !empty($D->i->website) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_site') ?></td>
				<td><a href="<?= htmlspecialchars($D->i->website) ?>" title="<?= htmlspecialchars($D->i->website) ?>" target="_blank"><?= htmlspecialchars(str_cut(preg_replace('/^(http(s)?|ftp)\:\/\/(www\.)?/','',$D->i->website),25)) ?></a></td>
			</tr>
			<?php } ?>
			<?php if( !empty($D->date_lastlogin) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_lastonline') ?></td>
				<td><?= $D->date_lastlogin ?></td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>
		<?php if( !empty($D->usr->email) || !empty($D->i->personal_email) || !empty($D->i->work_phone) || !empty($D->i->personal_phone) ) { ?>
		<table border="0" style="margin-top:2px; margin-bottom:2px;">
			<tr>
				<th align="left" colspan="2"><?= $this->lang('uinfo_sect_contacts') ?></th>
			</tr>
			<?php if( !empty($D->usr->email) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_email1') ?></td>
				<td><a href="mailto:<?= $D->usr->email ?>"><?= $D->usr->email ?></a></td>
			</tr>
			<?php } ?>
			<?php if( !empty($D->i->personal_email) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_email2') ?></td>
				<td><a href="mailto:<?= htmlspecialchars($D->i->personal_email) ?>"><?= htmlspecialchars($D->i->personal_email) ?></a></td>
			</tr>
			<?php } ?>
			<?php if( !empty($D->i->work_phone) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_phone1') ?></td>
				<td><?= htmlspecialchars($D->i->work_phone) ?></td>
			</tr>
			<?php } ?>
			<?php if( !empty($D->i->personal_phone) ) { ?>
			<tr>
				<td style="font-size:11px; color:#333;"><?= $this->lang('uinfo_phone2') ?></td>
				<td><?= htmlspecialchars($D->i->personal_phone) ?></td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>
	<?php } elseif( 0 == $D->num_results ) { ?>
		<div class="msgbox" style="margin-top:10px;"><?= $this->lang('user_errmsg_'.$D->show, array('#USERNAME#'=>$D->usr->username)) ?></div>
	<?php } elseif( $D->show == 'updates' ) { ?>
		<?= $D->posts_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('posts_paging_prev') ?></a> |
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('posts_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('posts_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('posts_paging_next') ?></a>
		</div>
		<?php } ?>
	<?php } elseif( $D->show == 'groups' ) { ?>
	<div id="members">
		<?= $D->groups_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('groups_paging_prev') ?></a> |
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('groups_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('groups_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('groups_paging_next') ?></a>
		</div>
		<?php } ?>
	</div>
	<?php } elseif( $D->show == 'followers' || $D->show == 'following' ) { ?>
	<div id="members">
		<?= $D->users_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('members_paging_prev') ?></a> |
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('members_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('members_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('members_paging_next') ?></a>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<hr />
	<?php if( ! $D->is_my_profile ) { ?>
	<div id="profileftr">
		<a href="<?= $C->SITE_URL.'newpost/touser:'.$D->usr->username ?>"><?= $this->lang('user_ftr_privmsg', array('#USERNAME#'=>$D->usr->username)) ?></a>
		<span>|</span>
		<?php if( ! $D->i_follow_him ) { ?>
		<a href="<?= $D->paging_url.$D->pg.'&do_follow=1' ?>" class="follow"><?= $this->lang('user_ftr_follow', array('#USERNAME#'=>$D->usr->username)) ?></a>
		<?php } else { ?>
		<a href="<?= $D->paging_url.$D->pg.'&do_unfollow=1' ?>" class="stopfollow" onclick="return confirm('<?= $this->lang('user_ftr_unfollow_c', array('#USERNAME#'=>$D->usr->username)) ?>');"><?= $this->lang('user_ftr_unfollow', array('#USERNAME#'=>$D->usr->username)) ?></a>
		<?php } ?>
	</div>
	<?php } ?>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>