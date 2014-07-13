<?php
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class OpenBEM
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }
    
    public function get_projects($userid)
    {
        $userid = (int) $userid;
        $result = $this->mysqli->query("SELECT * FROM openbem_projects WHERE project_owner = '$userid'");
        
        $projects = array();
        
        while($row = $result->fetch_object())
        {
            $projects[] = $row;
        }
        
        return $projects;
    }
    
    public function get_project_details($userid, $project_id)
    {
        $userid = (int) $userid;
        $project_id = (int) $project_id;
        $result = $this->mysqli->query("SELECT * FROM openbem_projects WHERE project_owner = '$userid' AND project_id = '$project_id'");
        return $result->fetch_object();
    }
    
    public function add_project($userid,$name,$description)
    {
        $userid = (int) $userid;
        
        $project_mdate = time();
        
        $result = $this->mysqli->query("INSERT INTO openbem_projects (`project_name`,`project_description`,`project_owner`,`project_mdate`) VALUES ('$name','$description','$userid','$project_mdate')");
        $project_id = $this->mysqli->insert_id;
        return $project_id;
    }

    public function delete_project($userid,$project_id)
    {
        $project_owner = (int) $userid;
        $project_id = (int) $project_id;
        
        $result = $this->mysqli->query("DELETE FROM openbem_projects WHERE `project_id`='$project_id' AND `project_owner`='$project_owner'");
        
        if ($this->mysqli->affected_rows==1) {
            $result = $this->mysqli->query("DELETE FROM openbem_scenarios WHERE `project_id`='$project_id'");  
            return true;
        }      
        
        return false;
    }
    
    public function get_scenarios($project_id)
    {
        $result = $this->mysqli->query("SELECT `scenario_id`,`scenario_meta` FROM openbem_scenarios WHERE `project_id` = '$project_id' ORDER BY scenario_id ASC");
        $scenarios = array();
        
        while($row = $result->fetch_object())
        {
            $row->scenario_meta = json_decode($row->scenario_meta);
            $scenarios[] = $row;
        }
        
        // if (count($scenarios)==0) $this->add_scenario($project_id);
        
        return $scenarios;
    }
    
    public function add_scenario($projectid,$meta)
    {
        $meta = json_decode($meta);
        if ($meta==null) return false;
        $meta = json_encode($meta);
        
        $data = false;
        $this->mysqli->query("INSERT INTO openbem_scenarios (`project_id`,`scenario_meta`,`scenario_data`) VALUES ('$projectid','$meta','$data')");
        $new_scenario_id = $this->mysqli->insert_id;
        return $new_scenario_id;
    }
    
    public function clone_scenario($projectid,$scenario_id)
    {
        // 1) Get data from scenario to clone
        $result = $this->mysqli->query("SELECT `scenario_data`, `scenario_meta` FROM openbem_scenarios WHERE `scenario_id` = '$scenario_id'");
        $row = $result->fetch_array();
        $data = $row['scenario_data'];
        $meta = json_decode($row['scenario_meta']);
        $meta->name = "Copy of ".$meta->name;
        $meta = json_encode($meta);
        
        // 2) Insert data in new scenario
        $this->mysqli->query("INSERT INTO openbem_scenarios (`project_id`,`scenario_meta`,`scenario_data`) VALUES ('$projectid','$meta','$data')");
        $new_scenario_id = $this->mysqli->insert_id;
        
        return $new_scenario_id;
    }
    
    public function delete_scenario($scenario_id)
    {
        $result = $this->mysqli->query("DELETE FROM openbem_scenarios WHERE `scenario_id` = '$scenario_id'");
        
        return array("Deleted");
    }

    public function get_scenario($scenario_id)
    {
        $scenario_id = (int) $scenario_id;
        $result = $this->mysqli->query("SELECT `scenario_meta`,`scenario_data` FROM openbem_scenarios WHERE `scenario_id` = '$scenario_id'");
        
        $row = $result->fetch_object();
        
        $row->scenario_meta = json_decode($row->scenario_meta);
        $row->scenario_data = json_decode($row->scenario_data);
        
        return $row;
        
    }
    
    public function save_scenario($scenario_id,$data)
    {
        $scenario_id = (int) $scenario_id;
        $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);
        $data = json_decode($data);

        // Dont save if json_decode fails
        if ($data!=null) {

          $data = json_encode($data);
          $data = $this->mysqli->real_escape_string($data);

          $this->mysqli->query("UPDATE openbem_scenarios SET `scenario_data` = '$data' WHERE `scenario_id` = '$scenario_id'");
          if ($this->mysqli->affected_rows==1) return true; else return false;
          
        }
        else
        {
          return false;
        }

    }
}
?>
