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

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/waterheating/waterheating_model.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/waterheating/waterheating_controller.js"></script>

<h3>Water heating</h3>
<div id="waterheating" ></div>

<script>
  var path = "<?php echo $path; ?>";
  
  var building = <?php echo $building; ?>;
  var inputdata = openbem.get(building);
  if (inputdata.hotwater!=undefined) hot_water_model.input = inputdata.hotwater;
  
  $("#waterheating").html(load_view('waterheating'));
  
  function load_view(view)
  {
    var result = "";
    $.ajax({url: path+"Modules/openbem/SimpleMonthly/Modules/"+view+"/"+view+"_view.html", async: false, success: function(data) {result = data;} });
    return result;
  }
  
  // link and default
  var i = hot_water_model.input;
  var o = hot_water_model.calc();
  update_view(i,o);

  hot_water_controller();
  
  function save(i)
  {
    // save output variables to solar hot water input values
    inputdata.solarhotwater.Vd_average = o.Vd_average;
    inputdata.solarhotwater.annual_hotwater_energy_content = o.annual_energy_content;
    
    inputdata.waterheating_gains = o.waterheating_gains;

    inputdata.hotwater = i;
    openbem.save(building,inputdata);
  }
  
</script>
