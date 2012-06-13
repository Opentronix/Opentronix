<?= $D->page->lang('eml_ntf_title', array('#USER#'=>$D->user->username)) ?>


<?= $D->message_txt ?>


<?= $D->page->lang('emltxt_ntf_editnotif', array('#A0#'=>$C->SITE_URL.'settings/notifications', '#A1#'=>'<a href="'.$C->SITE_URL.'settings/notifications" target="_blank">', '#A2#'=>'</a>')) ?>


<?= $D->page->lang('eml_ntf_signatr', array('#SITE_URL#'=>$C->SITE_URL)) ?>