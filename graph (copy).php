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


<div id="graph_bound" style="width:100%; height:400px; position:relative; ">
  <div id="graph"></div>
</div>


<script id="source" language="javascript" type="text/javascript">

  var path = "<?php echo $path; ?>";
  var apikey = "";
  
  var timeWindow = (3600000*18.0*1);	// Initial time window
  var start = +new Date - timeWindow;	// Get start time
  var end = +new Date;				        // Get end time
  
  var $graph_bound = $('#graph_bound');
  var $graph = $('#graph').width($graph_bound.width()).height($('#graph_bound').height());

  var lab_back_left = feed.get_data(16006,start,end,0);
  var outside_data = feed.get_data(14972,start,end,0);
  var power_data = feed.get_data(1066,start,end,0);
  
  
  var internal = 18.35;
  
  var thermal_capacity = 7500000;
  var energy = internal * thermal_capacity; 
  var lossrate = 150;
  
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
    
    heatloss = -1 * ((internal - outside) * lossrate);
    energy += (heatloss + heatinput) * step;
    internal = energy / thermal_capacity;
    
    sim.push([time,internal]);
  }
  
  var plot = $.plot($graph, [{data: sim, lines: { show: true, fill: false }},{data: outside_data, lines: { show: true, fill: false }},{data: lab_back_left, lines: { show: true, fill: false }},{data: power_data, yaxis: 2, lines: { show: true, fill: false }}], {
  grid: { show: true, hoverable: true, clickable: true },
  xaxis: { mode: "time", localTimezone: true, min: start, max: end },
  selection: { mode: "xy" }
  });
  
</script>
