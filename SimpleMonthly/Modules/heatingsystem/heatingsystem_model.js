var energysystems = {

  'heatpump':{name:"Heatpump", efficiency:3.0, fuel: 'electric'},
  'woodbatch':{name:"Wood batch boiler", efficiency:0.92, fuel: 'wood'},
  'woodpellet':{name:"Wood pellet boiler", efficiency:0.92, fuel: 'wood'},
  'woodstove':{name:"Wood stove", efficiency:0.87, fuel: 'wood'},
  'openwoodfire':{name:"Open wood fire", efficiency:0.25, fuel: 'wood'},

  'oilrangecooker':{name:"Oil range cooker", efficiency:0.55, fuel: 'oil'},
  'gasboiler':{name:"Gas boiler", efficiency:0.90, fuel: 'gas'},
  'oilboiler':{name:"Oil boiler", efficiency:0.85, fuel: 'oil'},
  
  'electricheater':{name:"Electric room heater", efficiency:1.0, fuel: 'electric'},
  'electricimmersion':{name:"Electric immersion heater", efficiency:1.0, fuel: 'electric'},
  
  'electric-high':{name:"High rate electric", efficiency:1.0, fuel: 'electric-high'},
  'electric-low':{name:"Low rate electric", efficiency:1.0, fuel: 'electric-low'},
  'electric':{name:"Electric", efficiency:1.0, fuel: 'electric'},
    
  'other-wood':{name:"Other wood", efficiency:1.0, fuel: 'wood'},
  'other-oil':{name:"Other oil", efficiency:1.0, fuel: 'oil'},   
  'other-gas':{name:"Other gas", efficiency:1.0, fuel: 'gas'},
  'other-electric':{name:"Other electric", efficiency:1.0, fuel: 'electric'},
}

