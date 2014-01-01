function hot_water_controller()
{ 
  $("input[type=text]").change(function(){
    var id = $(this).attr('id');
    if (id!=undefined) {
      i[id] = parseFloat($(this).val());
      o = hot_water_model.calc();
      update_view(i,o); save(i);
    }
  });
  
  $("input[type=checkbox]").change(function(){
    var id = $(this).attr('id');
    if ($(this)[0].checked) i[id] = true; else i[id] = false;
    o = hot_water_model.calc();
    update_view(i,o); save(i);
  });
  
  $("select").change(function(){
    var id = $(this).attr('id');
    i[id] = $(this).val();
    o = hot_water_model.calc();
    update_view(i,o); save(i);
  });
  
  $(".combi_loss").change(function(){
    var m = $(this).attr('m');
    i.combi_loss[m] = parseFloat($(this).val());
    o = hot_water_model.calc();
    update_view(i,o); save(i);
  });
}

function update_view(i,o)
{
    if (i.instantaneous_hotwater) $(".loss-interface").hide(); else  $(".loss-interface").show();
    
    if (i.declared_loss_factor_known) {
      $(".declared-loss-factor-known").show();
      $(".declared-loss-factor-not-known").hide();
    } else {
      $(".declared-loss-factor-known").hide();
      $(".declared-loss-factor-not-known").show();
    }
    
    $("#storage_volume").val(i.storage_volume);
    $("#sap-occupancy").html(o.N.toFixed(2)); 
    $("#manual_occupancy").val(i.manual_occupancy); 
    $("#annual-average-hot-water-use").html(o.Vd_average.toFixed(2));
    $("#manufacturer_loss_factor").val(i.manufacturer_loss_factor);
    $("#temperature_factor_a").val(i.temperature_factor_a);
    $("#loss_factor_b").val(i.loss_factor_b);
    $("#volume_factor_b").val(i.volume_factor_b);
    $("#temperature_factor_b").val(i.temperature_factor_b);

    $("#instantaneous_hotwater").prop('checked', i.instantaneous_hotwater);
    $("#low_water_use_design").prop('checked', i.low_water_use_design);
    $("#use_manual_occupancy").prop('checked', i.use_manual_occupancy);
    $("#contains_dedicated_solar_storage_or_WWHRS").prop('checked', i.contains_dedicated_solar_storage_or_WWHRS);   
    $("#community_heating").prop('checked', i.community_heating);
    $("#solar_water_heating").prop('checked', i.solar_water_heating);
    $("#hot_water_store_in_dwelling").prop('checked', i.hot_water_store_in_dwelling);
        
    $("#monthly_hotwater_use_per_day").html(
      monthlyrow("Hot water usage in litres per day for each month: ",o.Vd_m,0)
    );
    
    $("#monthly_energy_content").html(
      monthlyrow("Energy content (kWh/month) ",o.monthly_energy_content,0)
    );
    
    $("#distribution_loss").html(
      monthlyrow("Distribution loss (0.15 x E) kWh/month",o.distribution_loss,0)
    );
    
    $("#monthly_storage_loss").html(
      monthlyrow("Water storage loss",o.monthly_storage_loss,0)
    );
    
    $("#primary_circuit_loss").html(
      monthlyrow("Primary circuit loss",o.primary_circuit_loss,0)
    );
    
    $("#total_heat_required").html(
      monthlyrow("Total heat required",o.total_heat_required,0)
    );
    
    $("#solar_hot_water_contribution").html(
      monthlyrow("Solar hot water input",i.solar_hot_water_contribution,0)
    );
    
    $("#hot_water_heater_output").html(
      monthlyrow("Hot Water Heater output",o.hot_water_heater_output,0)
    );
    
    $("#heat_gains_from_water_heating").html(
      monthlyrow("Heat gains from water heating",o.heat_gains_from_water_heating,0)
    );
    
    for (var m=0; m<12; m++)
    {
      $(".combi_loss[m="+m+"]").val(i.combi_loss[m]);
    }
}

function monthlyrow(title,monthly,dp)
{
  var monthly_html = "<td>"+title+"</td>";
  for (var m=0; m<12; m++) {
    monthly_html += "<td>"+(monthly[m]).toFixed(dp)+"</td>";
  }
  return "<tr>"+monthly_html+"</tr>";
}
