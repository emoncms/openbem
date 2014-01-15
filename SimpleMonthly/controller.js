function openbem_controller(module)
{ 
  $("input[type=text]").change(function(){
    var id = $(this).attr('id');
    if (id!=undefined) {
      if (i[id]!=undefined) {
        i[id] = parseFloat($(this).val());
        openbem_update(module);
      }
    }
  });
  
  $("input[type=checkbox]").change(function(){
    var id = $(this).attr('id');
    if (i[id]!=undefined) {
      if ($(this)[0].checked) i[id] = true; else i[id] = false;
      openbem_update(module);
    }
  });
  
  $("select").change(function(){
    var id = $(this).attr('id');
    if (i[id]!=undefined) {
      i[id] = $(this).val();
      openbem_update(module);
    }
  });
}

function openbem_update_view(i,o)
{
  for (key in i) {
    var element = $("#"+key);
    if (element.length>0) {
      if (element.is("input[type=text]")) element.val(i[key]);
      if (element.is("input[type=checkbox]")) element.attr('checked',i[key]);
      if (element.is("select")) element.val(i[key]);
      if (element.is("span")) element.html(i[key]);
      if (element.is("div")) element.html(i[key]);
      if (element.is("tbody")) monthlyrow(element,i[key]);
    }
  }
  
  for (key in o) {
    var element = $("#"+key);
    var dp = element.attr('dp');
    
    if (element.length>0) {
      if (element.is("input[type=text]")) element.val(o[key].toFixed(dp));
      if (element.is("span")) element.html(o[key].toFixed(dp));
      if (element.is("div")) element.html(o[key].toFixed(dp));
      if (element.is("tbody")) monthlyrow(element,o[key]);
    }
  }
}

function monthlyrow(element,data)
{  
  var title = element.attr('title');
  var dp = element.attr('dp');
  var units = element.attr('units');

  if (title==undefined) title = "";
  if (dp==undefined) dp = 0;
  if (units==undefined) units = "";
  
  var monthly_html = "<td>"+title+"</td>";
  for (var m=0; m<12; m++) {
    monthly_html += "<td>"+(data[m]).toFixed(dp)+units+"</td>";
  }
  
  element.html("<tr>"+monthly_html+"</tr>");
}
