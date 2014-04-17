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

?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/openbem.js"></script>
<br>

<ul class="breadcrumb">
<li><a href="<?php echo $path; ?>openbem/projects">My Projects</a> <span class="divider">/</span></li>
<li><a href="<?php echo $path; ?>openbem/project?project_id=<?php echo $project_id; ?>" class="project_name"></a> <span class="divider">/</span></li>
<li class="active">Compare '<a href="<?php echo $path; ?>openbem/monthly?project_id=<?php echo $project_id; ?>&scenario_id=<?php echo $scenarioB; ?>" class="scenario_nameB"></a>' with '<a href="<?php echo $path; ?>openbem/monthly?project_id=<?php echo $project_id; ?>&scenario_id=<?php echo $scenarioA; ?>" class="scenario_nameA"></a>'</li>
</ul>
    
<h2>Changes</h2>
<br>
<table id="compare" class="table table-striped"></table>

<script>
  
  var path = "<?php echo $path; ?>";
  
  var scenarioA = <?php echo $scenarioA; ?>;
  var scenarioB = <?php echo $scenarioB; ?>;
  
  var project_id = <?php echo $project_id; ?>; 
  var project_details = openbem.getprojectdetails(project_id);
  $(".project_name").html(project_details.project_name);
  
  var scenarioA = openbem.get_scenario(scenarioA);
  var inputdataA = scenarioA.scenario_data;
  
  var scenarioB = openbem.get_scenario(scenarioB);
  var inputdataB = scenarioB.scenario_data;
  
  $(".scenario_nameA").html(scenarioA.scenario_meta.name);
  $(".scenario_nameB").html(scenarioB.scenario_meta.name);
  
  var out = "";

  var changes = [
  
    ["Basement area",'context.input.basement_area'],
    ["Basement height",'context.input.basement_height'],
    ["Ground floor area",'context.input.groundfloor_area'],
    ["Ground floor height",'context.input.groundfloor_height'],
    ["First floor area",'context.input.firstfloor_area'],
    ["First floor height",'context.input.firstfloor_height'],
    ["Second floor area",'context.input.secondfloor_area'],
    ["Second floor height",'context.input.secondfloor_height'],
    ["Third floor area",'context.input.thirdfloor_area'],
    ["Third floor height",'context.input.thirdfloor_height'],
    ["Other floor 1 area",'context.input.otherfloor1_area'],
    ["Other floor 1 height",'context.input.otherfloor1_height'],
    ["Other floor 2 area",'context.input.otherfloor2_area'],
    ["Other floor 2 height",'context.input.otherfloor2_height'],
    ["Other floor 3 area",'context.input.otherfloor3_area'],
    ["Other floor 3 height",'context.input.otherfloor3_height'],   
    ["Region",'context.input.contextregion'],
    ["Altitude",'context.input.contextaltitude'],
    ["Use manual occupancy",'context.input.use_manual_occupancy'],
    ["Manual occupancy",'context.input.manual_occupancy'],
                   
    ["Number of chimney's",'ventilation.input.number_of_chimneys'],
    ["Number of open flue's",'ventilation.input.number_of_openflues'],
    ["Number of intermittent fans",'ventilation.input.number_of_intermittentfans'],
    ["Number of passive vents",'ventilation.input.number_of_passivevents'],
    ["Number of flueless gas fires",'ventilation.input.number_of_fluelessgasfires'],
    ["Number of storeys",'ventilation.input.dwelling_storeys'],
    ["Dwelling construction",'ventilation.input.dwelling_construction'],

    ["Suspended wooden floor",'ventilation.input.suspended_wooden_floor'],
    ["Draught lobby",'ventilation.input.draught_lobby'],
    ["Percentage draught proofed",'ventilation.input.percentage_draught_proofed'],
    ["Air permeability test",'ventilation.input.air_permeability_test'],
    ["Air permeability value",'ventilation.input.air_permeability_value'],
    ["Number of sides sheltered",'ventilation.input.number_of_sides_sheltered'],
    ["Ventilation type",'ventilation.input.ventilation_type'],
    ["System air change rate",'ventilation.input.system_air_change_rate'],
    ["Balanced heat recovery efficiency",'ventilation.input.balanced_heat_recovery_efficiency'],

    ["Heating system responsiveness",'meaninternaltemperature.input.R'],
    ["Control type",'meaninternaltemperature.input.control_type'],  
    ["Control type",'meaninternaltemperature.input.control_type'],  
    ["Living area",'meaninternaltemperature.input.living_area'],

    ["Use utilfactor for gains",'balance.input.use_utilfactor_forgains'],
    ["Energy cost deflator",'heatingsystem.input.energy_cost_deflator']

  ];
  
  for (z in changes)
  {
    var keystr = changes[z][1];
    var description = changes[z][0];
    
    var keys = keystr.split(".");
    
    var subA = inputdataA;
    var subB = inputdataB;
    
    for (z in keys)
    {
      if (subA!=undefined) {
        subA = subA[keys[z]];
      }

      if (subB!=undefined) {
        subB = subB[keys[z]];
      }
    }
    
    var valA = subA;
    var valB = subB; 
    
    if (valA!=valB) {
      out += "<tr><td>"+description+" changed from "+valA+" to "+valB+"</td></tr>";
    }
  }
  
  // Changes to elements
  
  var num = inputdataB.elements.input.list.length;
  
  if (inputdataA.elements.input.list.length>inputdataB.elements.input.list.length) {
      num = inputdataA.elements.input.list.length;
  }
  
  for (var i=0; i<num; i++)
  {
    if (inputdataA.elements.input.list[i].uvalue != inputdataB.elements.input.list[i].uvalue) {
      valA = inputdataA.elements.input.list[i].uvalue;
      valB = inputdataB.elements.input.list[i].uvalue;
      out += "<tr><td>Element: "+inputdataA.elements.input.list[i].name+" u-value changed from "+valA+" to "+valB+"</tr>";
    }
    
    if (inputdataA.elements.input.list[i].area != inputdataB.elements.input.list[i].area) {
      valA = inputdataA.elements.input.list[i].area;
      valB = inputdataB.elements.input.list[i].area;
      out += "<tr><td>Element: "+inputdataA.elements.input.list[i].name+" area changed from "+valA+" m2 to "+valB+" m2</td></tr>";
    }
    
    if (inputdataA.elements.input.list[i].kvalue != inputdataB.elements.input.list[i].kvalue) {
      valA = inputdataA.elements.input.list[i].kvalue;
      valB = inputdataB.elements.input.list[i].kvalue;
      out += "<tr><td>Element: "+inputdataA.elements.input.list[i].name+" k-value changed from "+valA+" kJ/m2 to "+valB+" kJ/m2</td></tr>";
    }
  }
  
  $("#compare").html(out);
</script>
