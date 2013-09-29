var model =
{

  // Public variables
  
  // input (defaults)
  input: {
    region: 0,
    airchanges: 0,
    volume: 0,
    elements: []
  },
  
  calc: function()
  { 
    var infiltration_WK = model.input.airchanges * model.input.volume * 0.33;

    var mean_internal_temperature = model.input.mean_internal_temperature; //[18,18,18,18,18,18,18,18,18,18,18,18];

    var total_fabric_heat_loss_WK = 0;
    var total_thermal_capacity = 0;
    var windows = [];

    for (i in model.input.elements)
    {
      var id = model.input.elements[i].lib;
     
      total_fabric_heat_loss_WK += element_library[id].uvalue*model.input.elements[i].area;
      if (element_library[id].kvalue!=undefined) total_thermal_capacity += element_library[id].kvalue*model.input.elements[i].area;
      
      // Create specific windows array to pass to solar gains calculation
      if (element_library[id].type=='Window') {
      
        windows.push({
          orientation:model.input.elements[i].orientation, 
          area:model.input.elements[i].area, 
          overshading: model.input.elements[i].overshading, 
          g: element_library[id].g, 
          ff: element_library[id].ff
        });

      }
    }
    
    
    var total_heat_loss_WK = total_fabric_heat_loss_WK+infiltration_WK;
    // Display monthly solar gains and calculate average
    var solargains = calc_solar_gains_from_windows(windows,model.input.region);

    var sum = 0;
    for (z in solargains) sum += solargains[z];
    var average_solargains = sum / 12.0;
    
    var temperature_difference = [];
    var heat_demand = [];
    var heating_system_demand = [];
    
    var sum = 0;
    for (var m=0; m<12; m++)
    {
      // calculations
      temperature_difference[m] = mean_internal_temperature[m] - table_u1[model.input.region][m];
      heat_demand[m] = temperature_difference[m] * (total_fabric_heat_loss_WK + infiltration_WK);
      heating_system_demand[m] = heat_demand[m] - solargains[m] - 0;
      sum += heating_system_demand[m];
    }

    var annual_heating_demand = ((sum / 12.0) * 0.024 * 365);
    var annual_fuel_input = annual_heating_demand/ (model.input.heating_system_efficiency / 100.0);
    var annual_heating_cost = annual_fuel_input * model.input.fuel_cost;
    var annual_energy_cost = annual_fuel_input * model.input.fuel_cost;

    var ECF = (annual_energy_cost * 0.42) / ((6*3.5) + 45.0);
    var sap_rating = 0;

    if (ECF >= 3.5) sap_rating = 117 - 121 * (Math.log(ECF) / Math.LN10);
    if (ECF < 3.5) sap_rating = 100 - 13.95 * ECF;

    sap_rating = Math.round(sap_rating);

    var band = 0;
    for (z in ratings)
    {
      if (sap_rating>=ratings[z].start && sap_rating<=ratings[z].end) {band = z; break;}
    }

    /*
     * Transfer to result variables
     *
     *
    */

    var result = {};
   
    // Heat loss factors
    result.infiltration_WK = infiltration_WK;
    result.total_fabric_heat_loss_WK = total_fabric_heat_loss_WK;
    result.total_heat_loss_WK = total_heat_loss_WK;
    
    // Thermal capacity
    result.total_thermal_capacity = total_thermal_capacity; 
    
    // Solar gains   
    result.solargains = solargains;
    result.average_solargains = average_solargains;
    
    // Monthly temperature difference and heat demand
    result.mean_internal_temperature = mean_internal_temperature;
    result.temperature_difference = temperature_difference;
    result.heat_demand = heat_demand;
    result.heating_system_demand = heating_system_demand;
    
    // Annual totals
    result.annual_heating_demand = annual_heating_demand;
    result.annual_fuel_input = annual_fuel_input;
    result.annual_heating_cost = annual_heating_cost;
    result.annual_energy_cost = annual_energy_cost;
    
    // SAP result 
    result.sap_rating = sap_rating;
    result.band = band;
    
    return result;
  }
};
