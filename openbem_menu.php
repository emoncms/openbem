<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/feed/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  $menu_left[] = array('name'=> dgettext($domain, "OpenBEM"), 'path'=>"openbem/projects" , 'session'=>"write", 'order' => 0 );

?>
