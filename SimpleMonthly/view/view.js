
function view()
{
  var element_table_mode = 'uvalue';
  $("#air_change_rate").val(inputdata.airchanges);
  
  var out = "";
  for (i in inputdata.elements)
  {
    var id = inputdata.elements[i].lib;
    out += "<tr>";
    
    // Only draws type first time it gets that element type in the list
    // makes for nicer looking layout, relies upon ordered elements list
    if (i>0) {
      var lasttype = element_library[inputdata.elements[i-1].lib].type;
      if (element_library[id].type!=lasttype) {
        out += "<td><b>"+element_library[id].type+"</b></td>";
      } else {
        out += "<td></td>";
      }
    } else {
      out += "<td><b>"+element_library[id].type+"</b></td>";
    }
    
    out += "<td><b>"+inputdata.elements[i].name+"</b><br><i>";
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
    
    out += "<td>"+inputdata.elements[i].area.toFixed(1)+"m<sup>2</sup></td>";
    
    if (element_table_mode == 'uvalue')
    {
      out += "<td>"+element_library[id].uvalue+"</td>";
      out += "<td>"+(element_library[id].uvalue*inputdata.elements[i].area).toFixed(1)+" W/K</td>";
    } else {
      if (element_library[id].kvalue) {
        out += "<td>"+element_library[id].kvalue+"</td>";
        out += "<td>"+(element_library[id].kvalue*inputdata.elements[i].area).toFixed(0)+" kJ/K</td>";
      } else {
        out += "<td>n/a</td><td>n/a</td>";
      }
    }
    // Edit and delete icon's, span hide's/show's the icons when the mouse hovers.
    out += "<td><span style='display:none'>";
    out += "<i class='icon-pencil' style='margin-right: 10px; cursor:pointer' eid="+i+" ></i>";
    out += "<i class='icon-trash' eid="+i+" style='cursor:pointer' ></i>";
    out += "</span></td>";
    
    out += "</tr>";
  }
  
  if (inputdata.elements.length==0) {
    out = "<tr class='alert'><td></td><td style='padding-top:50px; padding-bottom:50px'><b>Click on Add element (top-left) to add floor, walls, roof and window elements</b></td><td></td><td></td><td></td><td></td></tr>";
  }
  
  $("#elements").html(out);

  $("#total_fabric_heat_loss_WK").html(result.total_fabric_heat_loss_WK.toFixed(0));
  $("#infiltration_heat_loss_WK").html(result.infiltration_WK.toFixed(0));
  $("#total_heat_loss_WK").html(result.total_heat_loss_WK.toFixed(0));
  $("#total_thermal_capacity").html(result.total_thermal_capacity.toFixed(0));
  
  var sum = 0;
  var out = "<td> <b>-</b> Solar Gains: </td>";
  for (z in result.solargains) out += "<td>"+result.solargains[z].toFixed(0)+"W</td>";
  out += "<td><b>"+result.average_solargains.toFixed(0)+"W</b></td>";
  $("#solargains").html(out);
  
  // External, Internal, Difference, Heat demand
  var external_temperature_html = "<td>External Temperature: </td>";
  var internal_temperature_html = "<td>Internal Temperature: </td>";
  var temperature_difference_html = "<td>Temperature Difference: </td>";
  var heat_demand_html = "<td>Total heat demand: </td>";
  var internal_gains_html = "<td> <b>-</b> Internal Gains: </td>";
  var heating_system_demand_html = "<td>Heating system demand: </td>";

  var editmode = true;
  
  for (var m=0; m<12; m++)
  {
    // View outputs 
    external_temperature_html += "<td>"+table_u1[inputdata.region][m].toFixed(1)+"C</td>";
    
    if (!editmode) internal_temperature_html += "<td>"+result.mean_internal_temperature[m].toFixed(1)+"C</td>";
    
    if (editmode) {
    
     internal_temperature_html += "<td><input id='monthly_mean_internal_temperature' month='"+m+"' type='text' value='"+result.mean_internal_temperature[m].toFixed(1)+"' /></td>";
    
    }
    
    temperature_difference_html += "<td>"+(result.temperature_difference[m]).toFixed(1)+"C</td>";
    heat_demand_html += "<td>"+(result.heat_demand[m]).toFixed(0)+"W</td>";
    internal_gains_html += "<td>"+(0).toFixed(0)+"W</td>";
    heating_system_demand_html += "<td>"+(result.heating_system_demand[m]).toFixed(0)+"W</td>";
  }

  $("#external_temperature").html("<tr>"+external_temperature_html+"</tr>");
  $("#internal_temperature").html("<tr>"+internal_temperature_html+"</tr>");
  $("#temperature_difference").html("<tr>"+temperature_difference_html+"</tr>");
  $("#heat_demand").html("<tr>"+heat_demand_html+"</tr>");
  $("#internal_gains").html("<tr>"+internal_gains_html+"</tr>");
  $("#heating_system_demand").html("<tr>"+heating_system_demand_html+"<td></td></tr>");

    $("#heating_system_efficiency").html(inputdata.heating_system_efficiency);
    $("#fuel_cost").html(inputdata.fuel_cost);

  $("#annual_heating_demand").html(result.annual_heating_demand.toFixed(0));
  $("#annual_fuel_input").html(result.annual_fuel_input.toFixed(0));
  $("#annual_heating_cost").html(result.annual_heating_cost.toFixed(0));
  $("#annual_energy_cost").html(result.annual_energy_cost.toFixed(0));
  $("#sap_rating").html(result.sap_rating+" ("+ratings[result.band].letter+")");
  $(".sap_rating").css('background-color',ratings[result.band].color);
}
