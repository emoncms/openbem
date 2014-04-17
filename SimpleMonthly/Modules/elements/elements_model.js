var elements_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.elements!=undefined) {
      for (z in inputdata.elements.input) this.input[z] = inputdata.elements.input[z];
    }
    
    // Input dependencies
    this.input.TFA = inputdata.TFA;
  },
  
  input: {

    region: 0,
    TFA:0,

    list: 
    [
/*      {
          "name": "Main Floor",
          "lib": "floor0007",
          "area": 21,
          "uvalue": 0.5,
          "kvalue": 110
      },
      {
          "name": "Front wall",
          "lib": "wall0010",
          "area": 11.399999999999999,
          "uvalue": 1.1,
          "kvalue": 350
      },
      {
          "name": "Back wall",
          "lib": "wall0010",
          "area": 11.399999999999999,
          "uvalue": 1.1,
          "kvalue": 350
      },
      {
          "name": "Left wall",
          "lib": "wall0010",
          "area": 9.19,
          "uvalue": 1.1,
          "kvalue": 350
      },
      {
          "name": "Right wall",
          "lib": "wall0004",
          "area": 9.19,
          "uvalue": 0.45,
          "kvalue": 10
      },
      {
          "name": "Roof",
          "lib": "roof0002",
          "area": 27.240000000000002,
          "uvalue": 0.25,
          "kvalue": 9
      },
      {
          "name": "Front window",
          "lib": "window0121",
          "area": 0.783,
          "orientation": 3,
          "overshading": 3,
          "uvalue": 4.8,
          "g":0.85,
          "gL":0.9,
          "ff":0.7
      },
      {
          "name": "Roof window Front",
          "lib": "window0001",
          "area": 1.0290000000000001,
          "orientation": 3,
          "overshading": 3,
          "uvalue": 3.1,
          "g":0.76,
          "gL":0.8,
          "ff":0.7
      },
      {
          "name": "Roof window Back",
          "lib": "window0001",
          "area": 1.0290000000000001,
          "orientation": 3,
          "overshading": 2,
          "uvalue": 3.1,
          "g":0.76,
          "gL":0.8,
          "ff":0.7
      } */
    ]
  },
  
  calc: function ()
  { 
    var i = this.input;

    var total_fabric_heat_loss_WK = 0;
    var total_thermal_capacity = 0;
    var windows = [];

    for (z in i.list)
    {
      var id = i.list[z].lib;
     
      total_fabric_heat_loss_WK += i.list[z].uvalue * i.list[z].area;
      if (i.list[z].kvalue!=undefined) total_thermal_capacity += i.list[z].kvalue * i.list[z].area;
      
      // Create specific windows array to pass to solar gains calculation
      if (element_library[id].type=='Window') {
      
        windows.push({
          orientation: i.list[z].orientation, 
          area: i.list[z].area, 
          overshading: i.list[z].overshading, 
          g: i.list[z].g, 
          ff: i.list[z].ff,
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

    var monthly_solargains = calc_solar_gains_from_windows(windows,i.region);
    
    var monthly_fabric_heat_loss = [];
    for (var m=0; m<12; m++)
    {
      monthly_fabric_heat_loss[m] = total_fabric_heat_loss_WK;
    }
    
    var TMP = total_thermal_capacity / i.TFA;
    
    return {
      total_fabric_heat_loss_WK: total_fabric_heat_loss_WK,
      total_thermal_capacity: total_thermal_capacity,
      TMP: TMP,
      monthly_fabric_heat_loss: monthly_fabric_heat_loss,
      monthly_solargains: monthly_solargains,
      GL: GL
    }
  }
  
}

function elements_savetoinputdata(inputdata,o)
{
  inputdata.losses['fabric'] = o.monthly_fabric_heat_loss;
  inputdata.gains['solargains'] = o.monthly_solargains;
}

function elements_customview(i)
{
  //console.log(i.list);
  var element_table_mode = 'uvalue';
  
  var out = "";
  for (z in i.list)
  {
    var id = i.list[z].lib;
    out += "<tr>";
    
    // Only draws type first time it gets that element type in the list
    // makes for nicer looking layout, relies upon ordered elements list
    if (z>0) {
      var lasttype = element_library[i.list[z-1].lib].type;
      if (element_library[id].type!=lasttype) {
        out += "<td><b>"+element_library[id].type+"</b></td>";
      } else {
        out += "<td></td>";
      }
    } else {
      out += "<td><b>"+element_library[id].type+"</b></td>";
    }
    
    out += "<td><b>"+i.list[z].name+"</b><br><i>";
    for (x in element_library[id])
    {
      if (x=='description') out += element_library[id][x]+", ";
      if (x=='uvalue') out += "U-value: "+i.list[z][x]+", ";
      if (x=='kvalue') out += "k-value: "+i.list[z][x];
      if (x=='g') out += "g: "+i.list[z][x]+", ";
      if (x=='gL') out += "gL: "+i.list[z][x]+", ";
      if (x=='ff') out += "Frame factor: "+i.list[z][x];
    }
    out +="</i></td>";
    
    out += "<td>"+i.list[z].area.toFixed(1)+"m<sup>2</sup></td>";
    
    if (element_table_mode == 'uvalue')
    {
      out += "<td>"+i.list[z].uvalue+"</td>";
      out += "<td>"+(i.list[z].uvalue*i.list[z].area).toFixed(1)+" W/K</td>";
    } else {
      if (element_library[id].kvalue) {
        out += "<td>"+i.list[z].kvalue+"</td>";
        out += "<td>"+(i.list[z].kvalue*i.list[z].area).toFixed(0)+" kJ/K</td>";
      } else {
        out += "<td>n/a</td><td>n/a</td>";
      }
    }
    // Edit and delete icon's, span hide's/show's the icons when the mouse hovers.
    out += "<td><span style='display:none'>";
    out += "<i class='icon-pencil' style='margin-right: 10px; cursor:pointer' eid="+z+" ></i>";
    out += "<i class='icon-trash' eid="+z+" style='cursor:pointer' ></i>";
    out += "</span></td>";
    
    out += "</tr>";
  }
  
  if (i.list.length==0) {
    out = "<tr class='alert'><td></td><td style='padding-top:50px; padding-bottom:50px'><b>Click on Add element (top-left) to add floor, walls, roof and window elements</b></td><td></td><td></td><td></td><td></td></tr>";
  }
  
  $("#elements").html(out);
}

function elements_customcontroller(module)
{
  // Show edit button only on hover
  $("#elements").on("mouseenter",'tr', function() {
      $(this).find("td:last > span").show();
  });
    
  $("#elements").on("mouseleave",'tr', function() {
      $(this).find("td:last > span").hide();
  });
  
  // Delete's the element
  $("#elements").on("click",".icon-trash",function(){
    var id = $(this).attr('eid');
    i.list.splice(id,1);
    
    openbem_update(module);
  });
  
  // Action controllers for element modal
  
  // Edit: this will launch the edit/add dialog 
  $("#elements").on("click",".icon-pencil",function(){
    var id = $(this).attr('eid');

    // Set the element type
    var element = i.list[id].lib;
    var type = element_library[element].type;
    $("#element-type").val(type);
    
    if (type=='Window') {
      $("#window_options").show();
      $("#window_orientation").val(i.list[id].orientation);
      $("#window_overshading").val(i.list[id].overshading);
    } else {
      $("#window_options").hide();
    }
    
    // Populate and set the element
    var out = "";
    for (z in element_library)
    {
      if (element_library[z].type==type) {
      
        if (element == z) { 
          out += "<option value='"+z+"' selected>"+element_library[z].description+"</option>";
        } else {
          out += "<option value='"+z+"'>"+element_library[z].description+"</option>";
        }
      }
    }
    $("#element-selector").html(out);
   
    // Set name and area
    $("#element-title").val(i.list[id].name);
    $("#element-area").val(i.list[id].area);
    $("#element-uvalue").val(i.list[id].uvalue);
    $("#element-kvalue").val(i.list[id].kvalue); 
    
    if (i.list[id].g!=undefined) $("#window-g").val(i.list[id].g);
    if (i.list[id].gL!=undefined) $("#window-gL").val(i.list[id].gL); 
    if (i.list[id].ff!=undefined) $("#window-ff").val(i.list[id].ff);
    
    $("#myModal").attr('eid',id);
    
    $("#myModalLabel").html("Edit building element");
    $("#element-add").hide();
    $("#element-edit").show();
    $("#myModal").modal('show');
  });

  $("#element-type").click(function()
  {
    var type = $(this).val();

    if (type=='Window') {
      $("#window_options").show();
    } else {
      $("#window_options").hide();
    }

    var out = "";
    for (z in element_library)
    {
      if (element_library[z].type==type) out += "<option value='"+z+"'>"+element_library[z].description+"</option>";
    }
    $("#element-selector").html(out);
  });
  
  $("#element-selector").click(function()
  {
    var element_id = $(this).val();
    $("#element-uvalue").val(element_library[element_id].uvalue);
    $("#element-kvalue").val(element_library[element_id].kvalue); 
    
    if (element_library[element_id].type=='Window')
    {
      $("#window-g").val(element_library[element_id].g);
      $("#window-gL").val(element_library[element_id].gL); 
      $("#window-ff").val(element_library[element_id].ff);
    }
  });

  $("#element-add").click(function()
  {
    var element_id = $("#element-selector").val();
    var name = $("#element-title").val();
    var area = parseFloat($("#element-area").val()*1);
    
    var type = element_library[element_id].type;

    var uvalue = parseFloat($("#element-uvalue").val()*1);
    var kvalue = parseFloat($("#element-kvalue").val()*1);

    if (uvalue!=element_library[element_id].uvalue || kvalue!=element_library[element_id].kvalue) {
      if (type=='Roof') element_id = 'roof0000'; 
      if (type=='Wall') element_id = 'wall0000'; 
      if (type=='Floor') element_id = 'floor0000'; 
    }
    
    if (type=='Window') {
      var g = parseFloat($("#window-g").val()*1);
      var gL = parseFloat($("#window-gL").val()*1);
      var ff = parseFloat($("#window-ff").val()*1);
      
      if (g!=element_library[element_id].g || gL!=element_library[element_id].gL || ff!=element_library[element_id].ff) element_id = 'window0000';
    }
    
    if (!element_id)
    { 
      alert("Please select an element using the drop down element selector");
    } 
    else if (!name) 
    {
      alert("Please enter a name for the element such as 'South wall'");
    } 
    else if (area<=0 && area!=NaN)
    {
      alert("Please give an area greater than 0");
    } 
    else
    {
    
      if (type=='Window') {
        var orient = parseInt($("#window_orientation").val());
        var shade = parseInt($("#window_overshading").val());
        i.list.push({name: name, lib: element_id, area: area, orientation: orient,overshading: shade, uvalue: uvalue, g:g, gL:gL, ff:ff});
      } else {
        i.list.push({
          name: name, 
          lib: element_id, 
          area: area,
          
          uvalue: uvalue,
          kvalue: kvalue
        
        });
      }
    
      // save and update
      openbem_update(module);

      $("#myModal").modal('hide');
    }
  });
  
  $("#element-edit").click(function()
  {
    var id = $("#myModal").attr('eid');
    var element_id = $("#element-selector").val();
    var name = $("#element-title").val();
    console.log(name);
    var area = parseFloat($("#element-area").val()*1);
    var type = element_library[element_id].type;
    
    var uvalue = parseFloat($("#element-uvalue").val()*1);
    var kvalue = parseFloat($("#element-kvalue").val()*1);
    
    if (uvalue!=element_library[element_id].uvalue || kvalue!=element_library[element_id].kvalue) {
      if (type=='Roof') element_id = 'roof0000'; 
      if (type=='Wall') element_id = 'wall0000'; 
      if (type=='Floor') element_id = 'floor0000';
    }
    
    if (type=='Window') {
      var g = parseFloat($("#window-g").val()*1);
      var gL = parseFloat($("#window-gL").val()*1);
      var ff = parseFloat($("#window-ff").val()*1);
      
      if (g!=element_library[element_id].g || gL!=element_library[element_id].gL || ff!=element_library[element_id].ff) element_id = 'window0000';
    }
    
    if (!element_id)
    { 
      alert("Please select an element using the drop down element selector");
    } 
    else if (!name) 
    {
      alert("Please enter a name for the element such as 'South wall'");
    } 
    else if (area<=0 && area!=NaN)
    {
      alert("Please give an area greater than 0");
    } 
    else
    {

      if (type=='Window') {
        var orient = parseInt($("#window_orientation").val());
        var shade = parseInt($("#window_overshading").val());
        i.list[id] = {name: name, lib: element_id, area: area, orientation: orient,overshading: shade, uvalue: uvalue, g:g, gL:gL, ff:ff};
      
      } else {
        i.list[id] = {name: name, lib: element_id, area: area,
          uvalue: uvalue,
          kvalue: kvalue
        };
      }
      
      // save
      openbem_update(module);
      
      $("#element-add").show();
      $("#element-edit").hide();    
      $("#myModalLabel").html("Add building element");
      $("#myModal").modal('hide');
    }
  });
}

