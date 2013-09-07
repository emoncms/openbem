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
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/element_library.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/solar.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/windowgains.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/datasets.js"></script>
<br>

<style>
  #element-totals td:nth-of-type(1) {  text-align: right; }
  #element-totals td:nth-of-type(2) { width:100px; text-align: center;}

  #element-table th:nth-of-type(3) { text-align: center;}
  #element-table th:nth-of-type(4) { text-align: center;}
  #element-table th:nth-of-type(5) { width:100px; text-align: center;}
  
  #element-table td:nth-of-type(3) { text-align: center;}
  #element-table td:nth-of-type(4) { text-align: center;}
  #element-table td:nth-of-type(5) { width:100px; text-align: center;}
</style>

<ul class="nav nav-pills">
  <li class="active">
    <a href="#">OpenBEM</a>
  </li>
  <li><a href="graph">Simulation</a></li>
</ul>


<h1>OpenBEM</h1>
<p>An open source simple building energy model based on SAP 2012</p>

<h3>Ventilation & infiltration</h3>


<div class="accordion">
  <div class="accordion-group">
    <div class="accordion-heading" style="background-color: rgb(217, 237, 247);">
      <a class="accordion-toggle" data-toggle="collapse" href="#collapseOne">
      <i class="icon-info-sign"></i> Ventilation & infiltration (Click to hide/show)
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse">
      <div class="accordion-inner" style="background-color: rgba(217, 237, 247,0.5);">
        <p>Ventilation and infiltration, the movement of heated air from inside the house into its surroundings is one of the main sources of heat loss.</p>

        <p>The rate of air movement is typically measured in air-changes per hour. An air-change is when the full volume of air inside a house is replaced with a new volume of air. This happens suprisingly frequently. The heat lost is equall to the energy stored in the warm air relative to the external temperature</p>

        <p>Typically the number of air-changes per hour will be approximately 4 for an old, undraughtstripped house, 1 to 2 for an average modern house and 0.6 for a very tight, superinsulated house.</p>

        <p>The best way to accuratly measure the air-changes per hour rate is via an air-tightness test. The result of such a test will give you a air permeability value, q50, expressed in cubic metres per hour per square metre of envelope area</p><p>If you have an air-tightness test result select air-tightness test and enter your q50 value.</p>

        <p>The SAP worksheet gives a method for estimating air-changes per hour but it may be more accurate for newer houses as the results are typically around 1 to 2 airchanges per hour.</p>
      </div>
    </div>
  </div>
</div>

<div class="input-prepend">
<span class="add-on">Select input source: </span>
<select>
  <option>Air-tightness test</option>
  <option>Simple air-change slider</option>
  <option>SAP 2012 Worksheet</option>
</select>

<span class="add-on" style="margin-left:20px">Air change rate:</span> 
<input type="text" placeholder="3">
<span class="add-on">ACH</span> 
</div>

<h3>Building elements</h3>
<p>Add all the floor, roof, wall and window elements that describe your building:</p>

<ul class="nav nav-tabs">

  <li class="pull-right"><a>Thermal capacity</a></li>
  <li class="active  pull-right">
    <a>U-values</a>
  </li>
  <li class="disabled  pull-right"><a>Show: </a></li>
  
</ul>

<table id="element-table" class="table">
<tr><th></th><th>Element</th><th>Area</th><th>U-value</th><th>W/K</th></tr>
<tbody id="elements"></tbody>
</table>

<br>
<table id="element-totals" class="table">
<tr><td>Fabric heat loss W/K</td><td><span id="total_fabric_heat_loss_WK"></span> W/K</td></tr>
<tr><td>Infiltration heat loss W/K</td><td><span id="infiltration_heat_loss_WK"></span> W/K</td></tr>
<tr><td>Total heat loss W/K</td><td><span id="total_heat_loss_WK"></span> W/K</td></tr>
<tr><td>Total thermal capacity kJ/K</td><td><span id="total_thermal_capacity"></span> kJ/K</td></tr>
</table>


<ul class="nav nav-tabs">
<li class="disabled"><a>Calculate:</a></li>
  <li class="active">
    <a>Heating demand from internal temperature</a>
  </li>
  <li><a>Internal temperature from heating input</a></li>
</ul>

<div class="input-prepend">
<span class="add-on">Mean internal temperature source: </span>
<select>
  <option>Enter manually</option>
  <option>SAP 2012 based standard heating schedule</option>
</select>
</div>
    
