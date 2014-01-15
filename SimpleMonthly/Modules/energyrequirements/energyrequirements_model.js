var energyrequirements_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.energyrequirements!=undefined) {
      for (z in inputdata.energyrequirements.input) this.input[z] = inputdata.energyrequirements.input[z];
    }

    this.input.TFA = inputdata.TFA;

    if (inputdata.balance!=undefined) {
      this.input.space_heating_requirement = inputdata.balance.output.heat_demand_kwh;
    }

    if (inputdata.waterheating!=undefined) {
      this.input.water_heater_output = inputdata.waterheating.output.hot_water_heater_output;
    }
    
    if (inputdata.LAC!=undefined) {
      this.input.lighting_use = inputdata.LAC.output.EL_sum;
      this.input.appliance_use = inputdata.LAC.output.EA;
      this.input.cooking_use = inputdata.LAC.output.GC * 0.024 * 365;
    }
    
  },
  
  input: {
    TFA: 0,
    space_heating_requirement: [100,0,0,0,0,0,0,0,0,0,0,0,0],
    fraction_secondary: 0,
    fraction_main2: 0,
    efficiency_main1: 82,
    efficiency_main2: 82,
    efficiency_secondary: 65,
    
    water_heater_output: [0,0,0,0,0,0 ,0,0,0,0,0,0],
    water_heater_efficiency: 100,
    
    MV_fans: 0,
    warm_air_fans: 0,
    central_heating_pump: 0,
    oil_boiler_pump: 0,
    boiler_flue_fan: 0,
    electric_keep_hot: 0,
    solar_water_heating_pump: 0,
    lighting_use: 0,
    appliance_use: 0,
    cooking_use: 0,
    
    electricity_generated_pv: 0,
    electricity_generated_wind: 0,
    electricity_generated_hydro: 0
  },
  
  calc: function ()
  { 
    var i = this.input;
    
    var fraction_main = 1 - i.fraction_secondary;
    var fraction_total_main1 = fraction_main * (1 - i.fraction_main2);
    var fraction_total_main2 = fraction_main * (i.fraction_main2);
    
    var space_heating_fuel_main1 = [];
    var space_heating_fuel_main2 = [];
    var space_heating_fuel_secondary = [];
    
    var annual_main1_fuel = 0;
    var annual_main2_fuel = 0; 
    var annual_secondary_fuel = 0;
    
    for (var m = 0; m<12; m++)
    {
      space_heating_fuel_main1[m] = i.space_heating_requirement[m] * fraction_total_main1 * 100 / i.efficiency_main1;
      annual_main1_fuel += space_heating_fuel_main1[m];
      
      space_heating_fuel_main2[m] = i.space_heating_requirement[m] * fraction_total_main2 * 100 / i.efficiency_main2;
      annual_main2_fuel += space_heating_fuel_main2[m];
      
      space_heating_fuel_secondary[m] = i.space_heating_requirement[m] * i.fraction_secondary * 100 / i.efficiency_secondary;
      annual_secondary_fuel += space_heating_fuel_secondary[m];
      
    }
    
    var water_heater_fuel = [];
    var annual_water_heating_fuel = 0;
    for (var m = 0; m<12; m++)
    {
      water_heater_fuel[m] = i.water_heater_output[m] * i.water_heater_efficiency / 100;
      annual_water_heating_fuel += water_heater_fuel[m];
    }
    
    var total_misc_electrical = i.MV_fans + i.warm_air_fans + i.central_heating_pump + i.oil_boiler_pump + i.boiler_flue_fan + i.electric_keep_hot + i.solar_water_heating_pump;
    
    var total_energy_requirement = annual_main1_fuel + annual_main2_fuel + annual_secondary_fuel + annual_water_heating_fuel + total_misc_electrical + i.lighting_use + i.appliance_use + i.cooking_use + i.electricity_generated_pv + i.electricity_generated_wind + i.electricity_generated_hydro;
    
    var total_energy_requirement_m2 = total_energy_requirement / i.TFA;
    
    return {
      fraction_main: fraction_main,
      fraction_total_main1: fraction_total_main1,
      fraction_total_main2: fraction_total_main2,
      
      space_heating_fuel_main1: space_heating_fuel_main1,
      space_heating_fuel_main2: space_heating_fuel_main2,
      space_heating_fuel_secondary: space_heating_fuel_secondary,
      
      water_heater_fuel: water_heater_fuel,
      
      annual_main1_fuel: annual_main1_fuel,
      annual_main2_fuel: annual_main2_fuel,
      annual_secondary_fuel: annual_secondary_fuel,
      annual_water_heating_fuel: annual_water_heating_fuel,
      
      total_misc_electrical: total_misc_electrical,
      total_energy_requirement: total_energy_requirement,
      total_energy_requirement_m2: total_energy_requirement_m2
    }
  }
  
}

