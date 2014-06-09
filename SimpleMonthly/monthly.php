<?php 
/*

All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

*/

global $path; 

$sm = $path."Modules/openbem/SimpleMonthly/";

?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/openbem.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>datasets/datasets.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>datasets/element_library.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/model/solar.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/model/windowgains.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/model/utilisationfactor.js"></script>


<script type="text/javascript" src="<?php echo $sm; ?>controller.js"></script>

<script type="text/javascript" src="<?php echo $sm; ?>Modules/context/context_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/elements/elements_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/ventilation/ventilation_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/waterheating/waterheating_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/solarhotwater/solarhotwater_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/LAC/LAC_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/meaninternaltemperature/meaninternaltemperature_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/balance/balance_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/energyrequirements/energyrequirements_model.js"></script>


<script type="text/javascript" src="<?php echo $sm; ?>Modules/heatingsystem/heatingsystem_model.js"></script>

<script type="text/javascript" src="<?php echo $sm; ?>Modules/fuelcosts/fuelcosts_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/saprating/saprating_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/data/data_model.js"></script>

<script type="text/javascript" src="<?php echo $sm; ?>Modules/appliancelist/appliancelist_model.js"></script>

<br>
<ul class="breadcrumb">
<li><a href="<?php echo $path; ?>openbem/projects">My Projects</a> <span class="divider">/</span></li>
<li><a href="<?php echo $path; ?>openbem/project?project_id=<?php echo $project_id; ?>" class="project_name"></a> <span class="divider">/</span></li>
<li class="active scenario_name">Scenario <?php echo $scenario_id; ?></li>
</ul>

<div class="row">

  <div class="span3">

    <h3>OpenBEM Scenario</h3>
    
    <p>Back to <a href="<?php echo $path; ?>openbem/projects">all projects</a></p>

    <canvas id="rating" width="269px" height="350px"></canvas>
    <br><br>
    <table class="table table-bordered">
    <tr><td><a class="menu" name="context">Floor Area and Volume</a></td></tr>
    <tr><td><a class="menu" name="elements">Building Fabric</a></td></tr>
    <tr><td><a class="menu" name="ventilation">Ventilation & Infiltration</a></td></tr>
    <tr><td><a class="menu" name="meaninternaltemperature">Internal Temperature</a></td></tr>
    <tr><td><a class="menu" name="balance">Heat balance</a></td></tr>
    <!--<tr><td><a class="menu" name="energyrequirements">Energy Requirements</a></td></tr>-->
    <tr><td><a class="menu" name="heatingsystem">Energy Requirements</a></td></tr>
    <!--<tr><td><a class="menu" name="fuelcosts">Fuel costs</a></td></tr>-->
    <!--<tr><td><a class="menu" name="saprating">SAP rating</a></td></tr>-->
    <tr><td><a class="menu" name="data">Export data</a></td></tr>
    </table>
    
    <h4>Optional modules</h4>
    <table class="table table-bordered">
    <tr><td><a class="menu" name="waterheating">SAP Water Heating gains</a></td>
    <td><i class="icon-trash remove-module" name="waterheating"></i></td></tr>
    <tr><td><a class="menu" name="solarhotwater">SAP Solar Hot Water gains</a></td>
    <td><i class="icon-trash remove-module" name="solarhotwater"></i></td></tr>
    <tr><td><a class="menu" name="LAC">SAP Lighting, Appliances<br>& Cooking gains</a></td>
    <td><i class="icon-trash remove-module" name="LAC"></i></td></tr>
    <tr><td><a class="menu" name="appliancelist">Appliance List</a></td>
    <td><i class="icon-trash remove-module" name="appliancelist"></i></td></tr>
    </table>
    
    
  </div>

  <div class="span9">
    <div id="placeholder" ></div>
  </div>

</div>

