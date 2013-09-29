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


<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/view/model.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/view/view.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/view/controller.js"></script>
<br>

<style>
  #element-totals td:nth-of-type(1) {  text-align: right; }
  #element-totals td:nth-of-type(2) { width:100px; text-align: center; padding-right: 53px}

  #element-table th:nth-of-type(3) { text-align: center;}
  #element-table th:nth-of-type(4) { text-align: center;}
  #element-table th:nth-of-type(5) { width:80px; text-align: center;}
  
  #element-table td:nth-of-type(3) { text-align: center;}
  #element-table td:nth-of-type(4) { text-align: center;}
  #element-table td:nth-of-type(5) { text-align: center;}
  #element-table td:nth-of-type(6) { width:38px; text-align: center;}
  
  
  #annual td:nth-of-type(1) { text-align: left;}
  #annual td:nth-of-type(2) { width:150px; text-align: right;}
  #annual td:nth-of-type(3) { width:20px; text-align: left;}
  #annual td:nth-of-type(4) { width:38px; text-align: center;}
  
  #annual input {width:40px; margin:0px; padding:2px; text-align: right;}
  
  #monthly input {width:40px; margin:0px; padding:2px; text-align: center;}
</style>

<ul class="nav nav-pills">
  <li class="active">
    <a href="#">OpenBEM</a>
  </li>
  <li><a href="graph">Simulation</a></li>
  <li><a href="daily">Daily average</a></li>
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
<input id="air_change_rate" type="text" placeholder="3">
<span class="add-on">ACH</span> 
</div>

<h3>Building elements</h3>
<p>Add all the floor, roof, wall and window elements that describe your building:</p>

<ul class="nav nav-tabs">
  <li><a href="#myModal" role="button" data-toggle="modal">Add element: <i class="icon-plus" style="cursor:pointer"></i></a></li>
  <li class="pull-right"><a>Thermal capacity</a></li>
  <li class="active  pull-right">
    <a>U-values</a>
  </li>
  <li class="disabled  pull-right"><a>Show: </a></li>
  
</ul>

<table id="element-table" class="table">
<tr><th></th><th>Element</th><th>Area</th><th>U-value</th><th>W/K</th><th></th></tr>
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
    
<table id="monthly" class="table">
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

<table id="annual" class="table">
  <tr><td>Annual heating demand</td><td><span id="annual_heating_demand"></span></td><td>kWh</td><td></td></tr>
  <tr><td>Heating system efficiency</td><td><span id="heating_system_efficiency">100</span></td><td>%</td>
    <td><span style='display:none'><i iid='heating_system_efficiency' class='icon-pencil' style='margin-right: 10px; cursor:pointer' ></i></span></td>
  </tr>
  <tr><td>Annual fuel input</td><td><span id="annual_fuel_input"></span></td><td>kWh</td><td></td></tr>
  <tr>
    <td>Fuel cost</td><td><span id="fuel_cost">0.00</span></td><td>£/kWh</td>
    <td><span style='display:none'><i iid='fuel_cost' class='icon-pencil' style='margin-right: 10px; cursor:pointer' ></i></span></td>
  </tr>
  <tr><td>Annual heating cost</td><td>£ <span id="annual_heating_cost"></span></td><td></td><td></td></tr>
  <tr><td>Annual energy costs (+internal gain sources)</td><td>£ <span id="annual_energy_cost"></span></td><td></td><td></td></tr>
  <tr class="sap_rating"><td>SAP Rating</td><td><span id="sap_rating"></span></td><td></td><td></td></tr>
</table>


 
<!-- Modal -->
<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h3 id="myModalLabel">Add Building Element</h3>
</div>
<div class="modal-body">

<p><b>Element type:</b></p>
<select id="element-type">
  <option value=Roof>Roof</option>
  <option value=Wall>Wall</option>
  <option value=Floor>Floor</option>
  <option value=Window>Window</option>
</select>

<p><b>Select element:</b></p>
<select id="element-selector"></select>

<p><b>Set element name:</b></p>
<input id="element-name" type="text" />

<p><b>Set element area:</b></p>
<input id="element-area" type="text" />

</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
<button id="element-add" class="btn btn-primary">Add element</button>
<button id="element-edit" class="btn btn-primary" style="display:none" >Save element</button>
</div>
</div>
    
<script>

var elements = [

  {name:"Main Floor", lib: 'floor0007', area: (6*3.5)},
  {name:"Front wall", lib: 'wall0010', area: (6*1.9)},
  {name:"Back wall", lib: 'wall0010', area: (6*1.9)},

  // 1.45m  
  {name:"Left wall", lib: 'wall0010', area: (3.5*1.9) + 2.54},
  {name:"Right wall", lib: 'wall0004', area: (3.5*1.9) + 2.54},
  
  // hyp 2.27m 
  {name:"Roof", lib: 'roof0002', area: 2*(2.27*6)},
  
  {name:"Front window", lib: 'window0121', area: (0.87*0.9), orientation: 3, overshading: 3},
  {name:"Roof window Front", lib: 'window0001', area: (2.45*0.42), orientation: 3, overshading: 3},
  {name:"Roof window Back", lib: 'window0001', area: (2.45*0.42), orientation: 3, overshading: 2},  
  
];

// End of input data

//-----------------------------------------------------------------------------------------------------------------

// Start of model calcs

  fuel_cost = 0.05;
  heating_system_efficiency = 82;

  model.input.region = 13;
  model.input.airchanges = 1;
  model.input.volume = ((3.5*1.9) + 2.54) * 6.0;
  model.input.elements = elements;
  model.input.heating_system_efficiency = heating_system_efficiency;
  model.input.fuel_cost = fuel_cost;

  model.input.mean_internal_temperature = [18,18,18,18,18,18,18,18,18,18,18,18];
  
  // Initit
  var result = model.calc();
  view();
  
  load_controller();
    
</script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/view/modal_controller.js"></script>
