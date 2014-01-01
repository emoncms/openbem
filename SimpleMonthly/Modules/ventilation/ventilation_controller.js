function ventilation_controller()
{
  var input = {
  
    number_of_chimneys: 1,
    number_of_openflues: 1,
    number_of_intermittentfans: 0,
    number_of_passivevents: 0,
    number_of_fluelessgasfires: 0,
    
    dwelling_volume: 250,
    dwelling_storeys: 2,
    dwelling_construction: 'masonry',        // 'timberframe' or 'masonry'
    
    suspended_wooden_floor: 0,        // 'unsealed' or 'sealed' or 0
    draught_lobby: true,
    percentage_draught_proofed: 50,    // percentage of windows and doors
    air_permeability_test: true,     
    air_permeability_value: 0,        // 0 or value
    
    number_of_sides_sheltered: 2, 
    
    region: 13,
    
    // Ventilation types:
    // a) Balanced mechanical ventilation with heat recovery (MVHR)
    // b) Balanced mechanical ventilation without heat recovery (MV)
    // c) Whole house extract ventilation or positive input ventilation from outside
    // d) Natural ventilation or whole house positive input ventilation from loft
    
    ventilation_type: 'd',
    
    system_air_change_rate: 1,
    balanced_heat_recovery_efficiency: 80
  }
  
  ventilation_model.set_inputdata(input);
  var result = ventilation_model.calc();
  update_view_results(result);
    
  // Init view
  update_view(input);
  showhide_system(input);
  showhide_structural(input);
  
  $(".ventilation[type=text]").keyup(function()
  {
    var key = $(this).attr('name');
    var val = $(this).val();
    console.log("Input text updated: "+key+":"+val);
    
    input[key] = val;
    
    ventilation_model.set_inputdata(input);
    var result = ventilation_model.calc();
    update_view_results(result);
  });
  
  $("select[class=ventilation]").click(function()
  {
    var key = $(this).attr('name');
    var val = $(this).val();
    console.log("Select updated: "+key+":"+val);
    
    input[key] = val;
    
    ventilation_model.set_inputdata(input);
    var result = ventilation_model.calc();
    update_view_results(result);
    
    if (key=="ventilation_type") showhide_system(input);
  });  
  
  $("input[type=checkbox][class=ventilation]").click(function()
  {
    var key = $(this).attr('name');
    var val = $(this)[0].checked;
    console.log("Checkbox updated: "+key+":"+val);
    
    input[key] = val;
       
    ventilation_model.set_inputdata(input);
    var result = ventilation_model.calc();
    update_view_results(result);
    
    if (key=="air_permeability_test") showhide_structural(input);
  });
 
}

function update_view(input)
{
  $(".ventilation[type=text]").each(function() {
    var key = $(this).attr('name');
    $(this).val(input[key]);
  });

  $("select[class=ventilation]").each(function() {
    var key = $(this).attr('name');
    $(this).val(input[key]);
  });
  
  $("input[type=checkbox][class=ventilation]").each(function() {
    var key = $(this).attr('name');
    $(this)[0].checked = input[key];
  });
}

function update_view_results(result)
{
  var effective_air_change_rate_html = "<td>Air change rate: </td>";
  var infiltration_WK_html = "<td>Infiltration W/K: </td>";
  for (var m=0; m<12; m++)
  {
    effective_air_change_rate_html += "<td>"+(result.effective_air_change_rate[m]).toFixed(2)+"</td>";
    infiltration_WK_html += "<td>"+(result.infiltration_WK[m]).toFixed(0)+"W</td>";
  }
  $("#effective_air_change_rate").html("<tr>"+effective_air_change_rate_html+"</tr>");
  $("#infiltration_WK").html("<tr>"+infiltration_WK_html+"</tr>");
}

function showhide_system(input)
{
  if (input.ventilation_type=='a') {
    $("#system_air_change_rate").show();
    $("#balanced_heat_recovery_efficiency").show();
  }
  
  if (input.ventilation_type=='b') {
    $("#system_air_change_rate").show();
    $("#balanced_heat_recovery_efficiency").hide();
  }
  
  if (input.ventilation_type=='c') {
    $("#system_air_change_rate").show();
    $("#balanced_heat_recovery_efficiency").hide();
  }
  
  if (input.ventilation_type=='d') {
    $("#system_air_change_rate").hide();
    $("#balanced_heat_recovery_efficiency").hide();
  }
}

function showhide_structural(input)
{
  if (input.air_permeability_test==true) {
    $("#air_permeability_value").show();
    $("#structural").hide();
  } else {
    $("#air_permeability_value").hide();
    $("#structural").show();  
  }
}
