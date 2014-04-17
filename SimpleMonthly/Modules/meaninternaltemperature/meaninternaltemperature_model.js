var meaninternaltemperature_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.meaninternaltemperature!=undefined) {
      for (z in inputdata.meaninternaltemperature.input) this.input[z] = inputdata.meaninternaltemperature.input[z];
    }
    
    // Input dependencies
    this.input.TFA = inputdata.TFA;
    this.input.region = inputdata.region;
    this.input.altitude = inputdata.altitude;
    
    if (inputdata.elements!=undefined) {
      this.input.TMP = inputdata.elements.output.TMP;
    }
    
    for (var m=0; m<12; m++)
    {
      var month_total = 0;
      for (z in inputdata.gains) month_total += inputdata.gains[z][m];
      this.input.G[m] = month_total;
    }
  
    for (var m=0; m<12; m++)
    {
      var month_total = 0;
      for (z in inputdata.losses) month_total += inputdata.losses[z][m];
      this.input.H[m] = month_total;
      this.input.HLP[m] = month_total / this.input.TFA;
    }
    
  },
  
  input: {
    region:0,
    Th: 21,  // temperature_during_heating_periods
    TMP: 0,   // thermal mass parameter
    HLP: [0,0,0,0,0,0,0,0,0,0,0,0],   // heat loss parameter
    H: [0,0,0,0,0,0,0,0,0,0,0,0],     // heat transfer coefficient (W/K)
    Te: [0,0,0,0,0,0,0,0,0,0,0,0],    // external temperature
    G: [0,0,0,0,0,0,0,0,0,0,0,0],     // total gains
    R: 1,                             // heating system responsiveness
    living_area: 35,
    TFA:0,
    altitude:0,
    control_type: 1
  },
  
  calc: function ()
  { 
    var i = this.input;

    for (var m =0; m<12; m++)
    {
      i.Te[m] = table_u1[i.region][m]-(0.3*i.altitude/50);
    }
    //----------------------------------------------------------------------------------------------------------------
    // 7. Mean internal temperature (heating season)
    //----------------------------------------------------------------------------------------------------------------

    // Bring calculation of (96)m forward as its used in section 7.
    // Monthly average external temperature from Table U1
    // for (var i=1; i<13; i++) data['96-'+i] = table_u1[i.region][i-1]-(0.3 * i.altitude / 50);

    // See utilisationfactor.js for calculation
    // Calculation is described on page 159 of SAP document
    // Would be interesting to understand how utilisation factor equation 
    // can be derived
    
    var utilisation_factor_A = [];
    for (var m=0; m<12; m++) 
    { 
      utilisation_factor_A[m] = calc_utilisation_factor(i.TMP,i.HLP[m],i.H[m],i.Th,i.Te[m],i.G[m]);
    }

    // Table 9c: Heating requirement
    // Living area
    // 1. Set Ti to the temperature for the living area during heating periods (Table 9)
    // 2. Calculate the utilisation factor (Table 9a)
    // 3. Calculate the temperature reduction (Table 9b) for each off period (Table 9), u1 and u2, for weekdays

    var Ti_livingarea = [];
    for (var m=0; m<12; m++) 
    { 
      var Th = i.Th; // 21C;
      var Ti = i.Th;

      // (TMP,HLP,H,Ti,Te,G, R,Th,toff)
      var u1a = calc_temperature_reduction(i.TMP,i.HLP[m],i.H[m],Ti,i.Te[m],i.G[m],i.R,Th,7);
      var u1b = calc_temperature_reduction(i.TMP,i.HLP[m],i.H[m],Ti,i.Te[m],i.G[m],i.R,Th,0);
      var u2 =  calc_temperature_reduction(i.TMP,i.HLP[m],i.H[m],Ti,i.Te[m],i.G[m],i.R,Th,8);

      var Tweekday = Th - (u1a + u2);
      var Tweekend = Th - (u1b + u2);
      Ti_livingarea[m] = (5*Tweekday + 2*Tweekend) / 7;
    }

    // rest of dwelling
    var Th2 = [];
    for (var m=0; m<12; m++) {
      // see table 9 page 159
      if (i.control_type==1) Th2[m] = i.Th - 0.5 * i.HLP[m];
      if (i.control_type==2) Th2[m] = i.Th - i.HLP[m] + (Math.pow(i.HLP[m],2) / 12);
      if (i.control_type==3) Th2[m] = i.Th - i.HLP[m] + (Math.pow(i.HLP[m],2) / 12);
      //Th2[m] = i.Th - i.HLP[m] + 0.085 *Math.pow(i.HLP[m],2);
      
      if (isNaN(Th2[m])) Th2[m] = i.Th;
    }

    var utilisation_factor_B = [];
    for (var m=0; m<12; m++) 
    { 
      var Ti = Th2[m];
      var HLP = i.HLP[m];
      if (HLP>6.0) HLP = 6.0;
      // TMP,HLP,H,Ti,Te,G  
      utilisation_factor_B[m] = calc_utilisation_factor(i.TMP,HLP,i.H[m],Ti,i.Te[m],i.G[m]);
    }

    var Ti_restdwelling = [];
    for (var m=0; m<12; m++) 
    { 
      var Th = Th2[m];
      var Ti = Th2[m];

      var u1a = calc_temperature_reduction(i.TMP,i.HLP[m],i.H[m],Ti,i.Te[m],i.G[m],i.R,Th,7);
      var u1b = calc_temperature_reduction(i.TMP,i.HLP[m],i.H[m],Ti,i.Te[m],i.G[m],i.R,Th,0);
      var u2 =  calc_temperature_reduction(i.TMP,i.HLP[m],i.H[m],Ti,i.Te[m],i.G[m],i.R,Th,8);

      var Tweekday = Th - (u1a + u2);
      var Tweekend = Th - (u1b + u2);
      Ti_restdwelling[m] = (5*Tweekday + 2*Tweekend) / 7;
    }

    var fLA = i.living_area / i.TFA;
    if (isNaN(fLA)) fLA = 0;

    var MIT = [];
    for (var m=0; m<12; m++) 
    { 
      MIT[m] = (fLA * Ti_livingarea[m]) + (1 - fLA) * Ti_restdwelling[m];
    }

    return {
      utilisation_factor_A: utilisation_factor_A,
      Ti_livingarea: Ti_livingarea,
      Th2: Th2,
      utilisation_factor_B: utilisation_factor_B,
      Ti_restdwelling: Ti_restdwelling,
      MIT: MIT
    }
  }
  
}

function meaninternaltemperature_savetoinputdata(inputdata,o)
{
  inputdata.MIT = o.MIT;
}

