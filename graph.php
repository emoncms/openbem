<?php
  global $path;
?>

<!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.selection.min.js"></script>

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

<table class="table">
<tr><th>Segment</th><th>W/K</th><th>Thermal capacity</th></tr>
<tbody id="segment_config"></tbody>
</table>
<p><i>Segment 0 connects to external temperature, Segment <span class="numofsegments"></span> to heat input</i></p>

<button id="simulate" class="btn">Simulate</button>

<script >

  var path = "<?php echo $path; ?>";
  var apikey = "";
  
  var timeWindow = (3600000*28*1);	// Initial time window
  var start = +new Date - timeWindow;	// Get start time
  var end = +new Date + 1000*1000;				        // Get end time
  
  var $graph_bound = $('#graph_bound');
  var $graph = $('#graph').width($graph_bound.width()).height($('#graph_bound').height());

  var lab_back_left = feed.get_data(16006,start,end,0);
  var lab_front_left = feed.get_data(16008,start,end,0);
  var lab_front_right = feed.get_data(16010,start,end,0);
  var lab_back_right = feed.get_data(16004,start,end,0);
  
  var outside_data = feed.get_data(14972,start,end,0);
  var power_data = feed.get_data(16012,start,end,0);

  var segment = [
    {u:100,k:4000000},
    {u:500,k:2000000},
    {u:800,k:1000000}
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
  for (i in segment) 
  {
    segment[i].T = lab_back_left[0][1];
    segment[i].E = segment[i].T * segment[i].k;
    segment[i].H = 0;
    sum_u += 1 / segment[i].u;
    sum_k += 1*segment[i].k;
  }
  
  var total_wk = 1 / sum_u;
  var total_thermal_capacity = sum_k;
  
  $("#total_wk").html(total_wk.toFixed(0));
  $("#total_thermal_capacity").html(total_thermal_capacity);
  
  var sim = [];
  
  for (var z=1; z<outside_data.length; z++)
  {
    var lasttime = outside_data[z-1][0];
    var time = outside_data[z][0];
    var outside = outside_data[z][1];
    
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
    
    // --------------------------------------------
    
    segment[2].H = heatinput - ((segment[2].T - segment[1].T) * segment[2].u);
    segment[1].H = ((segment[2].T - segment[1].T) * segment[2].u) - ((segment[1].T - segment[0].T) * segment[1].u);
    segment[0].H = ((segment[1].T - segment[0].T) * segment[1].u) - ((segment[0].T - outside) * segment[0].u);
    
    segment[2].E += segment[2].H * step;
    segment[1].E += segment[1].H * step;
    segment[0].E += segment[0].H * step;
    
    segment[2].T = segment[2].E / segment[2].k;
    segment[1].T = segment[1].E / segment[1].k;
    segment[0].T = segment[0].E / segment[0].k;

    sim.push([time,segment[2].T]);
  }
  
  // PREDICTION !!
  var prediction = [];
  var lpv = power_data[power_data.length-1][1];
  var time = outside_data[outside_data.length-1][0];
  
  step =60*5;
    
  for (var n=0; n<480; n++)
  {
    time += (step*1000);

    segment[2].H = lpv - ((segment[2].T - segment[1].T) * segment[2].u);
    segment[1].H = ((segment[2].T - segment[1].T) * segment[2].u) - ((segment[1].T - segment[0].T) * segment[1].u);
    segment[0].H = ((segment[1].T - segment[0].T) * segment[1].u) - ((segment[0].T - outside) * segment[0].u);
    
    segment[2].E += segment[2].H * step;
    segment[1].E += segment[1].H * step;
    segment[0].E += segment[0].H * step;
    
    segment[2].T = segment[2].E / segment[2].k;
    segment[1].T = segment[1].E / segment[1].k;
    segment[0].T = segment[0].E / segment[0].k;
    
    prediction.push([time,segment[2].T]);
  }
  end = time;
  
  
  var linewidth = 1;
  
  var plot = $.plot($graph, 
    [
      {data: outside_data, lines: { show: true, fill: false }, color: "rgba(0,0,255,0.8)"},
      {data: power_data, yaxis: 2, lines: { show: true, fill: true, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,150,0,0.2)"},
      
      {data: lab_back_left, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      {data: lab_back_right, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      {data: lab_front_left, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      {data: lab_back_right, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"},
      
      {data: sim, lines: { show: true, fill: false, lineWidth: 3}, color: "rgba(0,0,0,1)"},
      {data: prediction, lines: { show: true, fill: false, lineWidth: 3}, color: "rgba(0,0,0,0.5)"}
       
    ], {
  grid: { show: true, hoverable: true, clickable: true },
  xaxis: { mode: "time", localTimezone: true, min: start, max: end },
  selection: { mode: "xy" }
  });
  
  
  }
  
  $("#simulate").click(function(){

    for (i in segment) 
    {
      segment[i].u = $("#u"+i).val();
      segment[i].k = $("#k"+i).val();
    }
    
    simulate();
  });
 
</script>
