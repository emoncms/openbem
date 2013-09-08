<?php
  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

  */

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


<script id="source" language="javascript" type="text/javascript">

  var path = "<?php echo $path; ?>";
  var apikey = "";
  
  var timeWindow = (3600000*12*1);	// Initial time window
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
  
  var internal = lab_back_left[0][1];
  var internal_air = lab_back_left[0][1];
  
  var thermal_capacity = 9000000;
  var energy = internal * thermal_capacity; 
  var lossrate = 120;
  
  // thermal capacity of air is 66774 J/K
  // we need a thermal capacity of something like 10x that
  var thermal_capacity_air = 600000;
  var energy_air = internal * thermal_capacity_air; 
    
  var heatloss = 0;
  var heatinput = 0;
  
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
    
    var heatloss_air_to_body = ((internal_air - internal) * 500);
    var heatloss_body_to_outside = ((internal - outside) * lossrate);

    energy_air += (-1 * heatloss_air_to_body + heatinput) * step;
    energy += (heatloss_air_to_body + (-1 * heatloss_body_to_outside)) * step;
    
    internal = energy / thermal_capacity;
    internal_air = energy_air / thermal_capacity_air;
        
    sim.push([time,internal_air]);
  }
  
  // PREDICTION !!
  var prediction = [];
  var lpv = power_data[power_data.length-1][1];
  var time = outside_data[outside_data.length-1][0];
  for (var n=0; n<480; n++)
  {
    time += (30*1000);
    
    var heatloss_air_to_body = ((internal_air - internal) * 500);
    var heatloss_body_to_outside = ((internal - outside) * lossrate);

    energy_air += (-1 * heatloss_air_to_body + lpv) * 30;
    energy += (heatloss_air_to_body + (-1 * heatloss_body_to_outside)) * 30;
    
    internal = energy / thermal_capacity;
    internal_air = energy_air / thermal_capacity_air;


    prediction.push([time,internal_air]);
  }
  end = time;
  
  
  var linewidth = 8;
  
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
  
</script>
