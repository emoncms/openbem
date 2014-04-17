<?php

  $schema['openbem_projects'] = array(
    'project_id'=>array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
    'project_name'=>array('type' => 'varchar(30)'),
    'project_description'=>array('type'=>'text'),
    'project_owner'=>array('type'=>'int(11)'),
    'project_collaborators'=>array('type'=>'text'),
    'project_mdate'=>array('type'=>'int(11)')
  );

  $schema['openbem_scenarios'] = array(
    'scenario_id'=>array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
    'project_id'=>array('type'=>'int(11)'),
    'scenario_meta'=>array('type' => 'text'),
    'scenario_data'=>array('type'=>'text')
  );
  
  //$schema['openbem_activity'] = array(
  //  'project_id'
  //  'timestamp'
  //  'activity'
  //);

  $schema['openbem2'] = array(
    'userid'=>array('type'=>'int(11)','Null'=>'NO'),
    'name'=>array('type'=>'text'),
    'building'=>array('type'=>'int(11)','Null'=>'NO'),
    'monthly'=>array('type'=>'text'),
    'dynamic'=>array('type'=>'text')
  );

?>
