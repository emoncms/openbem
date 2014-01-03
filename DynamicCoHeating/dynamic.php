<?php
  global $path;
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/DynamicCoHeating/openbem.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.selection.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>

<ul class="nav nav-pills">
  <li>
  <li><a href="<?php echo $path; ?>openbem/monthly/<?php echo $building; ?>">Simple Monthly</a></li>
  </li>
  <li class="active">
    <a href="#">Dynamic Coheating</a>
  </li>
</ul>

<h1>Dynamic Coheating</h1>
<p>The black line is simulated internal temperature based on properties defined below.</p>
<p>The gray line is internal temperature prediction 4h +</p>

<div id="graph_bound" style="width:100%; height:400px; position:relative; ">
  <div id="graph"></div>
</div>

<h3>Total W/K heat loss: <span id="total_wk"> </span> W/K</h3>
<h3>Total thermal capacity: <span id="total_thermal_capacity"></span> J/K</h3>

<h3><span id="error"></span></h3>

<table class="table">
<tr><th>Segment</th><th>W/K</th><th>Thermal capacity</th><th>Initial temperature</th></tr>
<tbody id="segment_config"></tbody>
</table>
<p><i>Segment 0 connects to external temperature, Segment <span class="numofsegments"></span> to heat input</i></p>

<button id="add-element" class="btn">Add element</button>
<button id="remove-element" class="btn">Remove element</button>
<button id="simulate" class="btn">Simulate</button>
<button id="save" class="btn">Save All</button>
<h2>Configure</h2>



<div class="input-prepend">
  <span class="add-on" style="width:180px; text-align:right;" >External temperature feed: </span>
  <select id="external_feed" style="width:208px"></select>
</div><br>

<div class="input-prepend">
  <span class="add-on" style="width:180px; text-align:right;" >Heating power feed: </span>
  <select id="power_feed" style="width:208px"></select>
</div><br>

<div class="input-prepend">
  <span class="add-on" style="width:180px; text-align:right;" >Solar power feed: </span>
  <select id="solar_feed" style="width:208px"></select>
</div><br>

<div class="input-prepend input-append">
  <span class="add-on" style="width:90px"> scale by: </span>
  <input id="solar_scale" type="text" style="width:65px"/>
  <span class="add-on" style="width:90px"> offset by: </span>
  <input id="solar_offset" type="text" style="width:65px"/>
  <button id="solar_ok" class="btn" type="button">Ok</button>
</div><br>

<div class="input-prepend">
  <span class="add-on" style="width:180px; text-align:right;" >Internal temperature feed: </span>
  <select id="internal_feed" style="width:208px"></select>
</div><br>

