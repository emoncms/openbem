
var openbem = {

  apikey: "",

  'getprojects':function()
  {
    var result = [];
    var apikeystr = ""; if (this.apikey!="") apikeystr = "?apikey="+this.apikey;
    
    $.ajax({ url: path+"openbem/getprojects.json"+apikeystr, dataType: 'json', async: false, success: function(data) {result = data;} });
    
    if (result=="") result = [];
    return result;
  },
  
  'getprojectdetails':function(project_id)
  {
    var result = {};
    var apikeystr = ""; if (this.apikey!="") apikeystr = "?apikey="+this.apikey;
    
    $.ajax({ url: path+"openbem/getprojectdetails.json", data: "project_id="+project_id, dataType: 'json', async: false, success: function(data) {result = data;} });
    
    if (result=="") result = {};
    return result;
  },
  
  'addproject':function(name,description)
  {
    var result = 0;
    $.ajax({ type: 'GET', url: path+"openbem/addproject.json", data: "name="+name+"&description="+description, async: false, success: function(data){result=data;} });
    return result;
  },
  
  'deleteproject':function(projectid)
  {
    var result = 0;
    $.ajax({ type: 'GET', url: path+"openbem/deleteproject.json", data: "projectid="+projectid, async: false, success: function(data){result=data;} });
    return result;
  },
  
  
  'get_scenarios':function(project_id)
  {
    var result = [];
    var apikeystr = ""; if (this.apikey!="") apikeystr = "?apikey="+this.apikey;
    
    $.ajax({ url: path+"openbem/getscenarios.json"+apikeystr, data: "project_id="+project_id, dataType: 'json', async: false, success: function(data) {result = data;} });
    
    if (result=="") result = [];
    return result;
  },
  
  'add_scenario':function(project_id,meta)
  {
    var result = 0;
    $.ajax({ type: 'GET', url: path+"openbem/addscenario.json", data: "project_id="+project_id+"&meta="+JSON.stringify(meta), async: false, success: function(data){result=data;} });
    return result;
  },
  
  'clone_scenario':function(project_id,scenario_id)
  {
    var result = 0;
    $.ajax({ type: 'GET', url: path+"openbem/clonescenario.json", data: "project_id="+project_id+"&scenario_id="+scenario_id, async: false, success: function(data){result=data;} });
    return result;
  },
  
  
  'delete_scenario':function(project_id,scenario_id)
  {
    var result = 0;
    $.ajax({ type: 'GET', url: path+"openbem/deletescenario.json", data: "project_id="+project_id+"&scenario_id="+scenario_id, async: false, success: function(data){result=data;} });
    return result;
  },
  
  'get_scenario':function(scenario_id)
  {
    var result = {};
    var apikeystr = ""; if (this.apikey!="") apikeystr = "?apikey="+this.apikey;
    
    $.ajax({ url: path+"openbem/getscenario.json"+apikeystr, data: "scenario_id="+scenario_id, dataType: 'json', async: false, success: function(data) {result = data;} });

    return result;
  },  
  
  'save_scenario':function(scenario_id,data)
  {
    var result = {};
    $.ajax({ type: 'POST', url: path+"openbem/savescenario.json", data: "scenario_id="+scenario_id+"&data="+JSON.stringify(data), async: true, success: function(data){} });
    return result;
  },
  
  
  

  'get':function(building)
  {
    var result = {};
    $.ajax({ url: path+"openbem/getmonthly.json", dataType: 'json', data: "building="+building, async: false, success: function(data) {result = data;} });
    return result;
  },

  'save':function(building,data)
  {
    var result = {};
    $.ajax({ type: 'POST', url: path+"openbem/savemonthly.json", data: "building="+building+"&data="+JSON.stringify(data), async: true, success: function(data){} });
    return result;
  },
  
  'list':function()
  {
    var result = {};
    var apikeystr = ""; //if (feed.apikey!="") apikeystr = "?apikey="+feed.apikey;
    
    $.ajax({ url: path+"openbem/getlist.json"+apikeystr, dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  }
}
