<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/feed/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  $menu_dropdown[] = array('name'=> dgettext($domain, "OpenBEM"), 'path'=>"openbem/monthly" , 'session'=>"write", 'order' => 0 );

?>