<script>
  var c=document.getElementById("rating");
  var ctx=c.getContext("2d");
  
  var path = "<?php echo $path; ?>";
  
  var scenario_id = <?php echo $scenario_id; ?>;
  var scenario = openbem.get_scenario(scenario_id);
  var inputdata = scenario.scenario_data;
  
  $(".scenario_name").html(scenario.scenario_meta.name);

  var project_id = <?php echo $project_id; ?>; 
  var project_details = openbem.getprojectdetails(project_id);
  $(".project_name").html(project_details.project_name);
  
  if (!inputdata) {
  
    inputdata = {};
  
    
    inputdata.occupancy = 0;
    inputdata.region = 0;
    inputdata.TFA = 0;
    inputdata.volume = 0;
    inputdata.altitude = 0;
    inputdata.MIT = [21,21,21, 21,21,21, 21,21,21, 21,21,21];
    inputdata.gains = {};
    inputdata.losses = {};

    inputdata.appliancelist_enabled = false;
    inputdata.LAC_enabled = false;
    inputdata.solarhotwater_enabled = false;
    inputdata.waterheating_enabled = false;
    
  }
  
  var i = {}; var o = {};
  
  load_module('balance');
  
  $(".menu").click(function()
  { 
    var module = $(this).attr('name');
    load_module(module);
  });
  
  $(".remove-module").click(function()
  {
    var name = $(this).attr('name');
    
    if (name=='appliancelist') {
        inputdata.appliancelist_enabled = false;
        delete inputdata.gains['appliancelistgains'];
    }
    
    if (name=='LAC') {
        inputdata.LAC_enabled = false;
        delete inputdata.gains['lighting'];
        delete inputdata.gains['appliances'];
        delete inputdata.gains['cooking'];
    }
    
    if (name=='solarhotwater') inputdata.solarhotwater_enabled = false;
    if (name=='waterheating') {
        inputdata.waterheating_enabled = false;
        delete inputdata.gains['waterheating'];
    }
    
    load_module('balance');
  });
  
  function load_module(module)
  { 
    if (module=='appliancelist') inputdata.appliancelist_enabled = true;
    if (module=='LAC') inputdata.LAC_enabled = true;
    if (module=='solarhotwater') inputdata.solarhotwater_enabled = true;
    if (module=='waterheating') inputdata.waterheating_enabled = true;
    calc_all();
    
    i = inputdata[module].input;
    o = inputdata[module].output;
    
    $("#placeholder").html(load_view(module));
    openbem_controller(module);
    var customcontroller = module+"_customcontroller";
    if (window[customcontroller]!=undefined) window[customcontroller](module);
    
    openbem_update_view(i,o);
    var customview = module+"_customview";
    if (window[customview]!=undefined) window[customview](i); 
    
    draw_rating(ctx);
    openbem.save_scenario(scenario_id,inputdata); 
  }
  
  function openbem_update(module)
  { 
    calc_all();
    
    o = inputdata[module].output;
    
    openbem_update_view(i,o);
    
    var customview = module+"_customview";
    if (window[customview]!=undefined) window[customview](i);
    
    draw_rating(ctx);
    
    openbem.save_scenario(scenario_id,inputdata); 
  }
  
  function calc_all()
  {
    calc_module('context');
    calc_module('ventilation');
    calc_module('elements');
    calc_module('meaninternaltemperature');
    if (inputdata.appliancelist_enabled) calc_module('appliancelist');
    if (inputdata.LAC_enabled) calc_module('LAC');
    if (inputdata.solarhotwater_enabled) calc_module('solarhotwater');
    if (inputdata.waterheating_enabled) calc_module('waterheating');
    calc_module('balance');
    //calc_module('energyrequirements');
    calc_module('heatingsystem');
    //calc_module('fuelcosts');
    //calc_module('saprating');
    calc_module('data');
  }
  
  function calc_module(module)
  {
    var modelname = module+"_model";
    var savetoinputdata = module+"_savetoinputdata";
    
    window[modelname].set_from_inputdata(inputdata);
    inputdata[module] = {
      input:window[modelname].input, 
      output:window[modelname].calc()
    };
    
    if (window[savetoinputdata]!=undefined) window[savetoinputdata](inputdata,inputdata[module].output); 
  }
  

  function load_view(view)
  {
    var result = ""; 
    $.ajax({url: path+"Modules/openbem/SimpleMonthly/Modules/"+view+"/"+view+"_view.html", async: false, cache: false, success: function(data) {result = data;} });
    return result;
  }
  
  function draw_rating(ctx)
  {
    var sap_rating = "?";
    var kwhm2 = "?";
    var letter = "";
    
    var kwhd = 0;
    var kwhdpp = 0;
    
    if (inputdata.heatingsystem!=undefined) {
      sap_rating = Math.round(inputdata.heatingsystem.output.sap_rating);
      var band = 0;
      for (z in ratings)
      {
        if (sap_rating>=ratings[z].start && sap_rating<=ratings[z].end) {band = z; break;}
      }
      color = ratings[band].color;
      letter = ratings[band].letter;
      sap_rating = sap_rating;
    }
    
    if (inputdata.heatingsystem!=undefined) {
      kwhm2 = inputdata.heatingsystem.output.total_primaryenergy_requirement / inputdata.TFA;
      if (isNaN(kwhm2)) kwhm2 = 0;
      kwhm2 = kwhm2.toFixed(0)+" kWh/m2";
      
      kwhd = inputdata.heatingsystem.output.total_primaryenergy_requirement / 365.0;
      kwhd = kwhd.toFixed(1)+" kWh/d";

      kwhdpp = inputdata.heatingsystem.output.total_primaryenergy_requirement / (365.0 * inputdata.occupancy);
      kwhdpp = kwhdpp.toFixed(1)+" kWh/d";
    }
    
    ctx.clearRect(0,0,269,350);
    
    ctx.fillStyle = color;
    ctx.strokeStyle = color;
    ctx.lineWidth = 3;
    ctx.fillRect(0,0,269,350);

    ctx.fillStyle = "rgba(255,255,255,0.6)";
    ctx.fillRect(0,0,269,350);
    ctx.strokeRect(0,0,269,350);
        
    var mid = 269 / 2;
    
    ctx.beginPath();
    ctx.arc(mid, mid, 100, 0, 2 * Math.PI, false);
    ctx.closePath();
    ctx.fillStyle = "rgba(255,255,255,0.6)";
    ctx.fill();
    ctx.stroke();
    
    ctx.fillStyle = color;
      
    ctx.textAlign = "center";
    
    ctx.font = "bold 22px arial";
    ctx.fillText("SAP",mid,90);  
    ctx.font = "bold 92px arial";
      
    ctx.fillText(sap_rating,mid,mid+30);

    ctx.font = "bold 22px arial";
    ctx.fillText(letter+" RATING",mid,mid+60);    
    ctx.font = "bold 32px arial";
    
    //ctx.shadowColor = "rgba(0,0,0,0.0)";
    //ctx.shadowOffsetX = 1; 
    //ctx.shadowOffsetY = 1; 
    //ctx.shadowBlur = 3;
    ctx.fillText(kwhm2,mid,280);
    
    ctx.font = "bold 18px arial";
    ctx.fillText("DAILY: "+kwhd,mid,308);

    ctx.font = "bold 18px arial";
    ctx.fillText("PER PERSON: "+kwhdpp,mid,336);
  }
  
</script>
