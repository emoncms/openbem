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
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/LAC/LAC_model.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/LAC/LAC_controller.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/datasets/element_library.js"></script>

<h3>Lighting, Appliances and Cooking Gains</h3>
<div id="LAC" ></div>

<script>
  var path = "<?php echo $path; ?>";
  
  var building = <?php echo $building; ?>;
  var inputdata = openbem.get(building);
  if (inputdata.LAC!=undefined) LAC_model.input = inputdata.LAC;
  
  LAC_model.input.elements = inputdata.elements;
  
  $("#LAC").html(load_view('LAC'));
  
  function load_view(view)
  {
    var result = "";
    $.ajax({url: path+"Modules/openbem/SimpleMonthly/Modules/"+view+"/"+view+"_view.html", async: false, success: function(data) {result = data;} });
    return result;
  }
  
  // link and default
  var i = LAC_model.input;
  var o = LAC_model.calc();
  update_view(i,o);
  LAC_controller();
  
  function save(i)
  {
    // inputdata.hotwater.solar_hot_water_contribution = o.Qs_monthly;
    inputdata.LAC = i;
    openbem.save(building,inputdata);
  }
  
</script>
