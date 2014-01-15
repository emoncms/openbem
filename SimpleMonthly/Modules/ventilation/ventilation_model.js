var ventilation_model =
{
  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.ventilation!=undefined) {
      for (z in inputdata.ventilation.input) this.input[z] = inputdata.ventilation.input[z];
    }
    
    // Input dependencies
    this.input.region = inputdata.region;
    this.input.dwelling_volume = inputdata.volume;
  },
  // Public variables
  
  // input (defaults)
  input: {
    number_of_chimneys: 0,
    number_of_openflues: 1,
    number_of_intermittentfans: 0,
    number_of_passivevents: 0,
    number_of_fluelessgasfires: 0,
    
    dwelling_volume: 0,
    dwelling_storeys: 1,
    dwelling_construction: 'timberframe',        // 'timberframe' or 'masonry'
    
    suspended_wooden_floor: 0,        // 'unsealed' or 'sealed' or 0
    draught_lobby: true,
    percentage_draught_proofed: 50,    // percentage of windows and doors
    air_permeability_test: false,     
    air_permeability_value: 0,        // 0 or value
    
    number_of_sides_sheltered: 2, 
    
    region: 0,
    
    // Ventilation types:
    // a) Balanced mechanical ventilation with heat recovery (MVHR)
    // b) Balanced mechanical ventilation without heat recovery (MV)
    // c) Whole house extract ventilation or positive input ventilation from outside
    // d) Natural ventilation or whole house positive input ventilation from loft
    
    ventilation_type: 'd',
    
    system_air_change_rate: 1,
    balanced_heat_recovery_efficiency: 80
  },
  
  calc: function()
  { 
    var total = 0;
    total += this.input.number_of_chimneys * 40;
    total += this.input.number_of_openflues * 20;
    total += this.input.number_of_intermittentfans * 10;
    total += this.input.number_of_passivevents * 10;
    total += this.input.number_of_fluelessgasfires * 10;
    var infiltration = total / this.input.dwelling_volume;
    
    if (this.input.air_permeability_test==false) 
    { 
      infiltration += (this.input.dwelling_storeys - 1) * 0.1;
      
      if (this.input.dwelling_construction=='timberframe') infiltration += 0.2;
      if (this.input.dwelling_construction=='masonry') infiltration += 0.35;
      
      if (this.input.suspended_wooden_floor=='unsealed') infiltration += 0.2;
      if (this.input.suspended_wooden_floor=='sealed') infiltration += 0.1;
      
      if (!this.input.draught_lobby) infiltration += 0.05;
      
      // Window infiltration
      infiltration += (0.25 - (0.2 * this.input.percentage_draught_proofed / 100 ));
    }
    else
    {
      infiltration += this.input.air_permeability_value / 20.0;
    }
    
    var shelter_factor = 1 - (0.075 * this.input.number_of_sides_sheltered);

    infiltration *= shelter_factor;

    var adjusted_infiltration = [];
    for (var m = 0; m<12; m++)
    {
      var windspeed = table_u2[this.input.region][m];
      var windfactor = windspeed / 4;
      adjusted_infiltration[m] = infiltration * windfactor;
    }
    
    // (24a)m effective_air_change_rate
    // (22b)m adjusted_infiltration
    // (23b)  this.input.effective_air_change_rate.exhaust_air_heat_pump
    // (23c)  this.input.balanced_heat_recovery_efficiency
    var effective_air_change_rate = [];
    switch(this.input.ventilation_type)
    {
    case 'a':
      for (var m = 0; m<12; m++)
      {
        // (24a)m = (22b)m + (23b) x (1 - (23c) / 100)
        effective_air_change_rate[m] = adjusted_infiltration[m] + this.input.system_air_change_rate * (1 - this.input.balanced_heat_recovery_efficiency / 100.0);
      }
      break;
    case 'b':
      for (var m = 0; m<12; m++)
      {
        // (24b)m = (22b)m + (23b)
        effective_air_change_rate[m] = adjusted_infiltration[m] + this.input.system_air_change_rate;
      }
      break;
    case 'c':
      for (var m = 0; m<12; m++)
      {
        // if (22b)m < 0.5 × (23b), then (24c) = (23b); otherwise (24c) = (22b) m + 0.5 × (23b)
        // effective_air_change_rate[m] = 
        if (adjusted_infiltration[m] < 0.5 * this.input.system_air_change_rate) {
          effective_air_change_rate[m] = this.input.system_air_change_rate;
        } else {
          effective_air_change_rate[m] = adjusted_infiltration[m] + (0.5 * this.input.system_air_change_rate);
        }
        
      }
      break;
    case 'd':
      for (var m = 0; m<12; m++)
      {
        // if (22b)m ≥ 1, then (24d)m = (22b)m otherwise (24d)m = 0.5 + [(22b)m2 × 0.5]
        if (adjusted_infiltration[m] >= 1) {
          effective_air_change_rate[m] = adjusted_infiltration[m];
        } else {
          effective_air_change_rate[m] = 0.5 + Math.pow(adjusted_infiltration[m],2) * 0.5;
        }
      }
      break;
    }
    
    var infiltration_WK = [];
    for (var m = 0; m<12; m++)
    {
      infiltration_WK[m] = effective_air_change_rate[m] * this.input.dwelling_volume * 0.33;
    }
    
    var output = {};
    output.effective_air_change_rate = effective_air_change_rate;
    output.infiltration_WK = infiltration_WK;
    
    return output;
    
  }
    
}

function ventilation_savetoinputdata(inputdata,o)
{
  inputdata.losses['ventilation'] = o.infiltration_WK;
}

function ventilation_customview(i)
{

  if (i.ventilation_type=='a') {
    $("#system_air_change_rate_div").show();
    $("#balanced_heat_recovery_efficiency_div").show();
  }
  
  if (i.ventilation_type=='b') {
    $("#system_air_change_rate_div").show();
    $("#balanced_heat_recovery_efficiency_div").hide();
  }
  
  if (i.ventilation_type=='c') {
    $("#system_air_change_rate_div").show();
    $("#balanced_heat_recovery_efficiency_div").hide();
  }
  
  if (i.ventilation_type=='d') {
    $("#system_air_change_rate_div").hide();
    $("#balanced_heat_recovery_efficiency_div").hide();
  }

  if (i.air_permeability_test==true) {
    $("#air_permeability_value_tbody").show();
    $("#structural").hide();
  } else {
    $("#air_permeability_value_tbody").hide();
    $("#structural").show();  
  }
}