var heatingsystem_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.heatingsystem!=undefined) {
      for (z in inputdata.heatingsystem.input) this.input[z] = inputdata.heatingsystem.input[z];
    }

    this.input.TFA = inputdata.TFA;
    
    if (inputdata.balance!=undefined) {
        
      if (this.input.energy_requirements.spaceheating==undefined) {
        this.input.energy_requirements.spaceheating = {name:"Space Heating", quantity: 0, suppliedby:[
          //{type:'gasboiler',fraction:0.95, efficiency:energysystems.gasboiler.efficiency}
        ]};
      }
      this.input.energy_requirements.spaceheating.quantity = inputdata.balance.output.annual_heat_demand;
    }

    if (inputdata.waterheating!=undefined && inputdata.waterheating_enabled) {
      if (this.input.energy_requirements.waterheating==undefined) {
        this.input.energy_requirements.waterheating = {name:"Hot water", quantity: 0, suppliedby:[
          //{type:'gasboiler',fraction:1.0, efficiency:energysystems.gasboiler.efficiency}
        ]};
      }
      this.input.energy_requirements.waterheating.quantity = inputdata.waterheating.output.annual_waterheating_demand;
    }
    
    if (inputdata.LAC!=undefined && inputdata.LAC_enabled) {
    
      if (this.input.energy_requirements.lighting==undefined) {
        this.input.energy_requirements.lighting = {name:"Lighting", quantity: 0, suppliedby:[{type:'electric',fraction:1.0, efficiency:energysystems.electric.efficiency}]};
      }

      if (this.input.energy_requirements.appliances==undefined) {
        this.input.energy_requirements.appliances = {name:"Appliances", quantity: 0, suppliedby:[{type:'electric',fraction:1.0, efficiency:energysystems.electric.efficiency}]};
      }

      if (this.input.energy_requirements.cooking==undefined) {
        this.input.energy_requirements.cooking = {name:"Cooking", quantity: 0, suppliedby:[{type:'electric',fraction:1.0, efficiency:energysystems.electric.efficiency}]};
      }
      
      this.input.energy_requirements.lighting.quantity = inputdata.LAC.output.EL_sum;
      this.input.energy_requirements.appliances.quantity = inputdata.LAC.output.EA;
      this.input.energy_requirements.cooking.quantity = inputdata.LAC.output.GC * 0.024 * 365;
    }
    
    if (inputdata.appliancelist!=undefined && inputdata.appliancelist_enabled) {
      if (this.input.energy_requirements.appliancelist==undefined) {
        this.input.energy_requirements.appliancelist = {name:"Electrical Appliances", quantity: 0, suppliedby:[{type:'electric',fraction:1.0, efficiency:energysystems.electric.efficiency}]};
      }
      this.input.energy_requirements.appliancelist.quantity = inputdata.appliancelist.output.annual_appliancelist_demand;
    }
    
    if (!inputdata.LAC_enabled) delete this.input.energy_requirements.lighting;
    if (!inputdata.LAC_enabled) delete this.input.energy_requirements.appliances;
    if (!inputdata.LAC_enabled) delete this.input.energy_requirements.cooking;
    if (!inputdata.waterheating_enabled) delete this.input.energy_requirements.waterheating;
    if (!inputdata.appliancelist_enabled) delete this.input.energy_requirements.appliancelist;
  },
  
  input: {
    TFA: 0,
    energy_cost_deflator: 0.47,
    energy_requirements: {},
    fuels: {
      'oil':{fuelcost:0.051},
      'gas':{fuelcost:0.043},
      'wood':{fuelcost:0.00},
      'electric':{fuelcost:0.145},
      'electric-high':{fuelcost:0.155},
      'electric-low':{fuelcost:0.07},
    }
  },
  
  calc: function ()
  { 
    var i = this.input;
    
    var fueltotals = {};
    
    var total_finaluse_requirement = 0;
    var total_primaryenergy_requirement = 0;
    
    for (z in i.energy_requirements)
    {
      var quantity = i.energy_requirements[z].quantity;
      
      total_finaluse_requirement += quantity;
      
      for (x in i.energy_requirements[z].suppliedby)
      {
        var type = i.energy_requirements[z].suppliedby[x].type;
        var fraction = i.energy_requirements[z].suppliedby[x].fraction;
        var efficiency = i.energy_requirements[z].suppliedby[x].efficiency;
        var fuel = energysystems[type].fuel;
        
        if (fueltotals[fuel]==undefined) fueltotals[fuel] = {quantity:0};
        fueltotals[fuel].quantity += (quantity * fraction) / efficiency;
      }
    }
    
    var total_cost = 0; 
    for (z in fueltotals)
    {
      total_primaryenergy_requirement += fueltotals[z].quantity;
      
      fueltotals[z].annualcost = fueltotals[z].quantity * i.fuels[z].fuelcost;
      fueltotals[z].fuelcost = i.fuels[z].fuelcost;
      total_cost += fueltotals[z].annualcost;
    }
    
    var energy_cost_factor = (total_cost * i.energy_cost_deflator) / (i.TFA + 45.0);
 
    var sap_rating = 0;
    if (energy_cost_factor >= 3.5) sap_rating = 117 - 121 * (Math.log(energy_cost_factor) / Math.LN10);
    if (energy_cost_factor < 3.5) sap_rating = 100 - 13.95 * energy_cost_factor;
  
    
    return {
      fueltotals: fueltotals,
      total_cost: total_cost,
      energy_cost_factor: energy_cost_factor,
      sap_rating: sap_rating,
      
      total_finaluse_requirement: total_finaluse_requirement,
      total_primaryenergy_requirement: total_primaryenergy_requirement
    }
  }
  
}

