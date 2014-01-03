var example_model =
{
  // Public variables
  
  // input (defaults)
  input: {
    aperture_area: 1.25,
    gross_area: 0,
    n0: 0.599,
    a1: 2.772,
    a2: 0.009,
    orientation: 4, // 0:North 1:NE/NW 2:East/West 3:SE/SW 4:South
    inclination: 35,
    overshading: 1.0,
    dedicated_storage: 168,
    
    
    
    
  },
  
  set_inputdata: function(inputdata)
  {
    for (z in inputdata)
    { 
      this.input[z] = inputdata[z];
    }
  },
  
  calc: function()
  { 
 
    var a = 0.892 * (this.input.a1 + 45 * this.input.a2);
    var collector_performance_ratio = a / this.input.n0;
    
    var solar_energy_available = this.input.aperture_area * this.input.n0 * annual_solar_rad * this.input.overshading;
  
    var solar_to_load_ratio = solar_energy_available / 45m;
    
    var utilisation_factor = 0;
    if (solar_to_load_ratio>0) utilisation_factor = 1 - Math.exp(-1/solar_to_load_ratio);
  
    if (this.input.nocylinderstat) utilisation_factor *= 0.90;
    
    if (collector_performance_ratio<20) 0.97 - 0.0367 * collector_performance_factor
    
    var result = {};
    result.c = c;
    
    return result;
  }
  
};
