<?= nl2br($this->lang('signinforg_email_hello')) ?><br />
<br />
<?= nl2br($this->lang('signinforg_email_message', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<a href="<?= $D->recover_link ?>" target="_blank"><?= $D->recover_link ?></a><br />
<br />
<?= nl2br($this->lang('signinforg_email_warning', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<br />
<?= nl2br($this->lang('signinforg_email_signature', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<br />