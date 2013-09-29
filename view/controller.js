function load_controller()
{

  $("#air_change_rate").keyup(function()
  {
    model.input.airchanges = $(this).val();
    result = model.calc();
    view();
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
    elements.splice(id,1);
    
    model.input.elements = elements;
    result = model.calc();
    view();
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
    
    if (iid=='heating_system_efficiency') $("#heating_system_efficiency").html("<input type='text' value='"+heating_system_efficiency+"' />");
    
    if (iid=='fuel_cost') $("#fuel_cost").html("<input type='text' value='"+fuel_cost+"' />");
  });
  
  $("#annual").on("click",".icon-ok",function(){
    $(this).removeClass('icon-ok');
    $(this).addClass('icon-pencil');
    
    var iid = $(this).attr('iid');
    
    if (iid=='heating_system_efficiency')
    {
      heating_system_efficiency = $("#heating_system_efficiency input").val();
      $("#heating_system_efficiency").html(heating_system_efficiency);
      model.input.heating_system_efficiency = heating_system_efficiency;
    }
    
    if (iid=='fuel_cost')
    {
      fuel_cost = $("#fuel_cost input").val();
      $("#fuel_cost").html(fuel_cost);
      model.input.fuel_cost = fuel_cost;
    }
    
    result = model.calc();
    view();

  });
  
  $("#monthly").on("keyup",'input', function() {
  
      var m = $(this).attr('month');
      var val = $(this).val();
      
      model.input.mean_internal_temperature[m] = parseFloat(val);
      
      result = model.calc();
      view();
  });

}
