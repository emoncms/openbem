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

<script type="text/javascript" src="<?php echo $sm; ?>interface/openbem.js"></script>
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
<script type="text/javascript" src="<?php echo $sm; ?>Modules/fuelcosts/fuelcosts_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/saprating/saprating_model.js"></script>

<script type="text/javascript" src="<?php echo $sm; ?>Modules/heatingsystem/heatingsystem_model.js"></script>

<script type="text/javascript" src="<?php echo $sm; ?>Modules/data/data_model.js"></script>
<script type="text/javascript" src="<?php echo $sm; ?>Modules/measures/measures_model.js"></script>
<ul class="nav nav-pills">
  <li><a href="<?php echo $path; ?>openbem/monthly/<?php echo $building; ?>">Simple Monthly</a></li>
  </li>
  <li>
  <a href="<?php echo $path; ?>openbem/dynamic/<?php echo $building; ?>">Dynamic Coheating</a>
  </li>
  <li>
  <a href="<?php echo $path; ?>openbem/heatingexplorer">Heating Explorer</a>
  </li>
</ul>

<div class="row">



  <div class="span3">

    <h3>OpenBEM</h3>

    <canvas id="after" width="269px" height="350px"></canvas>
    <br><br>
    <table class="table table-bordered">
    <tr><td><a href="<?php echo $path; ?>openbem/monthly/<?php echo $building; ?>"><b>Back to main model</b></a></td></tr>
    </table>
    
    <table class="table">
    <tr><td>Current energy cost</td><td>£<span id="current_energy_cost"></td></tr>
    <tr><td>After retrofit energy cost</td><td>£<span id="retrofit_energy_cost"></span></td></tr>
    <tr><td>Ten year saving</td><td>£<span id="tenyearsaving"></span></td></tr>
    <tr><td>Twenty five year saving</td><td>£<span id="twentyfiveyearsaving"></span></td></tr>
    <tr><td>Total cost of measures</td><td>£<span id="totalcostofmeasures"></span></td></tr>
    </table>
  </div>

  <div class="span9">
    <h3>Building Fabric Measures</h3>
    <p>Experimental measures explorer, costs are not accurate yet</p>
    <table class="table">
    <tr>
      <th>Current</th><th>Replaced with</th><th>Measure Cost</th><th>Apply</th>
    </tr>

    <tbody id="measures_list"></tbody>

    </table>
    

  </div>

</div>

