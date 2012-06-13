<?= nl2br($this->lang('prof_changemail_hello')) ?><br />
<br />
<?= nl2br($this->lang('prof_changemail_message', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<a href="<?= $D->confirmation_link ?>" target="_blank"><?= $D->confirmation_link ?></a><br />
<br />
<?= nl2br($this->lang('prof_changemail_warning', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<br />
<?= nl2br($this->lang('prof_changemail_signature', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL))) ?><br />
<br />