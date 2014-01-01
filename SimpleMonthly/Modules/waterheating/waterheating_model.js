var hot_water_model = 
{
  input: {
    TFA: 50,
    use_manual_occupancy: false,
    manual_occupancy: 0,
    low_water_use_design: false,
    instantaneous_hotwater: false,
    declared_loss_factor_known: false,
    manufacturer_loss_factor: 0,
    temperature_factor_a: 0,
    storage_volume: 160,
    loss_factor_b: 0.0191,
    volume_factor_b: 0.908,
    temperature_factor_b: 0.6,
    contains_dedicated_solar_storage_or_WWHRS: true,
    Vs: 0, // Vs is Vww from Appendix G3 or (H11) from Appendix H
    pipework_insulated_fraction: 0,
    hot_water_control_type: "no_cylinder_thermostat",
    community_heating: true,
    combi_loss: [0,0,0,0,0,0,0,0,0,0,0,0],
    solar_water_heating: true,
    solar_hot_water_contribution: [0,0,0,0,0,0,0,0,0,0,0,0],
    hot_water_store_in_dwelling: true,
  },

  calc: function()
  {
    var i = this.input;
    // Calculation of occupancy based on total floor area
    if (i.TFA > 13.9) {
      N = 1 + 1.76 * (1 - Math.exp(-0.000349 * Math.pow((i.TFA -13.9),2))) + 0.0013 * (i.TFA - 13.9);
    } else {
      N = 1;
    }
    
    if (i.use_manual_occupancy) N = i.manual_occupancy;

    var Vd_average = (25 * N) + 36;
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
    
    for (var m=0; m<12; m++) {
      Vd_m[m] = table_1c[m] * Vd_average;
      monthly_energy_content[m] = (4.190 * Vd_m[m] * table_1a[m] * table_1d[m]) / 3600;
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
    
    for (var m=0; m<12; m++) {
      hot_water_heater_output[m] = total_heat_required[m] + i.solar_hot_water_contribution[m];
      if (hot_water_heater_output[m]<0) hot_water_heater_output[m] = 0;
      
      if (i.hot_water_store_in_dwelling || i.community_heating) {
        heat_gains_from_water_heating[m] = 0.25 * (0.85*monthly_energy_content[m]+i.combi_loss[m]) + 0.8*(distribution_loss[m]+monthly_storage_loss[m]+primary_circuit_loss[m]);
      } else {
        heat_gains_from_water_heating[m] = 0.25 * (0.85*monthly_energy_content[m]) + 0.8*(distribution_loss[m]+primary_circuit_loss[m]);
      }
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
      N: N,
      Vd_average: Vd_average,
      Vd_m: Vd_m,
      monthly_energy_content: monthly_energy_content,
      distribution_loss: distribution_loss,
      monthly_storage_loss: monthly_storage_loss,
      primary_circuit_loss: primary_circuit_loss,
      total_heat_required: total_heat_required,
      hot_water_heater_output: hot_water_heater_output,
      heat_gains_from_water_heating: heat_gains_from_water_heating
    };
  }
}