<script>
  
  var path = "<?php echo $path; ?>";
  
  var building = <?php echo $building; ?>;
  
  var inputdata = openbem.get(building);
  var elements = inputdata.elements.input.list;
  
  var cleancopy = JSON.parse(JSON.stringify(inputdata));
  
  var current_energy_cost = inputdata.heatingsystem.output.total_cost;
  $("#current_energy_cost").html(current_energy_cost.toFixed(0));
  
  //var c=document.getElementById("before");
  //var ctx=c.getContext("2d");
  //draw_rating(ctx);
  
  var c=document.getElementById("after");
  var ctx=c.getContext("2d");
  
  
  
  // 1) Compile measures list
  
  var measures = [];
  for (z in elements)
  {
    var measure = false;
    
    var lib = elements[z].lib;
    var type = element_library[lib].type;

    if (type=='Floor' && elements[z].uvalue>0.25) {
      measure = { current: z, after: 'wall0006', cost: 20, area: elements[z].area, applied:true };
    }

    if (type=='Wall' && elements[z].uvalue>0.45) {
      measure = { current: z, after: 'wall0006', cost: 120, area: elements[z].area, applied:true };
    }

    if (type=='Roof' && elements[z].uvalue>0.25) {
      measure = { current: z, after: 'roof0005', cost: 6, area: elements[z].area, applied:true };
    }
    
    if (type=='Window' && elements[z].uvalue>1.3) {
      measure = { current: z, after: 'window0117', cost: "100", area: elements[z].area, applied:true };
    }
    
    if (measure) measures.push(measure);
  }

  // 3) Draw measures list
  var out = "";
  for (z in measures)
  {
    var lib = elements[measures[z].current].lib;
    out += "<tr><td><b>"+elements[measures[z].current].name+"</b><br>"+element_library[lib].description+"";
    out += "<br><i>u-value: "+elements[measures[z].current].uvalue+"</i>"
    out += "</td><td>";
    
    var lib = measures[z].after;
    
    out += "<b>"+element_library[lib].type+":</b> "+element_library[lib].description+"<br>";
    if (element_library[lib].type!='Window') {
      out += "<i>u-value: "+element_library[lib].uvalue+" k-value: "+element_library[lib].kvalue+"</i>";
    } else {
      out += "<i>u-value: "+element_library[lib].uvalue+" g: "+element_library[lib].g+" gL: "+element_library[lib].gL+" ff: "+element_library[lib].ff+"</i>";
    }

    var checked = ""; if (measures[z].applied) checked = 'checked';
    
    var measurecost = (measures[z].cost * measures[z].area);
    out += "</td><td>£"+measures[z].cost+"/m2 = £"+measurecost.toFixed(0)+"</td><td><input measureid="+z+" type='checkbox' "+checked+" / ></tr>";
  }
  $("#measures_list").html(out);

  apply_measures();
  calc_all();
  draw_rating(ctx);
  
  $("input[type=checkbox]").click(function(){
    var measureid = $(this).attr('measureid');
    measures[measureid].applied = $(this)[0].checked;
    
    inputdata = JSON.parse(JSON.stringify(cleancopy));
    elements = inputdata.elements.input.list;
    
    apply_measures();
    calc_all();
    draw_rating(ctx);
  });

  function apply_measures()
  {
    // 2) Apply measures
    var totalcostofmeasures = 0;
    for (z in measures)
    {
      var measure = measures[z];
      var type = element_library[measure.after].type;
      console.log(type);
      
      if (measure.applied) {
      
        if (type=='Window') {
          elements[measure.current].uvalue = element_library[measure.after].uvalue;
          elements[measure.current].kvalue = element_library[measure.after].kvalue;
          elements[measure.current].g = element_library[measure.after].g;
          elements[measure.current].gL = element_library[measure.after].gL;
          elements[measure.current].ff = element_library[measure.after].ff;
        } else {
          elements[measure.current].uvalue = element_library[measure.after].uvalue;
          elements[measure.current].kvalue = element_library[measure.after].kvalue;
        }
        
        var measurecost = (measure.cost * measure.area);
        totalcostofmeasures += measurecost;
      
      }
    }
    
    $("#totalcostofmeasures").html(totalcostofmeasures.toFixed(0));
  }
  

  function calc_all()
  {
    calc_module('measures');
    calc_module('context');
    calc_module('ventilation');
    calc_module('elements');
    calc_module('meaninternaltemperature');
    if (inputdata.LAC_enabled) calc_module('LAC');
    if (inputdata.solarhotwater_enabled) calc_module('solarhotwater');
    if (inputdata.waterheating_enabled) calc_module('waterheating');
    calc_module('balance');
    //calc_module('energyrequirements');
    calc_module('heatingsystem');
    //calc_module('fuelcosts');
    //calc_module('saprating');
    calc_module('data');
    
    
    var retrofit_energy_cost = inputdata.heatingsystem.output.total_cost;
    $("#retrofit_energy_cost").html(retrofit_energy_cost.toFixed(0));
    
    var tenyearsaving = 10 * (current_energy_cost - retrofit_energy_cost);
    var twentyfiveyearsaving = 25 * (current_energy_cost - retrofit_energy_cost); 
       
    $("#tenyearsaving").html(tenyearsaving.toFixed(0));
    $("#twentyfiveyearsaving").html(twentyfiveyearsaving.toFixed(0));
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
  
  function draw_rating(ctx)
  {
    var sap_rating = "?";
    var kwhm2 = "?";
    var letter = "";
    
    var kwhd = 0;
    var kwhdpp = 0;
    var color = 0;
    
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
