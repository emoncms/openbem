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

    public function save_monthly($userid, $building, $data)
    {
        $userid = (int) $userid;
        $building = (int) $building;
        $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);

        $data = json_decode($data);

        // Dont save if json_Decode fails
        if ($data!=null) {

          $data = json_encode($data);
          $data = $this->mysqli->real_escape_string($data);

          $result = $this->mysqli->query("SELECT `building` FROM openbem WHERE `userid` = '$userid' AND `building` = '$building'");
          $row = $result->fetch_object();

          if (!$row)
          {
              $this->mysqli->query("INSERT INTO openbem (`userid`, `building`, `monthly`) VALUES ('$userid','$building','$data')");
          }
          else
          {
              $this->mysqli->query("UPDATE openbem SET `monthly` = '$data' WHERE `userid` = '$userid' AND `building` = '$building'");
          }
          return true;
        }
        else
        {
          return false;
        }
    }
    
    public function save_dynamic($userid, $building, $data)
    {
        $userid = (int) $userid;
        $building = (int) $building;
        $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);

        $data = json_decode($data);

        // Dont save if json_Decode fails
        if ($data!=null) {

          $data = json_encode($data);
          $data = $this->mysqli->real_escape_string($data);

          $result = $this->mysqli->query("SELECT `building` FROM openbem WHERE `userid` = '$userid' AND `building` = '$building'");
          $row = $result->fetch_object();

          if (!$row)
          {
              $this->mysqli->query("INSERT INTO openbem (`userid`, `building`, `dynamic`) VALUES ('$userid','$building','$data')");
          }
          else
          {
              $this->mysqli->query("UPDATE openbem SET `dynamic` = '$data' WHERE `userid` = '$userid' AND `building` = '$building'");
          }
          return true;
        }
        else
        {
          return false;
        }
    }

    public function get_monthly($userid,$building)
    {
        $userid = (int) $userid;
        $building = (int) $building;
        $result = $this->mysqli->query("SELECT `monthly` FROM openbem WHERE `userid` = '$userid' AND `building` = '$building'");
        $row = $result->fetch_array();
        if ($row && $row['monthly']!=null) return $row['monthly']; else return '0';
    }
    
    public function get_dynamic($userid,$building)
    {
        $userid = (int) $userid;
        $building = (int) $building;
        $result = $this->mysqli->query("SELECT `dynamic` FROM openbem WHERE `userid` = '$userid' AND `building` = '$building'");
        $row = $result->fetch_array();
        if ($row && $row['dynamic']!=null) return $row['dynamic']; else return '0';
    }
}
?>