function heatingsystem_customcontroller(module)
{
  // Show edit button only on hover
  $("#test").on("click",'.delete-system', function() {
    var sid = $(this).attr('sid');
    var eid = $(this).attr('eid');
    i.energy_requirements[eid].suppliedby.splice(sid,1);
    openbem_update(module);
  });
  
  $("#test").on("click",'.add-system', function() {
    var eid = $(this).attr('eid');
    var sid = $(this).attr('sid'); 
    
    // Calculate remaining fraction needed
    var fraction = 1.0;
    for (z in i.energy_requirements[eid].suppliedby) {
      fraction = fraction - i.energy_requirements[eid].suppliedby[z].fraction;
    }
    if (fraction<0.01) fraction = 0;
    
    var default_type = 'electric';
    
    i.energy_requirements[eid].suppliedby.push({type:default_type,fraction:fraction,efficiency:energysystems[default_type].efficiency});
    
    openbem_update(module);
    
    var sid = i.energy_requirements[eid].suppliedby.length-1;
    
    draw_inline_editing(eid,sid);
    
  });
  
  $("#test").on("change",'#energysystemselect', function() {
    var system = $(this).val();
    var sid = $(this).attr('sid');
    var eid = $(this).attr('eid');
    i.energy_requirements[eid].suppliedby[sid].type = system;
    i.energy_requirements[eid].suppliedby[sid].efficiency = energysystems[system].efficiency;
    openbem_update(module); 
    //draw_inline_editing(eid,sid);
  });
  
  $("#test").on("change",'#editfraction', function() {
    var fraction = $(this).val();
    var sid = $(this).attr('sid');
    var eid = $(this).attr('eid');
    i.energy_requirements[eid].suppliedby[sid].fraction = fraction;
    openbem_update(module); 
    //draw_inline_editing(eid,sid);
  });
  
  $("#test").on("change",'#editefficiency', function() {
    var efficiency = $(this).val()*0.01;
    var sid = $(this).attr('sid');
    var eid = $(this).attr('eid');
    i.energy_requirements[eid].suppliedby[sid].efficiency = efficiency;
    openbem_update(module); 
    //draw_inline_editing(eid,sid);
  });
  
  $("#test").on("click",'.energysystemok', function() {
    openbem_update(module);
  });
  
  $("#test").on("click",'.systemtype,.systemfraction,.systemefficiency', function() {
    var sid = $(this).attr('sid');
    var eid = $(this).attr('eid');
    draw_inline_editing(eid,sid);
  });
  
  $("#add-energy-requirement").click(function(){
    var name = $("#add-energy-requirement-name").val(); 
    var quantity = $("#add-energy-requirement-quantity").val()*1;

    var varname = name.replace(/ /g,'');
    
    if (i.energy_requirements[varname]==undefined) {
      i.energy_requirements[varname] = {name:name, quantity: quantity, suppliedby:[]};
    }
    openbem_update(module); 
  });
  
  
  $("#test").on("mouseover",'tr',    
    function() {
      $('.delete-energy-requirements').hide();
      $(this).find("th:last > i").show();
    }
  );
  
  $("#test").on("click",'.delete-energy-requirements', function() {
    var z = $(this).attr('z');
    console.log("Delete: "+z);
    delete i.energy_requirements[z];
    openbem_update(module); 
  });
  
  $("#fuels-table").on("click",'.fuelcost', function() {
    var z = $(this).attr('z');
    var mode = $(this).attr('mode');
    if (mode == 'view')
    {
      $(this).attr('mode','edit');
      var fuelcost = o.fueltotals[z].fuelcost.toFixed(2);
    
      $(this).html("<div class='input-prepend input-append'><input class='fuelcostedit' z='"+z+"' type='text' style='width:40px' value='"+fuelcost+"' /><span class='add-on'>£/kwh <i class='icon-ok fuelcost-ok' z='"+z+"' style='cursor:pointer'></i></button></span>");
    }
  });
  
  $("#fuels-table").on("click",'.fuelcost-ok', function() {
    var z = $(this).attr('z');
    var newfuelcost = $(".fuelcostedit[z="+z+"]").val()*1;
    $(this).attr('mode','view');
    
    i.fuels[z].fuelcost = newfuelcost;
    openbem_update(module);
  });
  
}

