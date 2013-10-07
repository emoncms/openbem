function load_controller()
{

  $("#regions").click(function(){
  
    inputdata.region = $(this).val();
    model.set_inputdata(inputdata);
    result = model.calc();
    view();
    openbem.save(building,inputdata);
    
  });

  $("#air_change_rate").keyup(function()
  {
    inputdata.airchanges = $(this).val();
    model.set_inputdata(inputdata);
    result = model.calc();
    view();
    openbem.save(building,inputdata);
  });
  
  $("#volume").keyup(function()
  {
    inputdata.volume = $(this).val();
    model.set_inputdata(inputdata);
    result = model.calc();
    view();
    openbem.save(building,inputdata);
  });

  // Elements table events

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
    inputdata.elements.splice(id,1);
    
    model.set_inputdata(inputdata);
    result = model.calc();
    view();
    openbem.save(building,inputdata);
  });
  
  // Show edit button only on hover
  $("#annual").on("mouseenter",'tr', function() {
      $(this).find("td:last > span").show();
  });
    
  $("#annual").on("mouseleave",'tr', function() {
      $(this).find("td:last > span").hide();
  });
  
    // Edit: this will launch the edit/add dialog 
  $("#annual").on("click",".icon-pencil",function(){
    $(this).removeClass('icon-pencil');
    $(this).addClass('icon-ok');

    var iid = $(this).attr('iid');
    
    if (iid=='heating_system_efficiency') $("#heating_system_efficiency").html("<input type='text' value='"+inputdata.heating_system_efficiency+"' />");
    
    if (iid=='fuel_cost') $("#fuel_cost").html("<input type='text' value='"+inputdata.fuel_cost+"' />");
  });
  
  $("#annual").on("click",".icon-ok",function(){
    $(this).removeClass('icon-ok');
    $(this).addClass('icon-pencil');
    
    var iid = $(this).attr('iid');
    
    if (iid=='heating_system_efficiency')
    {
      inputdata.heating_system_efficiency = $("#heating_system_efficiency input").val();
      $("#heating_system_efficiency").html(inputdata.heating_system_efficiency);
    }
    
    if (iid=='fuel_cost')
    {
      inputdata.fuel_cost = $("#fuel_cost input").val();
      $("#fuel_cost").html(inputdata.fuel_cost);
    }
    
    model.set_inputdata(inputdata);
    result = model.calc();
    view();
    openbem.save(building,inputdata);
  });
  
  $("#monthly").on("keyup",'input', function() {
  
      var m = $(this).attr('month');
      var val = $(this).val();
      
      inputdata.mean_internal_temperature[m] = parseFloat(val);
      
      model.set_inputdata(inputdata);
      result = model.calc();
      view();
      openbem.save(building,inputdata);
  });

}
