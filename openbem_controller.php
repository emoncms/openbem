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

  if ($route->format == 'html')
  {
    if ($route->action=='heatpumpexplorer') $result = view("Modules/openbem/heatpump.php",array());
    if ($route->action=='heatingexplorer') $result = view("Modules/openbem/direct.php",array());
  }


  if ($route->format == 'html' && $session['write'])
  {
  
    $submenu = view("Modules/openbem/greymenu.php",array());
  
    if ($route->action == 'monthly') {
      if (!$route->subaction) $route->subaction = 1;
      $result = view("Modules/openbem/SimpleMonthly/monthly.php",array('building'=>$route->subaction));
    }
    
    if ($route->action == 'ventilation') {
      if (!$route->subaction) $route->subaction = 1;
      $result = view("Modules/openbem/SimpleMonthly/ventilation.php",array('building'=>$route->subaction));
    }

    if ($route->action == 'waterheating') {
      if (!$route->subaction) $route->subaction = 1;
      $result = view("Modules/openbem/SimpleMonthly/waterheating.php",array('building'=>$route->subaction));
    }

    if ($route->action == 'solarhotwater') {
      if (!$route->subaction) $route->subaction = 1;
      $result = view("Modules/openbem/SimpleMonthly/solarhotwater.php",array('building'=>$route->subaction));
    }
    
    if ($route->action == 'dynamic') {
      if (!$route->subaction) $route->subaction = 1;
      $result = view("Modules/openbem/DynamicCoHeating/dynamic.php",array('building'=>$route->subaction));
    }
    
  }

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
          $result = $openbem->save_monthly($session['userid'],get('building'),$data);
        } else {
          // ELSE Save in session data
          // $result = true;
          // $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);
          // $_SESSION['sapdata'] = $data;
        }

      }
    }
    
    if ($route->action == 'savedynamic')
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
          $result = $openbem->save_dynamic($session['userid'],get('building'),$data);
        } else {
          // ELSE Save in session data
          // $result = true;
          // $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);
          // $_SESSION['sapdata'] = $data;
        }

      }
    }
    
    if ($route->action == 'getmonthly' && $session['write'])
    {
      $result = json_decode($openbem->get_monthly($session['userid'], get('building'))); 
    } 
    
    if ($route->action == 'getdynamic' && $session['write'])
    {
      $result = json_decode($openbem->get_dynamic($session['userid'], get('building'))); 
    } 
    
  }

  return array('content'=>$result,'submenu'=>$submenu);
}