function draw_inline_editing(eid,sid)
{
  var out = "";
  out += "<td><select id='energysystemselect' eid='"+eid+"' sid='"+sid+"' style='width:120px'>";
  
  for (z in energysystems) {
    out += "<option value='"+z+"'>"+energysystems[z].name+"</option>";
  }
  out += "</select></td>";

  var quantity = i.energy_requirements[eid].quantity*1;
  var fraction = i.energy_requirements[eid].suppliedby[sid].fraction*1;
  var type = i.energy_requirements[eid].suppliedby[sid].type;
  var efficiency = i.energy_requirements[eid].suppliedby[sid].efficiency*1;
  
  out += "<td><input type='text' style='width:50px' id='editfraction' eid='"+eid+"' sid='"+sid+"' value='"+fraction.toFixed(2)+"' /></td>";
  out += "<td>"+(fraction*quantity).toFixed(0)+" kWh/year</td>";
  out += "<td><input type='text' style='width:50px' id='editefficiency' eid='"+eid+"' sid='"+sid+"' value='"+(efficiency*100).toFixed(0)+"' />%</td>";
  
  out += "<td>"+((fraction*quantity)/efficiency).toFixed(0)+" kWh/year</td>";
  out += "<td><i class='icon-ok energysystemok' style='cursor:pointer' ></i></td>";
  
  $(".system[sid="+sid+"][eid="+eid+"]").html(out);
  $("#energysystemselect").val(type);
}

function heatingsystem_customview(i)
{
  var out = "";
  for (z in o.fueltotals)
  {
    out += "<tr><td>"+z+"</td><td>"+o.fueltotals[z].quantity.toFixed(0)+" kWh</td><td class='fuelcost' mode='view' z='"+z+"'>£"+o.fueltotals[z].fuelcost.toFixed(2)+"/kWh</td><td>£"+o.fueltotals[z].annualcost.toFixed(0)+"</td></tr>";
  }
  $("#fuels-table").html(out);
  
  var out = "";
  for (z in i.energy_requirements)
  {
    var demand_all = i.energy_requirements[z].quantity;
    out += "<tr style='background-color:#eee'><th>"+i.energy_requirements[z].name+"</th><th></th>";
    out += "<th>"+i.energy_requirements[z].quantity.toFixed(0)+" kWh/year</th><th></th><th></th><th><i z="+z+" class='delete-energy-requirements icon-remove' style='display:none' ></i></th></tr>";
    
    out += "<tr style='font-size:13px'><td>Supplied by:</td><td>Fraction</td><td>Demand</td><td>Efficiency</td><td>Fuel input</td>";
    out += "<td><i class='icon-plus add-system' eid="+z+" style='cursor:pointer' ></i></td></tr>";
    
    for (x in i.energy_requirements[z].suppliedby)
    {
      var system = i.energy_requirements[z].suppliedby[x];
      out += "<tr class='system' eid='"+z+"' sid='"+x+"' style='font-size:13px'>";
      out += "<td class='systemtype' eid='"+z+"' sid='"+x+"'>"+energysystems[system.type].name+"</td>";
      out += "<td class='systemfraction' eid='"+z+"' sid='"+x+"'>"+(system.fraction*1).toFixed(2)+"</td>";
      out += "<td>"+(system.fraction*demand_all).toFixed(0)+" kWh/year</td>";
      out += "<td class='systemefficiency' eid='"+z+"' sid='"+x+"'>"+(system.efficiency*100).toFixed(0)+"%</td>";
      out += "<td>"+((system.fraction*demand_all)/system.efficiency).toFixed(0)+" kWh/year</td>";
      out += "<td><i class='icon-trash delete-system' eid="+z+" sid="+x+" style='cursor:pointer' ></i></td>";
      out += "</tr>";
    }
  }
  $("#test").html(out);
}


