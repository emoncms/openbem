var saprating_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.saprating!=undefined) {
      for (z in inputdata.saprating.input) this.input[z] = inputdata.saprating.input[z];
    }
    
    this.input.TFA = inputdata.TFA;

    if (inputdata.fuelcosts!=undefined) {
      this.input.total_energy_cost = inputdata.fuelcosts.output.total_energy_cost;
    }
    
  },
  
  input: {
    total_energy_cost: 1000,
    energy_cost_deflator: 0.47,
    TFA: 35,
  },
  
  calc: function ()
  { 
    var i = this.input;
    
    var energy_cost_factor = (i.total_energy_cost * i.energy_cost_deflator) / (i.TFA + 45.0);
 
    var sap_rating = 0;
    if (energy_cost_factor >= 3.5) sap_rating = 117 - 121 * (Math.log(energy_cost_factor) / Math.LN10);
    if (energy_cost_factor < 3.5) sap_rating = 100 - 13.95 * energy_cost_factor;
  
    return {
      energy_cost_factor: energy_cost_factor,
      sap_rating: sap_rating
    }
  }
  
}