<table class="table">
<tr>
  <td></td>
  <td>Jan</td>
  <td>Feb</td>
  <td>Mar</td>
  <td>Apr</td>
  <td>May</td>
  <td>Jun</td>
  <td>Jul</td>
  <td>Aug</td>
  <td>Sep</td>
  <td>Oct</td>
  <td>Nov</td>
  <td>Dec</td>
  <td><b>Average</b></td>
</tr>

<tbody id="external_temperature"></tbody>
<tbody id="internal_temperature"></tbody>
<tbody id="temperature_difference"></tbody>
<tbody id="heat_demand"></tbody>
<tbody id="solargains"></tbody>
<tbody id="internal_gains"></tbody>
<tbody id="heating_system_demand" style="background-color:#eee"></tbody>
</table>

<table class="table">
<tr><td>Annual heating demand</td><td><span id="annual_heating_demand"></span> kWh</td></tr>
<tr><td>Heating system efficiency</td><td><span id="heating_system_efficiency">82</span> %</td></tr>
<tr><td>Annual fuel input</td><td><span id="annual_fuel_input"></span> kWh</td></tr>
<tr><td>Fuel cost</td><td><span id="fuel_cost">0.05</span> £/kWh</td></tr>
<tr><td>Annual heating cost</td><td>£ <span id="annual_heating_cost"></span></td></tr>
<tr><td>Annual energy costs (+internal gain sources)</td><td>£ <span id="annual_energy_cost"></span></td></tr>
<tr class="sap_rating"><td>SAP Rating</td><td><span id="sap_rating"></span></td></tr>
</table>

<script>


var region = 13;

var airchanges = 3;
var volume = ((3.5*1.9) + 2.54) * 6.0;
var infiltration_WK = airchanges * volume * 0.33;
$("#infiltration_heat_loss_WK").html(infiltration_WK.toFixed(0));

var mean_internal_temperature = [18,18,18,18,18,18,18,18,18,18,18,18];

var elements = [

  {name:"Main Floor", lib: 'floor0004', area: (6*3.5)},
  {name:"Front wall", lib: 'wall0008', area: (6*1.9)},
  {name:"Back wall", lib: 'wall0008', area: (6*1.9)},

  // 1.45m  
  {name:"Left wall", lib: 'wall0008', area: (3.5*1.9) + 2.54},
  {name:"Right wall", lib: 'wall0004', area: (3.5*1.9) + 2.54},
  
  // hyp 2.27m 
  {name:"Roof", lib: 'roof0002', area: 2*(2.27*6)},
  
  {name:"Front window", lib: 'window0121', area: (0.87*0.9), orientation: 3, overshading: 3},
  {name:"Roof window Front", lib: 'window0001', area: (2.45*0.42), orientation: 3, overshading: 3},
  {name:"Roof window Back", lib: 'window0001', area: (2.45*0.42), orientation: 3, overshading: 2},  
  
];

var total_fabric_heat_loss_WK = 0;
var total_thermal_capacity = 0;

var windows = [];

var out = "";
for (i in elements)
{
  var id = elements[i].lib;
  out += "<tr>";
  
  // Only draws type first time it gets that element type in the list
  // makes for nicer looking layout, relies upon ordered elements list
  if (i>0) {
    var lasttype = element_library[elements[i-1].lib].type;
    if (element_library[id].type!=lasttype) {
      out += "<td><b>"+element_library[id].type+"</b></td>";
    } else {
      out += "<td></td>";
    }
  } else {
    out += "<td><b>"+element_library[id].type+"</b></td>";
  }
  
  
  out += "<td><b>"+elements[i].name+"</b><br><i>";
  for (z in element_library[id])
  {
    if (z=='description') out += element_library[id][z]+", ";
    if (z=='uvalue') out += "U-value: "+element_library[id][z]+", ";
    if (z=='kvalue') out += "k-value: "+element_library[id][z];
    if (z=='g') out += "g: "+element_library[id][z]+", ";
    if (z=='gL') out += "gL: "+element_library[id][z]+", ";
    if (z=='ff') out += "Frame factor: "+element_library[id][z];
  }
  out +="</i></td>";
  
  out += "<td>"+elements[i].area.toFixed(1)+"m<sup>2</sup></td>";
  out += "<td>"+element_library[id].uvalue+"</td>";
  out += "<td>"+(element_library[id].uvalue*elements[i].area).toFixed(1)+" W/K</td>";
  out += "</tr>";
  
  total_fabric_heat_loss_WK += element_library[id].uvalue*elements[i].area;
  if (element_library[id].kvalue!=undefined) total_thermal_capacity += element_library[id].kvalue*elements[i].area;
  
  // Create specific windows array to pass to solar gains calculation
  if (element_library[id].type=='Window') {
    windows.push({orientation:elements[i].orientation, area:elements[i].area, overshading: elements[i].overshading, g: element_library[id].g, ff: element_library[id].ff});
  }
}

