<?= $this->lang('signup_email_hello') ?>

<?= $this->lang('signup_email_message', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL)) ?>
<?= $D->activation_link ?>

<?= $this->lang('signup_email_warning', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL)) ?>

<?= $this->lang('signup_email_signature', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#SITE_URL#'=>$C->SITE_URL)) ?>