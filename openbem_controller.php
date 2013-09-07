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
  global $session, $route;
  $result = false;

  if ($route->format == 'html')
  {
    if ($route->action == 'view') $result = view("Modules/openbem/view.php",array());
    if ($route->action == 'graph') $result = view("Modules/openbem/graph.php",array());
    //$result = view("Modules/openbem/internaltemp.php",array());
  }

  return array('content'=>$result);
}
