<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/feed/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  $menu_dropdown[] = array('name'=> dgettext($domain, "OpenBEM v3"), 'path'=>"openbem/projects" , 'session'=>"write", 'order' => 8 );

?>
