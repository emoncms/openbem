function solarhotwater_controller()
{ 
  $("input[type=text]").change(function(){
    var id = $(this).attr('id');
    if (id!=undefined) {
      i[id] = parseFloat($(this).val());
      o = solarhotwater_model.calc();
      update_view(i,o); save(i);
    }
  });
  
  $("input[type=checkbox]").change(function(){
    var id = $(this).attr('id');
    if ($(this)[0].checked) i[id] = true; else i[id] = false;
    o = solarhotwater_model.calc();
    update_view(i,o); save(i);
  });
  
  $("select").change(function(){
    var id = $(this).attr('id');
    i[id] = $(this).val();
    o = solarhotwater_model.calc();
    update_view(i,o); save(i);
  });
}

function update_view(i,o)
{
  for (key in i) {
    var element = $("#"+key);
    if (element.length>0) {
      if (element.is("input[type=text]")) element.val(i[key]);
      if (element.is("select")) element.val(i[key]);
      if (element.is("span")) element.html(i[key]);
      if (element.is("div")) element.html(i[key]);
    }
  }
  
  for (key in o) {
    var element = $("#"+key);
    if (element.length>0) {
      if (element.is("input[type=text]")) element.val(o[key]);
      if (element.is("span")) element.html(o[key]);
      if (element.is("div")) element.html(o[key]);
      if (element.is("tbody")) element.html(monthlyrow("",o[key],0));
    }
  }
}

function monthlyrow(title,monthly,dp)
{
  var monthly_html = "<td>"+title+"</td>";
  for (var m=0; m<12; m++) {
    monthly_html += "<td>"+(monthly[m]).toFixed(dp)+"</td>";
  }
  return "<tr>"+monthly_html+"</tr>";
}