<p>Other feeds (comma seperated feed id's):</p>

<div class="input-append">
<input id="other_feeds"  id="appendedInputButton" type="text" style="width:345px">
<button id="other_feeds_ok" class="btn" type="button">Ok</button>
</div><br>

<div class="input-prepend input-append">
  <span class="add-on" style="width:180px; text-align:right;" >Time Window (hours): </span>
  <input type="text" id="timewindow" style="width:155px" />
  <button id="timewindow_ok" class="btn" type="button">Ok</button>
</div><br>

<script >

  var path = "<?php echo $path; ?>";
  var apikey = "";
  
  var building = <?php echo $building; ?>;
  var settings = openbem.getdynamic(building);
  
  if (settings==0)
  {
    settings = {
  
      powerfeed: 0,
      solarfeed: 0,
      externalfeed: 0,
      internalfeed: 0,
      
      otherfeeds: [],
      
      solarfactor: 0.6,
      solaroffset: 1,
      
      segments: [
        {u:130,k:11000000,T:10},
        {u:340,k:2500000,T:15},
        {u:712,k:600000,T:15}
      ],
      
      timewindow: (3600000*24)
    };
  }
  
 
  var segment = settings.segments;

  //var start = +1378638900000 - settings.timewindow;	// Get start time
  //var end = +1378638900000 +1000*1000;				        // Get end time
  
  var timeWindow = (3600000*24.0*0.7);	//Initial time window
  var start = +new Date - timeWindow;	//Get start time
  var end = +new Date;				    //Get end time
  
  var $graph_bound = $('#graph_bound');
  var $graph = $('#graph').width($graph_bound.width()).height($('#graph_bound').height());

  var power_feed_data = feed.get_data(settings.powerfeed,start,end,0);
  var solar_feed_data = feed.get_data(settings.solarfeed,start,end,0);
  var external_feed_data = feed.get_data(settings.externalfeed,start,end,0);
  var internal_feed_data = feed.get_data(settings.internalfeed,start,end,0);
  
  var segment_config_html = "";
    
  for (i in segment) 
  {
    segment_config_html += "<tr><td>"+i+"</td>";
    segment_config_html += "<td><input id='u"+i+"' type='text' value='"+segment[i].u+"'/ ></td>";
    segment_config_html += "<td><input id='k"+i+"' type='text' value='"+segment[i].k+"'/ ></td>";
    segment_config_html += "<td><input id='t"+i+"' type='text' value='"+segment[i].T+"'/ ></td></tr>";
  }

  $(".numofsegments").html(segment.length-1);
  $("#segment_config").html(segment_config_html);
 
  simulate();  
  
  function simulate()
  {  
    for (i in segment) 
    {
      segment[i].u = $("#u"+i).val();
      segment[i].k = $("#k"+i).val();
      segment[i].T = $("#t"+i).val();
    }
    
    // INITIAL CONDITIONS  
    var sum_u = 0;
    var sum_k = 0;
        
    for (i in segment) 
    {
      segment[i].E = segment[i].T * segment[i].k;
      segment[i].H = 0;
      sum_u += 1 / segment[i].u;
      sum_k += 1*segment[i].k
    }
    
    var total_wk = 1 / sum_u;
    var total_thermal_capacity = sum_k;
    
    var sim = [];
    
    var error = 0;
    for (var z=1; z<external_feed_data.length; z++)
    {
      var lasttime = external_feed_data[z-1][0];
      var time = external_feed_data[z][0];
      var outside = external_feed_data[z][1];
      
      var step = (time - lasttime) / 1000.0;

      // Not all feeds share the same timestamp and interval rate and so we need to use
      // binary search to locate closest power datapoint to the external temperature 
      // datapoint which we are using as our base time.
      // The external temperature is recorded every 60s while power is recorded every 10s
      // we use binary search to find the power value every 60 seconds
      
      var spos = 0, epos = power_feed_data.length-1, mid = 0;
      for (var n=0; n<20; n++) {
        mid = spos + Math.round((epos - spos ) / 2);
        if (power_feed_data[mid][0] > time) epos = mid; else spos = mid;
      }
      heatinput = power_feed_data[mid][1];
      
      // Here we do the same for solar PV data
      
      if (settings.solarfeed>0) {
        var spos = 0, epos = solar_feed_data.length-1, mid = 0;
        for (var n=0; n<20; n++) {
          mid = spos + Math.round((epos - spos ) / 2);
          if (solar_feed_data[mid][0] > (time+3600000*settings.solaroffset)) epos = mid; else spos = mid;
        }
        heatinput += solar_feed_data[mid][1]*settings.solarfactor;
      }
      // And the same again for our internal temperature reference which we use to compare the simulated 
      // temperature against and so calculate the average error.
      
      var spos = 0, epos = internal_feed_data.length-1, mid = 0;
      for (var n=0; n<20; n++) {
        mid = spos + Math.round((epos - spos ) / 2);
        if (internal_feed_data[mid][0] > time) epos = mid; else spos = mid;
      }
      var ref = internal_feed_data[mid][1];
      
      // The following 14 lines of code is the actual simulation code
      // We calculate how much heat (in Watts) flow between the segments
      // Its a two stage process:
      
      // 1) we calculate the heat flow rate from current temperatures
      
      var len = segment.length-1;
      for (var i=0; i<=len; i++)
      {
        var H_left = 0, H_right = 0;
        if (i>0) H_left = (segment[i].T - segment[i-1].T) * segment[i].u; else H_left = (segment[i].T - outside) * segment[i].u;
        if (i<len) H_right = (segment[i+1].T - segment[i].T) * segment[i+1].u; else H_right = heatinput;
        segment[i].H = H_right - H_left;
      }
      
      // 2) We calculate the change of energy in each segment and the new temperature
      // of each segment.
      
      for (i in segment) 
      {
        segment[i].E += segment[i].H * step;
        segment[i].T = segment[i].E / segment[i].k;
      }
      
      // Populate the simulation plot with simulated internal temperature
      sim.push([time,segment[segment.length-1].T]);
      
      // Average error calculation
      error += Math.abs(segment[segment.length-1].T - ref);
    }
    
    var linewidth = 1;
    
    var feeds = [
        {data: external_feed_data, lines: { show: true, fill: false }, color: "rgba(0,0,255,0.8)"},
        {data: power_feed_data, yaxis: 2, lines: { show: true, fill: true, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,150,0,0.2)"},
        {data: solar_feed_data, yaxis: 2, lines: { show: true, fill: false, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,255,0,0.2)"},
        {data: internal_feed_data, lines: { show: true, fill: false }, color: "rgba(200,0,0,1.0)"},
        {data: sim, lines: { show: true, fill: false, lineWidth: 3}, color: "rgba(0,0,0,1)"}
    ];
    
    for (i in settings.otherfeeds)
    {
      var data = feed.get_data(settings.otherfeeds[i],start,end,0);
      feeds.push({data: data, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"});
    }
    
    var plot = $.plot($graph, feeds, {
    grid: { show: true, hoverable: true, clickable: true },
    xaxis: { mode: "time", localTimezone: true, min: start, max: end },
    selection: { mode: "xy" }
    });

    $("#total_wk").html(total_wk.toFixed(0));
    $("#total_thermal_capacity").html(total_thermal_capacity);
    $("#error").html("Model is within an average of: "+(error/ external_feed_data.length).toFixed(3)+"C of measured temperature");
  }
  
  $("#simulate").click(function(){
  
    for (i in segment) 
    {
      segment[i].u = $("#u"+i).val();
      segment[i].k = $("#k"+i).val();
      segment[i].T = $("#t"+i).val();
    }
  
    simulate();
  });
  
  $("#add-element").click(function(){
    if (segment.length) { 
      segment.push(segment[segment.length-1]);
      
      var i = segment.length-1;
      segment_config_html = "";
      segment_config_html += "<tr><td>"+i+"</td>";
      segment_config_html += "<td><input id='u"+i+"' type='text' value='"+segment[i].u+"'/ ></td>";
      segment_config_html += "<td><input id='k"+i+"' type='text' value='"+segment[i].k+"'/ ></td>";
      segment_config_html += "<td><input id='t"+i+"' type='text' value='"+segment[i].T+"'/ ></td></tr>";
      
      $('#segment_config').append(segment_config_html);
      simulate();
    }
  });
  
  $("#remove-element").click(function(){
  
    if (segment.length>1) {
      segment.splice(segment.length-1,1);
      $('#segment_config tr:last').remove();

      simulate();
    }
  });
  
  // Load feed list from server
  var feedlist = feed.list();
  
  // Populate feed selectors
  
  var out = "", selected = "";
  for (z in feedlist) {
    if (feedlist[z].id==settings.externalfeed) selected = 'selected'; else selected = '';
    if (feedlist[z].datatype==1) out += "<option value='"+z+"' "+selected+">"+feedlist[z].name+"</option>";
  }
  $("#external_feed").html(out);
  
  var out = "", selected = "";
  for (z in feedlist) {
    if (feedlist[z].id==settings.internalfeed) selected = 'selected'; else selected = '';
    if (feedlist[z].datatype==1) out += "<option value='"+z+"' "+selected+">"+feedlist[z].name+"</option>";
  }
  $("#internal_feed").html(out);

  var out = "", selected = "";
  for (z in feedlist) {
    if (feedlist[z].id==settings.powerfeed) selected = 'selected'; else selected = '';
    if (feedlist[z].datatype==1) out += "<option value='"+z+"' "+selected+">"+feedlist[z].name+"</option>";
  }
  $("#power_feed").html(out);
  
  var out = "", selected = "";
  for (z in feedlist) {
    if (feedlist[z].id==settings.solarfeed) selected = 'selected'; else selected = '';
    if (feedlist[z].datatype==1) out += "<option value='"+z+"' "+selected+">"+feedlist[z].name+"</option>";
  }
  $("#solar_feed").html(out);
  
  $("#other_feeds").val(settings.otherfeeds.join(","));
  $("#timewindow").val(settings.timewindow/3600000);

  $("#solar_scale").val(settings.solarfactor);
  $("#solar_offset").val(settings.solaroffset);
    
  $("#other_feeds_ok").click(function(){
  
    var str = $("#other_feeds").val();
    var arr = str.split(",");
    
    settings.otherfeeds = [];
    
    for (z in arr) {
      for (i in feedlist) {
        if (feedlist[i].id == arr[z]) {
          settings.otherfeeds.push(arr[z]);
        }
      }
    }
    $("#other_feeds").val(settings.otherfeeds.join(","));
    simulate();
  });
  
  $("#timewindow_ok").click(function(){
  
    settings.timewindow = $("#timewindow").val()*3600000;
    start = +new Date - settings.timewindow;	// Get start time
    
    power_feed_data = feed.get_data(settings.powerfeed,start,end,0);
    solar_feed_data = feed.get_data(settings.solarfeed,start,end,0);
    external_feed_data = feed.get_data(settings.externalfeed,start,end,0);
    internal_feed_data = feed.get_data(settings.internalfeed,start,end,0);
  
    simulate();
  });
  
  $("#solar_ok").click(function(){
    settings.solarfactor = parseFloat($("#solar_scale").val());
    settings.solaroffset = parseFloat($("#solar_offset").val());
    simulate();
  });
  
  // feed selector controllers
  
  $("#external_feed").click(function(){
    var z = $(this).val();
    if (feedlist[z].id!=settings.externalfeed) 
    { 
      settings.externalfeed = feedlist[z].id;
      external_feed_data = feed.get_data(settings.externalfeed,start,end,0);
      simulate();
    }
  });
  
  $("#power_feed").click(function(){
    var z = $(this).val();
    if (feedlist[z].id!=settings.powerfeed) 
    { 
      settings.powerfeed = feedlist[z].id;
      power_feed_data = feed.get_data(settings.powerfeed,start,end,0);
      simulate();
    }
  });
  
  $("#solar_feed").click(function(){
    var z = $(this).val();
    if (feedlist[z].id!=settings.solarfeed) 
    { 
      settings.solarfeed = feedlist[z].id;
      solar_feed_data = feed.get_data(settings.solarfeed,start,end,0);
      simulate();
    }
  });
  
  $("#internal_feed").click(function(){
    var z = $(this).val();
    if (feedlist[z].id!=settings.internalfeed) 
    { 
      settings.internalfeed = feedlist[z].id;
      internal_feed_data = feed.get_data(settings.internalfeed,start,end,0);
      simulate();
    }
  });
  
  $("#save").click(function(){
    for (i in segment) 
    {
      segment[i].u = $("#u"+i).val();
      segment[i].k = $("#k"+i).val();
      segment[i].T = $("#t"+i).val();
    }
  
    openbem.savedynamic(building,settings); 
  });

</script>
