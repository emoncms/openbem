
var openbem = {

  'getdynamic':function(building)
  {
    var result = {};
    $.ajax({ url: path+"openbem/getdynamic.json", dataType: 'json', data: "building="+building, async: false, success: function(data) {result = data;} });
    return result;
  },

  'savedynamic':function(building,data)
  {
    var result = {};
    $.ajax({ url: path+"openbem/savedynamic.json", data: "building="+building+"&data="+JSON.stringify(data), async: true, success: function(data){} });
    return result;
  },
}