$("#elements").html(out);
$("#total_fabric_heat_loss_WK").html(total_fabric_heat_loss_WK.toFixed(0));
$("#total_heat_loss_WK").html((total_fabric_heat_loss_WK+infiltration_WK).toFixed(0));
$("#total_thermal_capacity").html(total_thermal_capacity.toFixed(0));


// Display monthly solar gains and calculate average
var solargains = calc_solar_gains_from_windows(windows,region);

var sum = 0;
var out = "<td> <b>-</b> Solar Gains: </td>";
for (z in solargains)
{
  out += "<td>"+solargains[z].toFixed(0)+"W</td>";
  sum += solargains[z];
}
out += "<td><b>"+(sum/12.0).toFixed(0)+"W</b></td>";
$("#solargains").html(out);


// External, Internal, Difference, Heat demand
var external_temperature_html = "<td>External Temperature: </td>";
var internal_temperature_html = "<td>Internal Temperature: </td>";
var temperature_difference_html = "<td>Temperature Difference: </td>";
var heat_demand_html = "<td>Total heat demand: </td>";
var internal_gains_html = "<td> <b>-</b> Internal Gains: </td>";
var heating_system_demand_html = "<td>Heating system demand: </td>";

var sum = 0;
for (var m=0; m<12; m++)
{
  // calculations
  var temperature_difference = mean_internal_temperature[m] - table_u1[region][m];
  var heat_demand = temperature_difference * (total_fabric_heat_loss_WK + infiltration_WK);
  var heating_system_demand = heat_demand - solargains[m] - 0;
  sum += heating_system_demand;
  
  // View outputs
  external_temperature_html += "<td>"+table_u1[region][m].toFixed(1)+"C</td>";
  internal_temperature_html += "<td>"+mean_internal_temperature[m].toFixed(1)+"C</td>";
  temperature_difference_html += "<td>"+(temperature_difference).toFixed(1)+"C</td>";
  heat_demand_html += "<td>"+(heat_demand).toFixed(0)+"W</td>";
  internal_gains_html += "<td>"+(0).toFixed(0)+"W</td>";
  heating_system_demand_html += "<td>"+(heating_system_demand).toFixed(0)+"W</td>";
}

$("#external_temperature").html("<tr>"+external_temperature_html+"</tr>");
$("#internal_temperature").html("<tr>"+internal_temperature_html+"</tr>");
$("#temperature_difference").html("<tr>"+temperature_difference_html+"</tr>");
$("#heat_demand").html("<tr>"+heat_demand_html+"</tr>");
$("#internal_gains").html("<tr>"+internal_gains_html+"</tr>");
$("#heating_system_demand").html("<tr>"+heating_system_demand_html+"<td></td></tr>");

var annual_heating_demand = ((sum / 12.0) * 0.024 * 365);
var annual_fuel_input = annual_heating_demand / 0.82;
var annual_heating_cost = annual_fuel_input * 0.05;
var annual_energy_cost = annual_fuel_input * 0.05;

var ECF = (annual_energy_cost * 0.42) / ((6*3.5) + 45.0);
var sap_rating = 0;

if (ECF >= 3.5) sap_rating = 117 - 121 * (Math.log(ECF) / Math.LN10);
if (ECF < 3.5) sap_rating = 100 - 13.95 * ECF;

sap_rating = Math.round(sap_rating);

var ratings = [
  {start:92, end:100, letter:'A', color:"#009a44"},
  {start:81, end:91, letter:'B', color:"#2dca73"},
  {start:69, end:80, letter:'C', color:"#b8f351"},
  {start:55, end:68, letter:'D', color:"#f5ec00"},
  {start:39, end:54, letter:'E', color:"#ffac4d"},
  {start:21, end:38, letter:'F', color:"#fd8130"},
  {start:1, end:20, letter:'G', color:"#fd001a"}
];

var band = 0;
for (z in ratings)
{
  if (sap_rating>=ratings[z].start && sap_rating<=ratings[z].end) {band = z; break;}
}

$("#annual_heating_demand").html(annual_heating_demand.toFixed(0));
$("#annual_fuel_input").html(annual_fuel_input.toFixed(0));
$("#annual_heating_cost").html(annual_heating_cost.toFixed(0));
$("#annual_energy_cost").html(annual_energy_cost.toFixed(0));
$("#sap_rating").html(sap_rating+" ("+ratings[band].letter+")");
$(".sap_rating").css('background-color',ratings[band].color);
</script>
