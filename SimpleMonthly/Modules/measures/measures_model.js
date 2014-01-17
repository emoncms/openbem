var measures_model = {

  set_from_inputdata: function (inputdata)
  {
    // Copy full saved input object
    if (inputdata.measures!=undefined) {
      for (z in inputdata.measures.input) this.input[z] = inputdata.measures.input[z];
    }
    
    if (inputdata.elements!=undefined) {
      this.input.elementlist = inputdata.elements.input.list;
    }
    
    
    
  },
  
  input: {
  
    elementlist: {}
  },
  
  calc: function ()
  { 
    console.log(inputdata);
    var i = this.input;
    
    var measures = [];
    
    for (z in i.elementlist)
    {
      var lib = i.elementlist[z].lib;
      var type = element_library[lib].type;
    
      if (type=='Window' && i.elementlist[z].uvalue>1.3) {
     
        measures.push({
        
        current: "<b>"+i.elementlist[z].name+"</b><br><i>u-value: "+i.elementlist[z].uvalue+"</i>",
        after: "<b>PVC or Wood frame, triple-glazed<br>argon filled (low-E, εn = 0.05, soft coat)<br>16 or more mm gap</b><br><i>u-value: 1.3</i>",
        cost: "£500"
        
        
        });
      }
    
    }
  
    return {measures:measures}
  }
  
}

function measures_savetoinputdata(inputdata,o)
{

}

function measures_customview(i)
{
  var out = "";
  for (z in o.measures)
  {
    out += "<tr><td>"+o.measures[z].current+"</td><td>"+o.measures[z].after+"</td><td>"+o.measures[z].cost+"</td><td><input type='checkbox' checked /></tr>";
  }
  $("#measures_list").html(out);
  

}
