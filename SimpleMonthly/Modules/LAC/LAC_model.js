
/*

Lighting 

*/

var LAC_model = {

  input: {
    TFA: 45,
    N: 2,
    LLE: 10,  // LLE is the number of fixed low energy lighting outlets
    L: 10,    // L is the total number of fixed lighting outlets
    reduced_internal_heat_gains: false,
    elements: {}
  },
  
  calc: function ()
  { 
    var i = this.input;
    // average annual energy consumption for lighting if no low-energy lighting is used is:
    var EB = 59.73 * Math.pow((i.TFA * i.N),0.4714);

    var C1 = 1 - (0.50 * i.LLE / i.L);

    var windows = [];
    for (z in i.elements)
    {
      var id = i.elements[z].lib;
      
      // Create specific windows array to pass to solar gains calculation
      if (element_library[id].type=='Window') {
      
        windows.push({
          orientation: i.elements[z].orientation, 
          area: i.elements[z].area, 
          overshading: i.elements[z].overshading, 
          g: element_library[id].g, 
          ff: element_library[id].ff
        });

      }
    }

    var sum = 0;
    for (z in windows) 
    {
      var overshading = windows[z].overshading;
      var accessfactor = [0.5,0.67,0.83,1.0];
      sum += 0.9 * windows[z].area * windows[z].g * windows[z].ff * accessfactor[overshading];
    }

    var GL = sum / i.TFA;

    var C2 = 0;
    if (GL<=0.095) {
      C2 = 52.2 * Math.pow(GL,2) - 9.94 * GL + 1.433;
    } else {
      C2 = 0.96;
    }

    var EL = EB * C1 * C2;

    var EL_monthly = [];
    var GL_monthly = [];
    
    var EL_sum = 0;
    for (var m=0; m<12; m++) { 
      EL_monthly[m] = EL * (1.0 + (0.5 * Math.cos((2*Math.PI * (m - 0.2))/12.0))) * table_1a[m] / 365.0;
      EL_sum += EL_monthly[m];
      
      GL_monthly[m] = EL_monthly[m] * 0.85 * 1000 / (24 * table_1a[m]);
      if (i.reduced_internal_heat_gains) GL_monthly[m] = 0.4 * EL_monthly[m];  
    }

    /*

    Electrical appliances

    */

    // The initial value of the annual energy use in kWh for electrical appliances is
    var EA_initial = 207.8 * Math.pow((i.TFA * i.N),0.4714);

    var EA_monthly = [];
    var GA_monthly = [];
    var EA = 0; // Re-calculated the annual total as the sum of the monthly values
    for (var m=0; m<12; m++)
    {
      // The appliances energy use in kWh in month m (January = 1 to December = 12) is
      EA_monthly[m] = EA_initial * (1.0 + (0.157 * Math.cos((2*Math.PI * (m - 1.78))/12.0))) * table_1a[m] / 365.0;
      EA += EA_monthly[m];

      GA_monthly[m] = EA_monthly[m] * 1000 / (24 * table_1a[m]);
      if (i.reduced_internal_heat_gains) GA_monthly[m] = 0.67 * GA_monthly[m];
    }

    // The annual CO2 emissions in kg/m2/year associated with electrical appliances is
    var appliances_CO2 = (EA * 0.522 ) / i.TFA;

    /*

    Cooking

    */

    // Internal heat gains in watts from cooking
    var GC = 35 + 7 * i.N; 
    
    // When lower internal heat gains are assumed for the calculation
    if (i.reduced_internal_heat_gains) GC = 23 + 5 * i.N;
    
    // CO2 emissions in kg/m2/year associated with cooking
    var cooking_CO2 = (119 + 24 * i.N) / i.TFA;
  
    return {
      EB: EB.toFixed(0),
      C1: C1.toFixed(2),
      C2: C2.toFixed(2),
      GL: GL,
      EL: EL.toFixed(0),
      EL_monthly: {title:"Lighting energy use:", data:EL_monthly, dp:0, units:" kWh"},
      GL_monthly: {title:"Lighting internal heat gain<br> for each month:", data:GL_monthly, dp:0, units:"W"},
      EL_sum: EL_sum,
      
      EA_initial: EA_initial.toFixed(0),
      EA_monthly: {title:"Appliance energy use:", data:EA_monthly, dp:0, units:" kWh"},
      EA: EA.toFixed(0),
      GA_monthly: {title:"Appliance internal heat gain<br>for each month:", data:GA_monthly, dp:0, units:"W"},
      appliances_CO2: appliances_CO2,
      
      GC: GC,
      cooking_CO2: cooking_CO2
    }
  }
  
}

