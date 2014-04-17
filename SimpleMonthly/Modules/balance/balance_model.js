

var balance_model = {

  set_from_inputdata: function (inputdata)
  {
    this.input.region = inputdata.region;
    this.input.gains = inputdata.gains;
    this.input.losses = inputdata.losses;
    this.input.MIT = inputdata.MIT;
    this.input.altitude = inputdata.altitude;
    this.input.TFA = inputdata.TFA;
    
    if (inputdata.elements!=undefined) {
      this.input.TMP = inputdata.elements.output.TMP;
    }
  },

  input: {
    region: 0,
    externaltemperature: [10,10,10 ,10,10,10 ,10,10,10 ,10,10,10],
    MIT: [21,21,21, 21,21,21, 21,21,21, 21,21,21],
    gains: {},
    losses: {},
    altitude:0,
    TFA:0,
    TMP:0,
    use_utilfactor_forgains: true,
  },
  
  calc: function ()
  { 
    var i = this.input;
    
    for (var m =0; m<12; m++)
    {
      i.externaltemperature[m] = table_u1[i.region][m]-(0.3*i.altitude/50);
    }
    
    var deltaT = [];
    for (var m=0; m<12; m++)
    {
      deltaT[m] = i.MIT[m] - i.externaltemperature[m];
    }
    
    var total_losses = [];
    
    var total_gains = [];
    var utilisation_factor = [];
    var useful_gains = [];
    
    for (var m=0; m<12; m++)
    {
      // Monthly loss totals
      var H = 0; // heat transfer coefficient
      for (z in i.losses) H += i.losses[z][m];
      total_losses[m] = H * deltaT[m];
      
      // Monthly gains total
      var month_total = 0;
      for (z in i.gains) month_total += i.gains[z][m];
      total_gains[m] = month_total;

      // HLP for utilisation calc
      var HLP = H / i.TFA; 
     
      utilisation_factor[m] = calc_utilisation_factor(i.TMP,HLP,H,i.MIT[m],i.externaltemperature[m],total_gains[m]);
      
      if (i.use_utilfactor_forgains) {
      useful_gains[m] = total_gains[m] * utilisation_factor[m];
      } else {
      useful_gains[m] = total_gains[m];
      }
    }
    
    var heat_demand = [];
    var cooling_demand = [];
    var heat_demand_kwh = [];
    var cooling_demand_kwh = [];
    var sum = 0;
    var cooling_sum = 0;
    for (var m=0; m<12; m++)
    {
      heat_demand[m] = total_losses[m] - useful_gains[m];
      cooling_demand[m] = 0;
      
      if (heat_demand[m]<0) {
        cooling_demand[m] = useful_gains[m] - total_losses[m];
        heat_demand[m] = 0;
      }
      
      sum += heat_demand[m];
      cooling_sum += cooling_demand[m];
      
      heat_demand_kwh[m] = 0.024 * heat_demand[m] * table_1a[m];
      cooling_demand_kwh[m] = 0.024 * cooling_demand[m] * table_1a[m];
    }
    
    var annual_heating_demand = ((sum / 12.0) * 0.024 * 365);
    var annual_cooling_demand = ((cooling_sum / 12.0) * 0.024 * 365);  
    return {
      total_gains: total_gains,
      utilisation_factor: utilisation_factor,
      useful_gains: useful_gains,
      total_losses: total_losses,
      heat_demand: heat_demand,
      cooling_demand: cooling_demand,
      cooling_demand_kwh: cooling_demand_kwh,
      heat_demand_kwh: heat_demand_kwh,
      deltaT: deltaT,
      annual_heat_demand:annual_heating_demand,
      annual_cooling_demand:annual_cooling_demand
    }
  }
  
}

function balance_customview(i)
{
  var out = "";
  for (z in i.losses)
  {
    out += "<tr><td>"+z+"</td>";
    for (m in i.losses[z])
    {
      out += "<td>"+i.losses[z][m].toFixed(0)+"WK</td>";
    }
    out += "</tr>";
  }
  $("#losses_list").html(out);
  
  var out = "";
  for (z in i.gains)
  {
    out += "<tr><td>"+z+"</td>";
    for (m in i.gains[z])
    {
      out += "<td>"+i.gains[z][m].toFixed(0)+"W</td>";
    }
    out += "</tr>";
  }
  $("#gains_list").html(out);
}

