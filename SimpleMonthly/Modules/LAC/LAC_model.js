
/*

Lighting 

*/

var LAC_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.LAC!=undefined) {
      for (z in inputdata.LAC.input) this.input[z] = inputdata.LAC.input[z];
    }
    
    // Input dependencies
    this.input.TFA = inputdata.TFA;
    this.input.N = inputdata.occupancy;
    
    if (inputdata.elements!=undefined) {
      this.input.GL = inputdata.elements.output.GL;
    }
  },
  
  input: {
    TFA: 45,
    N: 2,
    LLE: 10,  // LLE is the number of fixed low energy lighting outlets
    L: 10,    // L is the total number of fixed lighting outlets
    GL: 0,
    reduced_internal_heat_gains: false,
    elements: {}
  },
  
  calc: function ()
  { 
    var i = this.input;
    // average annual energy consumption for lighting if no low-energy lighting is used is:
    var EB = 59.73 * Math.pow((i.TFA * i.N),0.4714);

    var C1 = 1 - (0.50 * i.LLE / i.L);

    var C2 = 0;
    if (i.GL<=0.095) {
      C2 = 52.2 * Math.pow(i.GL,2) - 9.94 * i.GL + 1.433;
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

    var GC_monthly = [];
    for (var m=0; m<12; m++) GC_monthly[m] = GC;
    
    // CO2 emissions in kg/m2/year associated with cooking
    var cooking_CO2 = (119 + 24 * i.N) / i.TFA;
  
    return {
      EB: EB,
      C1: C1,
      C2: C2,
      EL: EL,
      EL_monthly: EL_monthly,
      GL_monthly: GL_monthly,
      EL_sum: EL_sum,
      
      EA_initial: EA_initial,
      EA_monthly: EA_monthly,
      EA: EA,
      GA_monthly: GA_monthly,
      appliances_CO2: appliances_CO2,
      
      GC: GC,
      GC_monthly: GC_monthly,
      cooking_CO2: cooking_CO2
    }
  }
  
}

function LAC_savetoinputdata(inputdata,o)
{
  inputdata.gains['lighting'] = o.GL_monthly;
  inputdata.gains['appliances'] = o.GA_monthly;
  inputdata.gains['cooking'] = o.GC_monthly;
}
