var waterheating_model = 
{
  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.waterheating!=undefined) {
      for (z in inputdata.waterheating.input) this.input[z] = inputdata.waterheating.input[z];
    }
    
    // Input dependencies
    this.input.TFA = inputdata.TFA;
    this.input.N = inputdata.occupancy;
    
    if (inputdata.solarhotwater!=undefined) {
      this.input.solar_hot_water_contribution = inputdata.solarhotwater.output.Qs_monthly;
    }
  },
  
  input: {
    TFA: 0,
    N: 0,
    low_water_use_design: false,
    instantaneous_hotwater: false,
    declared_loss_factor_known: false,
    manufacturer_loss_factor: 0,
    temperature_factor_a: 0,
    storage_volume: 0,
    loss_factor_b: 0.0191,
    volume_factor_b: 0.908,
    temperature_factor_b: 0.6,
    contains_dedicated_solar_storage_or_WWHRS: true,
    Vs: 0, // Vs is Vww from Appendix G3 or (H11) from Appendix H
    pipework_insulated_fraction: 0,
    hot_water_control_type: "no_cylinder_thermostat",
    community_heating: true,
    combi_loss: [0,0,0,0,0,0,0,0,0,0,0,0],
    solar_water_heating: false,
    solar_hot_water_contribution: [0,0,0,0,0,0,0,0,0,0,0,0],
    hot_water_store_in_dwelling: true,
  },

  calc: function()
  {
    var i = this.input;

    var Vd_average = (25 * i.N) + 36;
    if (i.low_water_use_design) Vd_average *= 0.95;
    
    var Vd_m = [];
    var monthly_energy_content = [];
    var distribution_loss = [0,0,0,0,0,0,0,0,0,0,0,0];
    var energy_lost_from_water_storage = 0;
    var monthly_storage_loss = [0,0,0,0,0,0,0,0,0,0,0,0];
    var primary_circuit_loss = [0,0,0,0,0,0,0,0,0,0,0,0];
    var total_heat_required = [];
    var hot_water_heater_output = [];
    var heat_gains_from_water_heating = [];
    
    var annual_energy_content = 0;
    
    for (var m=0; m<12; m++) {
      Vd_m[m] = table_1c[m] * Vd_average;
      monthly_energy_content[m] = (4.190 * Vd_m[m] * table_1a[m] * table_1d[m]) / 3600;
      annual_energy_content += monthly_energy_content[m];
    }

    //----------------------------------------------------------------------------------------
    // Only calculate losses for storage and distribution if not instantaneous heating
    if (!i.instantaneous_hotwater)
    {
      // STORAGE LOSS kWh/d
      if (i.declared_loss_factor_known) {
        energy_lost_from_water_storage = i.manufacturer_loss_factor * i.temperature_factor_a;
      } else {
        energy_lost_from_water_storage = i.storage_volume * i.loss_factor_b * i.volume_factor_b * i.temperature_factor_b;
      }

      for (var m=0; m<12; m++) {
      
        // DISTRIBUTION LOSSES
        distribution_loss[m] = 0.15 * monthly_energy_content[m];
        
        // MONTHLY STORAGE LOSSES
        monthly_storage_loss[m] = table_1a[m] * energy_lost_from_water_storage;

        if (i.contains_dedicated_solar_storage_or_WWHRS) {
          monthly_storage_loss[m] = monthly_storage_loss[m] * ((i.storage_volume-i.Vs) / (i.storage_volume));
        }
       
        // PRIMARY CIRCUIT LOSSES
        if (m>=5 && m<=8) {
          hours_per_day = 3;
        } else {
          if (i.hot_water_control_type == "no_cylinder_thermostat") hours_per_day = 11;
          if (i.hot_water_control_type == "cylinder_thermostat_without_timer") hours_per_day = 5;
          if (i.hot_water_control_type == "cylinder_thermostat_with_timer") hours_per_day = 3;
          if (i.community_heating) hours_per_day = 3;
        }
        
        if (i.community_heating) i.pipework_insulated_fraction = 1.0;
        primary_circuit_loss[m] = table_1a[m] * 14 * ((0.0091 * i.pipework_insulated_fraction + 0.0245 * (1-i.pipework_insulated_fraction)) * hours_per_day + 0.0263);
        
        if (i.solar_water_heating) primary_circuit_loss[m] *= table_h4[m];
   
        total_heat_required[m] = 0.85 * monthly_energy_content[m] + distribution_loss[m] + monthly_storage_loss[m] + primary_circuit_loss[m] + i.combi_loss[m];
      }
    //----------------------------------------------------------------------------------------
    }
    else
    {
      for (var m=0; m<12; m++) total_heat_required[m] = 0.85 * monthly_energy_content[m];
    }
    
    //----------------------------------------------------------------------------------------
    
    var waterheating_gains = [];
    var annual_waterheating_demand = 0;
    for (var m=0; m<12; m++) {
    
      if (i.solar_water_heating) {
        hot_water_heater_output[m] = total_heat_required[m] + i.solar_hot_water_contribution[m];
      } else {
        hot_water_heater_output[m] = total_heat_required[m];
      }
      
      if (hot_water_heater_output[m]<0) hot_water_heater_output[m] = 0;
      
      annual_waterheating_demand += hot_water_heater_output[m];
      
      if (i.hot_water_store_in_dwelling || i.community_heating) {
        heat_gains_from_water_heating[m] = 0.25 * (0.85*monthly_energy_content[m]+i.combi_loss[m]) + 0.8*(distribution_loss[m]+monthly_storage_loss[m]+primary_circuit_loss[m]);
      } else {
        heat_gains_from_water_heating[m] = 0.25 * (0.85*monthly_energy_content[m]) + 0.8*(distribution_loss[m]+primary_circuit_loss[m]);
      }
      
      // Table 5 typical gains
      waterheating_gains[m] = (1000 * heat_gains_from_water_heating[m]) / (table_1a[m] * 24);
    }  

    /*
    // Combi loss for each month from Table 3a, 3b or 3c (enter “0” if not a combi boiler)
    switch(combi_type)
    {
    case 'instantaneous_no_keephot':
      combi_loss[m] = 600 * fu * table_1a[m] / 365;
      break;
    case 'instantaneous_keephot_timeclock':
      combi_loss[m] = 600 * table_1a[m] / 365;
      break;
    case 'instantaneous_keephot_no_timeclock':
      combi_loss[m] = 900 * table_1a[m] / 365;
      break;
    case '
    }
    */    
    
    return {
      Vd_average: Vd_average,
      Vd_m: Vd_m,
      monthly_energy_content: monthly_energy_content,
      distribution_loss: distribution_loss,
      monthly_storage_loss: monthly_storage_loss,
      primary_circuit_loss: primary_circuit_loss,
      total_heat_required: total_heat_required,
      hot_water_heater_output: hot_water_heater_output,
      heat_gains_from_water_heating: heat_gains_from_water_heating,
      waterheating_gains: waterheating_gains,
      annual_energy_content: annual_energy_content,
      annual_waterheating_demand: annual_waterheating_demand
    };
  }
}

function waterheating_savetoinputdata(inputdata,o)
{
  inputdata.gains['waterheating'] = o.waterheating_gains;
}

function waterheating_customview(i)
{
    if (i.instantaneous_hotwater) $(".loss-interface").hide(); else  $(".loss-interface").show();
    
    if (i.declared_loss_factor_known) {
      $(".declared-loss-factor-known").show();
      $(".declared-loss-factor-not-known").hide();
    } else {
      $(".declared-loss-factor-known").hide();
      $(".declared-loss-factor-not-known").show();
    }
    
    if (i.solar_water_heating) $("#solar_hot_water_contribution").show(); else $("#solar_hot_water_contribution").hide();

}
