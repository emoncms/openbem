<?php 
/*

All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

*/

global $path; ?>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/interface/openbem.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/datasets/datasets.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/ventilation/ventilation_model.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/ventilation/ventilation_controller.js"></script>

<h3>Ventilation & infiltration</h3>
<div id="ventilation" ></div>

<script>

  var building = <?php echo $building; ?>;
  
  var inputdata = openbem.get(building);
  
  console.log(inputdata);
  
  var path = "<?php echo $path; ?>";
  
  $("#ventilation").html(load_view('ventilation'));
  ventilation_controller();
  
  function load_view(view)
  {
    var result = "";
    $.ajax({url: path+"Modules/openbem/SimpleMonthly/Modules/"+view+"/"+view+"_view.php", async: false, success: function(data) {result = data;} });
    return result;
  }
  
</script>
