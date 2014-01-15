var fuelcosts_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.fuelcosts!=undefined) {
      for (z in inputdata.fuelcosts.input) this.input[z] = inputdata.fuelcosts.input[z];
    }

    if (inputdata.energyrequirements!=undefined) {
      this.input.annual_main1_fuel = inputdata.energyrequirements.output.annual_main1_fuel;
      this.input.annual_main2_fuel = inputdata.energyrequirements.output.annual_main2_fuel;
      this.input.annual_secondary_fuel = inputdata.energyrequirements.output.annual_secondary_fuel;
      this.input.annual_water_heating_fuel = inputdata.energyrequirements.output.annual_water_heating_fuel;
      this.input.total_misc_electrical = inputdata.energyrequirements.output.total_misc_electrical;
      this.input.lighting_use = inputdata.energyrequirements.input.lighting_use;
    }
    
  },
  
  input: {
    annual_main1_fuel: 0,
    main1_fuel_price: 4.3,
    
    annual_main2_fuel: 0,
    main2_fuel_price: 4.3,
    
    annual_secondary_fuel: 0,
    secondary_fuel_price: 13.8,
    
    high_rate_fraction: 1,
    
    annual_water_heating_fuel: 0,
    high_rate_fuel_price: 14.5,
    low_rate_fuel_price: 8.1,
    
    water_heating_fuel: 0,
    water_heating_fuel_price: 14.5,
    
    total_misc_electrical: 0,
    total_misc_electrical_fuel_price: 14.5,
    
    lighting_use: 0,
    lighting_fuel_price: 14.5,
    
    standing_charges: 0,
    
  },
  
  calc: function ()
  { 
    var i = this.input;
  
    var main1_fuel_cost = i.annual_main1_fuel * i.main1_fuel_price * 0.01;
    var main2_fuel_cost = i.annual_main2_fuel * i.main2_fuel_price * 0.01;
    var secondary_fuel_cost = i.annual_secondary_fuel * i.secondary_fuel_price * 0.01;
    
    var low_rate_fraction = 1 - i.high_rate_fraction;
    
    var high_rate_fuel_cost = i.high_rate_fraction * i.annual_water_heating_fuel * i.high_rate_fuel_price * 0.01;
    
    var low_rate_fuel_cost = low_rate_fraction * i.annual_water_heating_fuel * i.low_rate_fuel_price * 0.01;

    var water_heating_fuel_cost = i.water_heating_fuel * i.water_heating_fuel_price * 0.01;
    var total_misc_electrical_cost = i.total_misc_electrical * i.total_misc_electrical_fuel_price * 0.01;
    var lighting_cost = i.lighting_use * i.lighting_fuel_price * 0.01;
    
    var total_energy_cost = main1_fuel_cost + main2_fuel_cost + secondary_fuel_cost + high_rate_fuel_cost + low_rate_fuel_cost + total_misc_electrical_cost + lighting_cost + i.standing_charges;
    
    return {
    
      main1_fuel_cost: main1_fuel_cost,
      main2_fuel_cost: main2_fuel_cost,
      secondary_fuel_cost: secondary_fuel_cost,
      
      low_rate_fraction: low_rate_fraction,
      
      low_rate_fraction_o: low_rate_fraction,
      high_rate_fraction_o: i.high_rate_fraction,
      
      high_rate_fuel_cost: high_rate_fuel_cost,
      low_rate_fuel_cost: low_rate_fuel_cost,
      
      water_heating_fuel_cost: water_heating_fuel_cost,
      total_misc_electrical_cost: total_misc_electrical_cost,
      lighting_cost: lighting_cost,
      
      total_energy_cost: total_energy_cost,
    }
  }
  
}

