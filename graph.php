<?php
  global $path;
?>

<!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.selection.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>


<br>

<ul class="nav nav-pills">
  <li>
    <a href="view">OpenBEM</a>
  </li>
  <li class="active">
    <a href="#">Simulation</a>
  </li>
</ul>

<h3>Simulation</h3>
<p>The black line is simulated internal temperature based on properties from OpenBEM page.</p>
<p>The gray line is internal temperature prediction 4h +</p>

<div id="graph_bound" style="width:100%; height:400px; position:relative; ">
  <div id="graph"></div>
</div>

<h3>Total W/K heat loss: <span id="total_wk"> </span> W/K</h3>
<h3>Total thermal capacity: <span id="total_thermal_capacity"></span> J/K</h3>

<h3><span id="error"></span></h3>

<table class="table">
<tr><th>Segment</th><th>W/K</th><th>Thermal capacity</th></tr>
<tbody id="segment_config"></tbody>
</table>
<p><i>Segment 0 connects to external temperature, Segment <span class="numofsegments"></span> to heat input</i></p>

<button id="simulate" class="btn">Simulate</button>

<script >

  var path = "<?php echo $path; ?>";
  var apikey = "";
  
  var timeWindow = (3600000*24*4);	// Initial time window
  var start = +new Date - timeWindow;	// Get start time
  var end = +new Date +1000*1000;				        // Get end time
  
  var $graph_bound = $('#graph_bound');
  var $graph = $('#graph').width($graph_bound.width()).height($('#graph_bound').height());

  var lab_back_left = feed.get_data(16006,start,end,0);
  var lab_front_left = feed.get_data(16008,start,end,0);
  var lab_front_right = feed.get_data(16010,start,end,0);
  var lab_back_right = feed.get_data(16004,start,end,0);
  
  var outside_data = feed.get_data(14972,start,end,0);
  var power_data = feed.get_data(16012,start,end,0);
  var solar_data = feed.get_data(364,start,end,0);
  
  var simlast = 0;
  var lasterror = 1000000;
  var error = 0;

  var su = 150, sk = 2000000;
  var segment = [
    {u:130,k:11000000},
    {u:340,k:2500000},
    {u:712,k:600000}
  ];
  
  var segment_config_html = "";
  
  for (i in segment) 
  {
    segment_config_html += "<tr><td>"+i+"</td>";
    segment_config_html += "<td><input id='u"+i+"' type='text' value='"+segment[i].u+"'/ ></td>";
    segment_config_html += "<td><input id='k"+i+"' type='text' value='"+segment[i].k+"'/ ></td></tr>";
  }

  $(".numofsegments").html(segment.length-1);
  $("#segment_config").html(segment_config_html);
  
  simulate();  
  
  function simulate()
  {  
  // INITIAL CONDITIONS  
  var sum_u = 0;
  var sum_k = 0;
  
  var d = (lab_back_left[0][1] - outside_data[0][1]) / (segment.length+1);
  for (i in segment) 
  {
    segment[i].T = outside_data[0][1]+1 + i*0.8;
    segment[i].E = segment[i].T * segment[i].k;
    segment[i].H = 0;
    sum_u += 1 / segment[i].u;
    sum_k += 1*segment[i].k
  }
  
  var total_wk = 1 / sum_u;
  var total_thermal_capacity = sum_k;
  
  $("#total_wk").html(total_wk.toFixed(0));
  $("#total_thermal_capacity").html(total_thermal_capacity);
  
  var sim = [];
  
  var otsum = 0;
  var psum = 0;
  
  for (var z=1; z<outside_data.length; z++)
  {
    var lasttime = outside_data[z-1][0];
    var time = outside_data[z][0];
    var outside = outside_data[z][1];
    otsum += outside;
    
    var step = (time - lasttime) / 1000.0;

    //---------------------------------------------
    // Binary search power closest power datapoints
    
    var spos = 0;
    var epos = power_data.length-1;
    var mid = 0;
    
    for (var n=0; n<20; n++)
    {
      mid = spos + Math.round((epos - spos ) / 2);
      if (power_data[mid][0] > time) epos = mid; else spos = mid;
    }
    
    heatinput = power_data[mid][1];
    
    
    var spos = 0;
    var epos = solar_data.length-1;
    var mid = 0;
    
    for (var n=0; n<20; n++)
    {
      mid = spos + Math.round((epos - spos ) / 2);
      if (solar_data[mid][0] > time+3600*1000*1) epos = mid; else spos = mid;
    }
    
    heatinput += solar_data[mid][1]*0.6;
    
    psum += heatinput;
    // --------------------------------------------
    
    //---------------------------------------------
    // Binary search power closest power datapoints
    
    var spos = 0;
    var epos = lab_back_left.length-1;
    var mid = 0;
    
    for (var n=0; n<20; n++)
    {
      mid = spos + Math.round((epos - spos ) / 2);
      if (lab_back_left[mid][0] > time) epos = mid; else spos = mid;
    }
    
    var ref = lab_back_left[mid][1];
    // --------------------------------------------
    
    var len = segment.length-1;

    
    // To provide the option of simulating with one or two segments we have different cases
    
    // if there is only one segment then its gaining heat from the heatsource directly and loosing heat to the outside
    if (segment.length==1)
    {
      segment[0].H = heatinput - ((segment[0].T - outside) * segment[0].u);
    } 
   
    if (segment.length>1)
    {
      segment[len].H = heatinput - ((segment[len].T - segment[len-1].T) * segment[len].u);
      segment[0].H = ((segment[1].T - segment[0].T) * segment[1].u) - ((segment[0].T - outside) * segment[0].u);
    }
     
    if (segment.length>2)
    {
      for (var i=1; i<len; i++)
      {
        segment[i].H = ((segment[i+1].T - segment[i].T) * segment[i+1].u) - ((segment[i].T - segment[i-1].T) * segment[i].u);
      }
    }
    
    for (i in segment) 
    {
      segment[i].E += segment[i].H * step;
      segment[i].T = segment[i].E / segment[i].k;
    }
    
    sim.push([time,segment[segment.length-1].T]);
    
    error += Math.abs(segment[segment.length-1].T - ref); //* (segment[segment.length-1].T - ref))
  }
  
  simlast = segment[segment.length-1].T;
  
  var avexternal = otsum/outside_data.length-1;
  var avpower = psum/outside_data.length-1;
  
  
  // PREDICTION !!
  
  var prediction = [];
  var lpv = power_data[power_data.length-1][1];
  var time = outside_data[outside_data.length-1][0];
  
  step =30;
    
  for (var n=0; n<480; n++)
  {
    time += (step*1000);

    var len = segment.length-1;
    
    // To provide the option of simulating with one or two segments we have different cases
    
    // if there is only one segment then its gaining heat from the heatsource directly and loosing heat to the outside
    if (segment.length==1)
    {
      segment[0].H = lpv - ((segment[0].T - outside) * segment[0].u);
    } 
    
    if (segment.length>1)
    {
      segment[len].H = lpv - ((segment[len].T - segment[len-1].T) * segment[len].u);
      segment[0].H = ((segment[1].T - segment[0].T) * segment[1].u) - ((segment[0].T - outside) * segment[0].u);
    }
    
    if (segment.length>2)
    {
      for (var i=1; i<len; i++)
      {
        segment[i].H = ((segment[i+1].T - segment[i].T) * segment[i+1].u) - ((segment[i].T - segment[i-1].T) * segment[i].u);
      }
    }
    
    for (i in segment) 
    {
      segment[i].E += segment[i].H * step;
      segment[i].T = segment[i].E / segment[i].k;
    }
    
    prediction.push([time,segment[segment.length-1].T]);
  }
  end = time;
  
  
  var linewidth = 1;
  
  var plot = $.plot($graph, 
    [
      {data: outside_data, lines: { show: true, fill: false }, color: "rgba(0,0,255,0.8)"},
      {data: power_data, yaxis: 2, lines: { show: true, fill: true, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,150,0,0.2)"},
      {data: solar_data, yaxis: 2, lines: { show: true, fill: false, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,255,0,0.2)"},
        
      {data: lab_back_left, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      {data: lab_back_right, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      {data: lab_front_left, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      {data: lab_front_right, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      
      {data: sim, lines: { show: true, fill: false, lineWidth: 3}, color: "rgba(0,0,0,1)"},
      {data: prediction, lines: { show: true, fill: false, lineWidth: 3}, color: "rgba(0,0,0,0.5)"}
       
    ], {
  grid: { show: true, hoverable: true, clickable: true },
  xaxis: { mode: "time", localTimezone: true, min: start, max: end },
  selection: { mode: "xy" }
  });
  
  $("#error").html("Model is within an average of: "+(error/ outside_data.length).toFixed(3)+"C of measured temperature");
  }
  
  $("#simulate").click(function(){

    for (i in segment) 
    {
      segment[i].u = $("#u"+i).val();
      segment[i].k = $("#k"+i).val();
    }
    error = 0;
    simulate();
  });


//  var updater = setInterval(update, 500);
 
  function update()
  {
    var tmp = lab_front_left[lab_front_left.length-1][1];
    //segment[0].u += (simlast - tmp)*Math.abs(simlast - tmp);
    //segment[1].u += (simlast - tmp)*Math.abs(simlast - tmp);
    
    if (lasterror-error>0)  segment[0].k += error*10; else segment[0].k -= error*10;
    //if (lasterror-error>0)  segment[1].k += error*10; else segment[1].k -= error*10;
    console.log(error);
    
    lasterror = error;
    error = 0;
    simulate();
  }

  
  
  
</script>
