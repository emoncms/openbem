
var openbem = {

  'get':function(building)
  {
    var result = {};
    $.ajax({ url: path+"openbem/getmonthly.json", dataType: 'json', data: "building="+building, async: false, success: function(data) {result = data;} });
    return result;
  },

  'save':function(building,data)
  {
    var result = {};
    $.ajax({ url: path+"openbem/savemonthly.json", data: "building="+building+"&data="+JSON.stringify(data), async: true, success: function(data){} });
    return result;
  },
}
