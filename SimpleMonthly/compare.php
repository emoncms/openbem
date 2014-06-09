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
<div id="compare"></div>

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
    ["Living area",'meaninternaltemperature.input.living_area'],

    ["Use utilfactor for gains",'balance.input.use_utilfactor_forgains'],
    ["Energy cost deflator",'heatingsystem.input.energy_cost_deflator']

  ];
  
  out += "<table class='table table-striped'>";
  
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
  
  out += "</table>";
  
  // Changes to elements
  var listA = inputdataA.elements.input.list;
  var listB = inputdataB.elements.input.list;
  
  var elements_html = "";
  
  for (z in listA)
  {
        if (listB[z]==undefined)
        {
            elements_html += "<tr><td>Element: <b>'"+z+"'</b> in scenario A has been deleted</td></tr>";
        }
  }
  
  for (z in listB)
  {
        if (listA[z]==undefined)
        {
            elements_html += "<tr><td>New Element: <b>'"+z+"'</b> added to scenario B</td></tr>";
        }
        else
        {
            
            if (JSON.stringify(listA[z]) != JSON.stringify(listB[z]))
            {
                elements_html += "<tr><td><b>"+z+":</b><br><i>";
                for (x in listA[z])
                {
                    if (x=='description') elements_html += listA[z][x]+", ";
                    if (x=='area') elements_html += "Area: "+listA[z][x].toFixed(1)+"m<sup>2</sup>, ";
                    if (x=='uvalue') elements_html += "U-value: "+listA[z][x]+", ";
                    if (x=='kvalue') elements_html += "k-value: "+listA[z][x];
                    if (x=='g') elements_html += "g: "+listA[z][x]+", ";
                    if (x=='gL') elements_html += "gL: "+listA[z][x]+", ";
                    if (x=='ff') elements_html += "Frame factor: "+listA[z][x];
                }
                elements_html += "</i></td>";
                
                elements_html += "<td>"+(listA[z].uvalue*listA[z].area).toFixed(1)+" W/K</td>";
                
                elements_html += "<td><b>"+z+":</b><br><i>";
                for (x in listB[z])
                {
                    if (x=='description') elements_html += listA[z][x]+", ";
                    if (x=='area') elements_html += "Area: "+listA[z][x].toFixed(1)+"m<sup>2</sup>, ";
                    if (x=='uvalue') elements_html += "U-value: "+listB[z][x]+", ";
                    if (x=='kvalue') elements_html += "k-value: "+listB[z][x];
                    if (x=='g') elements_html += "g: "+listB[z][x]+", ";
                    if (x=='gL') elements_html += "gL: "+listB[z][x]+", ";
                    if (x=='ff') elements_html += "Frame factor: "+listB[z][x];
                }
                elements_html += "</i></td>";
                
                elements_html += "<td>"+(listB[z].uvalue*listB[z].area).toFixed(1)+" W/K</td>";
                
                var saving = (listA[z].uvalue*listA[z].area) - (listB[z].uvalue*listB[z].area);
                
                elements_html += "<td>";
                if (saving>0) elements_html +="<span style='color:#00aa00'>-";
                if (saving<0) elements_html +="<span style='color:#aa0000'>+";
                elements_html += (saving).toFixed(1)+" W/K</span></td>";
                
                elements_html += "</tr>";
            }
        }
  }
  
  if (elements_html!="") {
    out += "<h3>Building Elements</h3>";
    out += "<p>Changes to Floor's, Wall's, Windows and Roof elements</p>";
    out += "<table class='table table-striped'>";
    out += "<tr><th>Before</th><th>W/K</th><th>After</th><th>W/K</th><th>Change</th></tr>";
    out += elements_html;
    out += "</table>";
  }
  
  out += "<h3>Energy Requirements</h3>";
  
  // Changes to elements
  var listA = inputdataA.heatingsystem.input.energy_requirements;
  var listB = inputdataB.heatingsystem.input.energy_requirements;
   
  out += "<table class='table table-striped'>";
  
  for (z in listA)
  {
        if (listB[z]==undefined)
        {
                out += "<tr><td>";
                
                out += "<b>"+listA[z].name+": </b>";
                out += listA[z].quantity.toFixed(0)+" kWh<br>";
                out += "  Supplied by:<br>";
                for (i in listA[z].suppliedby)
                {
                    out += "  - Type: "+listA[z].suppliedby[i].type+", ";
                    out += "Fraction: "+(listA[z].suppliedby[i].fraction*100).toFixed(0)+"%, ";
                    out += "Efficiency: "+(listA[z].suppliedby[i].efficiency*100).toFixed(0)+"%";
                    out += "<br>";
                }
                
                out += "</td><td><br><b>Deleted in scenario B</b></td><td></td></tr>";
        }
  }
  
  for (z in listB)
  {
        if (listA[z]==undefined)
        {
                out += "<tr><td><br><b>New to scenario B</b></td><td>";
                
                out += "<b>"+listB[z].name+": </b>";
                out += listB[z].quantity.toFixed(0)+" kWh <b>(New)</b><br>";
                out += "  Supplied by:<br>";
                for (i in listB[z].suppliedby)
                {
                    out += "  - Type: "+listB[z].suppliedby[i].type+", ";
                    out += "Fraction: "+(listB[z].suppliedby[i].fraction*100).toFixed(0)+"%, ";
                    out += "Efficiency: "+(listB[z].suppliedby[i].efficiency*100).toFixed(0)+"%";
                    out += "<br>";
                }
                
                out += "</td><td></td></tr>";
        }
        else
        {
            
            if (JSON.stringify(listA[z]) != JSON.stringify(listB[z]))
            {   
                out += "<tr><td>";
                
                out += "<b>"+listA[z].name+": </b>";
                out += listA[z].quantity.toFixed(0)+" kWh<br>";
                out += "  Supplied by:<br>";
                for (i in listA[z].suppliedby)
                {
                    out += "  - Type: "+listA[z].suppliedby[i].type+", ";
                    out += "Fraction: "+(listA[z].suppliedby[i].fraction*100).toFixed(0)+"%, ";
                    out += "Efficiency: "+(listA[z].suppliedby[i].efficiency*100).toFixed(0)+"%";
                    out += "<br>";
                }
                
                out += "</td><td>";
                
                out += "<b>"+listB[z].name+": </b>";
                out += listB[z].quantity.toFixed(0)+" kWh<br>";
                out += "  Supplied by:<br>";
                for (i in listB[z].suppliedby)
                {
                    out += "  - Type: "+listB[z].suppliedby[i].type+", ";
                    out += "Fraction: "+(listB[z].suppliedby[i].fraction*100).toFixed(0)+"%, ";
                    out += "Efficiency: "+(listB[z].suppliedby[i].efficiency*100).toFixed(0)+"%";
                    out += "<br>";
                }
                
                out += "</td><td></td></tr>";
            }
        }
  }
  
  // out += "</table>";
  out += "<tr><td><h3>Fuel costs</h3></td><td></td><td></td></tr>";
  // out += "<h3>Fuel costs</h3>";
  
  // Changes to elements
  var listA = inputdataA.heatingsystem.output.fueltotals;
  var listB = inputdataB.heatingsystem.output.fueltotals;
  
  //out += "<table class='table table-striped'>";
  
  for (z in listA)
  {
        if (listB[z]==undefined)
        {
                out += "<tr><td>";
                
                out += "<b>"+z+": </b><br>";
                out += "Fuel quantity: "+listA[z].quantity.toFixed(0)+" kWh<br>";
                out += "Fuel cost: £"+listA[z].fuelcost.toFixed(2)+"<br>";
                out += "Annual cost: £"+listA[z].annualcost.toFixed(0)+"<br>";
                
                out += "</td><td><br><b>Deleted in scenario B</b></td></tr>";
        }
  }
  
  for (z in listB)
  {
        if (listA[z]==undefined)
        {
                out += "<tr><td><br><b>New to scenario B</b></td><td>";
                
                out += "<b>"+z+": </b><br>";
                out += "Fuel quantity: "+listB[z].quantity.toFixed(0)+" kWh<br>";
                out += "Fuel cost: £"+listB[z].fuelcost.toFixed(2)+"<br>";
                out += "Annual cost: £"+listB[z].annualcost.toFixed(0)+"<br>";
                
                out += "</td></tr>";
        }
        else
        {
            
            if (JSON.stringify(listA[z]) != JSON.stringify(listB[z]))
            {   
                out += "<tr><td>";
                
                out += "<b>"+z+": </b><br>";
                out += "Fuel quantity: "+listA[z].quantity.toFixed(0)+" kWh<br>";
                out += "Fuel cost: £"+listA[z].fuelcost.toFixed(2)+"<br>";
                out += "Annual cost: £"+listA[z].annualcost.toFixed(0)+"<br>";
                
                out += "</td><td>";
                
                out += "<b>"+z+": </b><br>";
                out += "Fuel quantity: "+listB[z].quantity.toFixed(0)+" kWh<br>";
                out += "Fuel cost: £"+listB[z].fuelcost.toFixed(2)+"<br>";
                out += "Annual cost: £"+listB[z].annualcost.toFixed(0)+"<br>";
                
                out += "</td>";
                
                out += "<td><br>";
                
                out += (100*(listA[z].quantity-listB[z].quantity)/listA[z].quantity).toFixed(0)+"% Energy saving<br><br>";
                
                out += (100*(listA[z].annualcost-listB[z].annualcost)/listA[z].annualcost).toFixed(0)+"% Cost saving<br>";
                
                out += "</td></tr>";
            }
        }
    }

    out += "<tr><td><h3>Totals</h3></td><td></td><td></td></tr>";

    out += "<tr>";
    out += "<td><b>Total Annual Cost:</b><br>";
    out += "£"+inputdataA.heatingsystem.output.total_cost.toFixed(0)+"</td>";
    out += "<td><b>Total Annual Cost:</b><br>"
    out += "£"+inputdataB.heatingsystem.output.total_cost.toFixed(0)+"</td>";
    out += "<td></td>";
    out += "</tr>";

    out += "<tr>";
    out += "<td><b>SAP Rating:</b><br>";
    out += ""+inputdataA.heatingsystem.output.sap_rating.toFixed(0)+"</td>";
    out += "<td><b>SAP Rating:</b><br>"
    out += ""+inputdataB.heatingsystem.output.sap_rating.toFixed(0)+"</td>";

    var sapinc = (inputdataB.heatingsystem.output.sap_rating-inputdataA.heatingsystem.output.sap_rating);

    if (sapinc>0) out +="<td><br><span style='color:#00aa00'>+";
    if (sapinc<0) out +="<td><br><span style='color:#aa0000'>-";
    out += sapinc.toFixed(0)+"</span></td>";
    
    out += "</tr>";

    out += "</table>";

    $("#compare").html(out);

</script>
