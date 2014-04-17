<?php
/*

All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

*/
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function openbem_controller()
{
  global $session, $route, $mysqli;
  $result = false;
  $submenu = false;
  
  require "Modules/openbem/openbem_model.php";
  $openbem = new OpenBEM($mysqli);

  if ($route->format == 'html' && $session['write'])
  {
    if ($route->action=='projects') $result = view("Modules/openbem/SimpleMonthly/projects_view.php",array());    
    if ($route->action=='project') $result = view("Modules/openbem/SimpleMonthly/project_view.php",array('project_id'=>get('project_id')));

        
    if ($route->action=='monthly') $result = view("Modules/openbem/SimpleMonthly/monthly.php",array('project_id'=>get('project_id'),'scenario_id'=>get('scenario_id')));
    
    if ($route->action=='compare') $result = view("Modules/openbem/SimpleMonthly/compare.php",array('project_id'=>get('project_id'),'scenarioA'=>get('scenarioA'),'scenarioB'=>get('scenarioB')));


    //if ($route->action=='compare') $result = view("Modules/openbem/SimpleMonthly/compare.php",array('building'=>$building));
    
    //if ($route->action=='measures') $result = view("Modules/openbem/SimpleMonthly/measures.php",array('building'=>$building));
  }

  /*
  if ($route->format == 'json' && $session['write'])
  {  
  
    if ($route->action == 'savemonthly')
    {
      $result = false;
      $data = null;
      
      // From post or get
      if (isset($_POST['data'])) $data = $_POST['data'];
      if (!isset($_POST['data']) && isset($_GET['data'])) $data = $_GET['data'];
      
      // if there is indeed data to be saved
      if ($data && $data!=null) {

        // and we have a write session then save it to db
        if ($session['write']) {
          $result = $openbem->save_monthly($session['userid'],post('building'),$data);
        } else {
          // ELSE Save in session data
          // $result = true;
          // $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);
          // $_SESSION['sapdata'] = $data;
        }

      }
    }
    */
    
    //if ($route->action == 'getscenario' && $session['write'] $result = json_decode($openbem->get_monthly($session['userid'], get('scenario'))); 
  if ($route->format == 'json' && $session['write'])
  {
    if ($route->action == 'getprojects') $result = $openbem->get_projects($session['userid']);
    if ($route->action == 'getprojectdetails') $result = $openbem->get_project_details($session['userid'],get('project_id'));
    if ($route->action == 'addproject') $result = $openbem->add_project($session['userid'],get('name'),get('description'));
    if ($route->action == 'deleteproject') $result = $openbem->delete_project($session['userid'],get('projectid'));
    if ($route->action == 'getproject') $result = $openbem->get_project_scenarios($session['userid'],get('project_id'));
    
    if ($route->action == 'getscenarios') $result = $openbem->get_scenarios(get('project_id'));
    if ($route->action == 'addscenario') $result = $openbem->add_scenario(get('project_id'),get('meta'));
    if ($route->action == 'clonescenario') $result = $openbem->clone_scenario(get('project_id'),get('scenario_id'));

    
    if ($route->action == 'getscenario') $result = $openbem->get_scenario(get('scenario_id'));
    if ($route->action == 'savescenario')
    {
      $result = false;
      $data = null;
      
      // From post or get
      if (isset($_POST['data'])) $data = $_POST['data'];
      if (!isset($_POST['data']) && isset($_GET['data'])) $data = $_GET['data'];
      
      // if there is indeed data to be saved
      if ($data && $data!=null) {

        // and we have a write session then save it to db
        if ($session['write']) {
          $result = $openbem->save_scenario(post('scenario_id'),$data);
        } else {
          // ELSE Save in session data
          // $result = true;
          // $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);
          // $_SESSION['sapdata'] = $data;
        }

      }
    }
  }

  return array('content'=>$result);
}
