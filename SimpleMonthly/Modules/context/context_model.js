var context_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.context!=undefined) {
      for (z in inputdata.context.input) this.input[z] = inputdata.context.input[z];
    }
  },
  
  input: {
    basement_area: 0, basement_height: 0,
    groundfloor_area: 0, groundfloor_height: 0,
    firstfloor_area: 35, firstfloor_height: 2,
    secondfloor_area: 0, secondfloor_height: 0,
    thirdfloor_area: 0, thirdfloor_height: 0,
    otherfloor1_area: 0, otherfloor1_height: 0,
    otherfloor2_area: 0, otherfloor2_height: 0,
    otherfloor3_area: 0, otherfloor3_height: 0,
    contextregion: 0,
    contextaltitude: 0,
    use_manual_occupancy: false,
    manual_occupancy: 0
  },
  
  calc: function ()
  { 
    var i = this.input;
  
    var basement_volume = i.basement_area * i.basement_height;
    var groundfloor_volume = i.groundfloor_area * i.groundfloor_height;
    var firstfloor_volume = i.firstfloor_area * i.firstfloor_height;
    var secondfloor_volume = i.secondfloor_area * i.secondfloor_height;
    var thirdfloor_volume = i.thirdfloor_area * i.thirdfloor_height;
    var otherfloor1_volume = i.otherfloor1_area * i.otherfloor1_height;
    var otherfloor2_volume = i.otherfloor2_area * i.otherfloor2_height;
    var otherfloor3_volume = i.otherfloor3_area * i.otherfloor3_height;
    
    var TFA = i.basement_area + i.groundfloor_area + i.firstfloor_area + i.secondfloor_area + i.thirdfloor_area + i.otherfloor1_area + i.otherfloor2_area + i.otherfloor3_area;
    
    var building_volume = basement_volume + groundfloor_volume + firstfloor_volume + secondfloor_volume + thirdfloor_volume + otherfloor1_volume + otherfloor2_volume + otherfloor3_volume;
    
    // Calculation of occupancy based on total floor area
    var occupancy = 0;
    
    if (TFA > 13.9) {
      occupancy = 1 + 1.76 * (1 - Math.exp(-0.000349 * Math.pow((TFA -13.9),2))) + 0.0013 * (TFA - 13.9);
    } else {
      occupancy = 1;
    }
    
    if (i.use_manual_occupancy) occupancy = i.manual_occupancy;
    
    return {
      basement_volume: basement_volume,
      groundfloor_volume: groundfloor_volume,
      firstfloor_volume: firstfloor_volume,
      secondfloor_volume: secondfloor_volume,
      thirdfloor_volume: thirdfloor_volume,
      otherfloor1_volume: otherfloor1_volume,
      otherfloor2_volume: otherfloor2_volume,
      otherfloor3_volume: otherfloor3_volume,
      TFA: TFA,
      building_volume: building_volume,
      occupancy: occupancy,
      altitude: i.contextaltitude,
      region: i.contextregion
    }
  }
  
}

function context_savetoinputdata(inputdata,o)
{
  inputdata.TFA = o.TFA;
  inputdata.volume = o.building_volume;
  inputdata.region = o.region;
  inputdata.occupancy = o.occupancy;
  inputdata.altitude = o.altitude;
}

function context_customview(i)
{
  if ($("#contextregion").html()=="")
  {
    var out = "";
    for (r in regions) out += "<option value="+r+">"+regions[r]+"</option>";
    $("#contextregion").html(out);
  }
  $("#contextregion").val(i.contextregion);
}
