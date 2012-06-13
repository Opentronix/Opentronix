<?= nl2br($this->lang('signup_email_hello')) ?><br />
<br />
<?= nl2br($this->lang('signup_email_message', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<a href="<?= $D->activation_link ?>" target="_blank"><?= $D->activation_link ?></a><br />
<br />
<?= nl2br($this->lang('signup_email_warning', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<br />
<?= nl2br($this->lang('signup_email_signature', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<br />