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

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/model/solar.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/solarhotwater/solarhotwater_model.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/Modules/solarhotwater/solarhotwater_controller.js"></script>


<h3>Solar Hot Water system</h3>
<div id="solarhotwater" ></div>

<script>
  var path = "<?php echo $path; ?>";
  
  var building = <?php echo $building; ?>;
  var inputdata = openbem.get(building);
  if (inputdata.solarhotwater!=undefined) solarhotwater_model.input = inputdata.solarhotwater;
  
  $("#solarhotwater").html(load_view('solarhotwater'));
  
  function load_view(view)
  {
    var result = "";
    $.ajax({url: path+"Modules/openbem/SimpleMonthly/Modules/"+view+"/"+view+"_view.html", async: false, success: function(data) {result = data;} });
    return result;
  }
  
  // link and default
  var i = solarhotwater_model.input;
  var o = solarhotwater_model.calc();
  update_view(i,o);
  solarhotwater_controller();
  
  function save(i)
  {
    inputdata.hotwater.solar_hot_water_contribution = o.Qs_monthly;
    inputdata.solarhotwater = i;
    openbem.save(building,inputdata);
  }
  
</script>
