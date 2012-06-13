<?= nl2br($D->page->lang('eml_ntf_title', array('#USER#'=>$D->user->username))) ?><br />
<br />
<?= nl2br($D->message_html) ?><br />
<br />
<?= nl2br($D->page->lang('emlhtml_ntf_editnotif', array('#A0#'=>$C->SITE_URL.'settings/notifications', '#A1#'=>'<a href="'.$C->SITE_URL.'settings/notifications" target="_blank">', '#A2#'=>'</a>'))) ?><br />
<br />
<?= nl2br($D->page->lang('eml_ntf_signatr', array('#SITE_URL#'=>$C->SITE_URL))) ?><br />