<?php

  $dbv = "_v3";

  $schema['openbem_projects'.$dbv] = array(
    'project_id'=>array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
    'project_name'=>array('type' => 'varchar(30)'),
    'project_description'=>array('type'=>'text'),
    'project_owner'=>array('type'=>'int(11)'),
    'project_collaborators'=>array('type'=>'text'),
    'project_mdate'=>array('type'=>'int(11)')
  );

  $schema['openbem_scenarios'.$dbv] = array(
    'scenario_id'=>array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
    'project_id'=>array('type'=>'int(11)'),
    'scenario_meta'=>array('type' => 'text'),
    'scenario_data'=>array('type'=>'text')
  );

?>
