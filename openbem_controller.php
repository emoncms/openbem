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
  global $session, $route, $redis, $mysqli,$path;
  $result = false;
  $submenu = false;
  $fw = false;
  
  require "Modules/openbem/openbem_model.php";
  $openbem = new OpenBEM($mysqli);

  if ($route->format == 'html')
  {
      if ($route->action=='projects' && $session['write']) $result = view("Modules/openbem/projects_view.php",array('project_id'=>(int)get('project_id'),'scenario_id'=>(int)get('scenario_id')));    
      if ($route->action=='project' && $session['write']) $result = view("Modules/openbem/project_view.php",array('project_id'=>(int)get('project_id')));
    
      if ($route->action=='monthly') {
          $result = view("Modules/openbem/openbem_view.php",array('project_id'=>(int)get('project_id'),'scenario_id'=>(int)get('scenario_id')));
          $fw = true;
      }
    
      if ($route->action=='compare' && $session['write']) {
        $result = view("Modules/openbem/compare.php",
            array(
                'project_id'=>(int)get('project_id'),
                'scenarioA'=>(int)get('scenarioA'),
                'scenarioB'=>(int)get('scenarioB')
            )
        );
      }
  }

  if ($route->format == 'json')
  {
    // Projects
    if ($route->action == 'getprojects' && $session['write']) $result = $openbem->get_projects($session['userid']);
    
    if ($route->action == 'getprojectdetails') {
        if ($session['write']) {
            $result = $openbem->get_project_details($session['userid'],get('project_id'));
        } else {
            $result = array(
                'project_id'=>0,
                'project_name'=>"Demo",
                'project_description'=>"Demo"
            );
        }
    }
    
    if ($route->action == 'addproject' && $session['write']) $result = $openbem->add_project($session['userid'],get('name'),get('description'));
    if ($route->action == 'deleteproject' && $session['write']) $result = $openbem->delete_project($session['userid'],get('projectid'));
    
    // Scenarios
    if ($route->action == 'getproject' && $session['write']) $result = $openbem->get_project_scenarios($session['userid'],get('project_id'));
    if ($route->action == 'getscenarios' && $session['write']) $result = $openbem->get_scenarios(get('project_id'));
    if ($route->action == 'addscenario' && $session['write']) $result = $openbem->add_scenario(get('project_id'),get('meta'));
    if ($route->action == 'clonescenario' && $session['write']) $result = $openbem->clone_scenario(get('project_id'),get('scenario_id'));
    if ($route->action == 'deletescenario' && $session['write']) $result = $openbem->delete_scenario(get('scenario_id'));

    // Model
    if ($route->action == 'getscenario') {
        if ($session['write']) {
            $result = $openbem->get_scenario(get('scenario_id'));
        } else {
        
            $result = array(
                'scenario_meta'=>array('name'=>"Demo", 'description'=>"Description", 'wk'=>"---"),
                'scenario_data'=>json_decode(file_get_contents($path."Modules/openbem/example.json"))
            );
        }
    }
    
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

  return array('content'=>$result, 'fullwidth'=>$fw);
}
