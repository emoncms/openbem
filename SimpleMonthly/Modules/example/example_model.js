var example_model =
{
  // Public variables
  
  // input (defaults)
  input: {
    a:1,
    b:2
  },
  
  set_inputdata: function(inputdata)
  {
    for (z in inputdata)
    { 
      this.input[z] = inputdata[z];
    }
  },
  
  calc: function()
  { 
    var c = this.input.a + this.input.b;
  
    var result = {};
    result.c = c;
    
    return result;
  }
  
};
