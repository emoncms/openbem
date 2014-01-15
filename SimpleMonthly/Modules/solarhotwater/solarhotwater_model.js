var solarhotwater_model = { 

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.solarhotwater!=undefined) {
      for (z in inputdata.solarhotwater.input) this.input[z] = inputdata.solarhotwater.input[z];
    }
    
    // Input dependencies
    this.input.region = inputdata.region;
    
    if (inputdata.waterheating!=undefined) {
      this.input.annual_hotwater_energy_content = inputdata.waterheating.output.annual_energy_content;
    }

  },
  
  input: {
    A: 1.25,          // Aperture Area 
    n0: 0.599,        // Zero-loss collector efficiency
    a1: 2.772,        // W/m2K, Collector linear heat loss coefficient
    a2: 0.009,        // W/m2K2, Collector 2nd order heat loss coefficient
    region: 13,
    orientation: 4,   // 0:North 1:NE/NW 2:East/West 3:SE/SW 4:South
    inclination: 35,
    overshading: 1.0,
    dedicated_storage: 168,
    Vs: 168,
    combined_cylinder_volume: 0,
    Vd_average: 125,
    annual_hotwater_energy_content: 0
  },
  
  calc: function ()
  {
    // 43 Annual average hot water usage in litres per day Vd,average
    // sum(45m) = Energy content of hot water used
    
    var i = this.input;

    var a = 0.892 * (i.a1 + 45 * i.a2);
    var collector_performance_ratio = a / i.n0;
    var annual_solar = annual_solar_rad(i.region,i.orientation,i.inclination);
    
    var solar_energy_available = i.A * i.n0 * annual_solar * i.overshading;
    
    var solar_load_ratio = solar_energy_available / i.annual_hotwater_energy_content;
    
    var utilisation_factor = 0;
    if (solar_load_ratio > 0) utilisation_factor = 1 - Math.exp(-1/(solar_load_ratio));
    
    var collector_performance_factor = 0;
    if (collector_performance_ratio < 20) {
      collector_performance_factor = 0.97 - 0.0367 * collector_performance_ratio + 0.0006 * Math.pow(collector_performance_ratio,2);
    } else {
      collector_performance_factor = 0.693 - 0.0108 * collector_performance_ratio;
    }
    if (collector_performance_factor<0) collector_performance_factor = 0;
    
    var Veff = 0;
    if (i.combined_cylinder_volume>0) { 
      Veff = i.Vs + 0.3 * (i.combined_cylinder_volume - i.Vs);
    } else {
      Veff = i.Vs;
    }
    
    var volume_ratio = Veff / i.Vd_average;
    var f2 = 1 + 0.2 * Math.log(volume_ratio);
    if (f2>1) f2 = 1;
    var Qs = solar_energy_available * utilisation_factor * collector_performance_factor * f2;
    
    
    // The solar input (in kWh) for month m is 
    
    var sum = 0;
    for (var m=0; m<12; m++) sum += solar_rad(i.region,i.orientation,i.inclination,m);
    var annualAverageSolarIrradiance = sum / 12;
    
    var Qs_monthly = [];
    for (m=0; m<12; m++)
    {
      var fm = solar_rad(i.region,i.orientation,i.inclination,m) / annualAverageSolarIrradiance;
      Qs_monthly[m] = - Qs * fm * table_1a[m] / 365;
    }
    
    // Variables to return as outputs
    return {
      a: a,
      collector_performance_ratio: collector_performance_ratio,
      annual_solar: annual_solar,
      solar_energy_available: solar_energy_available,
      solar_load_ratio: solar_load_ratio,
      utilisation_factor: utilisation_factor,
      collector_performance_factor: collector_performance_factor,
      Veff: Veff,
      volume_ratio: volume_ratio,
      f2: f2,
      Qs: Qs,
      Qs_monthly: Qs_monthly
    }   
  }

}


