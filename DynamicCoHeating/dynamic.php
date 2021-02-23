<?php global $path; ?>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.selection.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/graph/vis.helper.js"></script>
<h1>Dynamic Coheating</h1>
<p>The black line is simulated internal temperature based on properties defined below.</p>
<p>The gray line is internal temperature prediction 4h +</p>

<div id="graph_bound" style="height:400px; width:100%; position:relative; ">
    <div id="graph"></div>
    <div id="graph-buttons" style="position:absolute; top:18px; right:32px; opacity:0.5;">
        <div class='btn-group'>
            <button class='btn graph-time' type='button' time='1'>D</button>
            <button class='btn graph-time' type='button' time='7'>W</button>
            <button class='btn graph-time' type='button' time='30'>M</button>
            <button class='btn graph-time' type='button' time='365'>Y</button>
        </div>

        <div class='btn-group' id='graph-navbar'>
            <button class='btn graph-nav' id='zoomin'>+</button>
            <button class='btn graph-nav' id='zoomout'>-</button>
            <button class='btn graph-nav' id='left'><</button>
            <button class='btn graph-nav' id='right'>></button>
        </div>

    </div>
    <h3 style="position:absolute; top:0px; left:32px;"><span id="stats"></span></h3>
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
  <select class="feed_selector" name="external_feed" style="width:208px"></select>
</div><br>

<div class="input-prepend">
  <span class="add-on" style="width:180px; text-align:right;" >Heating power feed: </span>
  <select class="feed_selector" name="power_feed" style="width:208px"></select>
</div><br>

<div class="input-prepend">
  <span class="add-on" style="width:180px; text-align:right;" >Solar power feed: </span>
  <select class="feed_selector" name="solar_feed" style="width:208px"></select>
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
  <select class="feed_selector" name="internal_feed" style="width:208px"></select>
</div><br>

<p>Other feeds (comma seperated feed id's):</p>

<div class="input-append">
<input id="other_feeds"  id="appendedInputButton" type="text" style="width:345px">
<button id="other_feeds_ok" class="btn" type="button">Ok</button>
</div><br>

<script>
  
var timeWindow = (3600000*24.0*1);	//Initial time window

// Default settings
var defaults = {
  power_feed: 0,
  solar_feed: 0,
  external_feed: 0,
  internal_feed: 0,
  
  other_feeds: [],
  
  solarfactor: 0.6,
  solaroffset: 1,
  
  segments: [
    {u:130,k:11000000,T:10},
    {u:340,k:2500000,T:15},
    {u:712,k:600000,T:15}
  ],
  
  start: +new Date - timeWindow,
  end: +new Date
};

// Load in settings from local storage if available
var settings = localStorage.getItem("dynamicmodel");
if (settings==null) {
  settings = defaults;
} else {
  settings = JSON.parse(settings);
}

view.start = settings.start
view.end = settings.end
view.calc_interval();

var segment = settings.segments;

var $graph_bound = $('#graph_bound');
var $graph = $('#graph').width($graph_bound.width()).height($('#graph_bound').height());

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

data = {}

load();

function load() {
    
    data.power_feed = feed.get_data(settings.power_feed,view.start,view.end,view.interval,0,0);
    data.solar_feed = feed.get_data(settings.solar_feed,view.start,view.end,view.interval,0,0);
    data.external_feed = feed.get_data(settings.external_feed,view.start,view.end,view.interval,0,0);
    data.internal_feed = feed.get_data(settings.internal_feed,view.start,view.end,view.interval,0,0);
    simulate();
} 

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
  for (var z=1; z<data.external_feed.length; z++)
  {
    var lasttime = data.external_feed[z-1][0];
    var time = data.external_feed[z][0];
    var outside = data.external_feed[z][1];
    
    var step = (time - lasttime) / 1000.0;

    // Not all feeds share the same timestamp and interval rate and so we need to use
    // binary search to locate closest power datapoint to the external temperature 
    // datapoint which we are using as our base time.
    // The external temperature is recorded every 60s while power is recorded every 10s
    // we use binary search to find the power value every 60 seconds
    
    var spos = 0, epos = data.power_feed.length-1, mid = 0;
    for (var n=0; n<20; n++) {
      mid = spos + Math.round((epos - spos ) / 2);
      if (data.power_feed[mid][0] > time) epos = mid; else spos = mid;
    }
    heatinput = data.power_feed[mid][1];
    
    // Here we do the same for solar PV data
    
    if (settings.solar_feed>0) {
      var spos = 0, epos = data.solar_feed.length-1, mid = 0;
      for (var n=0; n<20; n++) {
        mid = spos + Math.round((epos - spos ) / 2);
        if (data.solar_feed[mid][0] > (time+3600000*settings.solaroffset)) epos = mid; else spos = mid;
      }
      heatinput += data.solar_feed[mid][1]*settings.solarfactor;
    }
    // And the same again for our internal temperature reference which we use to compare the simulated 
    // temperature against and so calculate the average error.
    
    var spos = 0, epos = data.internal_feed.length-1, mid = 0;
    for (var n=0; n<20; n++) {
      mid = spos + Math.round((epos - spos ) / 2);
      if (data.internal_feed[mid][0] > time) epos = mid; else spos = mid;
    }
    var ref = data.internal_feed[mid][1];
    
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
      {data: data.external_feed, lines: { show: true, fill: false }, color: "rgba(0,0,255,0.8)"},
      {data: data.power_feed, yaxis: 2, lines: { show: true, fill: true, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,150,0,0.2)"},
      {data: data.solar_feed, yaxis: 2, lines: { show: true, fill: false, fillColor: "rgba(255,150,0,0.2)"}, color: "rgba(255,255,0,0.2)"},
      {data: data.internal_feed, lines: { show: true, fill: false }, color: "rgba(200,0,0,1.0)"},
      {data: sim, lines: { show: true, fill: false, lineWidth: 3}, color: "rgba(0,0,0,1)"}
  ];
  
  for (i in settings.other_feeds)
  {
    var fdata = feed.get_data(settings.other_feeds[i],view.start,view.end,view.interval);
    feeds.push({data: fdata, lines: { show: true, fill: false, lineWidth:linewidth}, color: "rgba(255,0,0,0.3)"});
  }
  
  var plot = $.plot($graph, feeds, {
    grid: { show: true, hoverable: true, clickable: true },
    xaxis: { mode: "time", localTimezone: true, min: view.start, max: view.end },
    selection: { mode: "x" }
  });
  
  $graph.bind("plotselected", function (event, ranges)
  {
      view.start = ranges.xaxis.from;
      view.end = ranges.xaxis.to;
      view.calc_interval()
      load();
  });

  $("#total_wk").html(total_wk.toFixed(0));
  $("#total_thermal_capacity").html(total_thermal_capacity);
  $("#error").html("Model is within an average of: "+(error/ data.external_feed.length).toFixed(3)+"C of measured temperature");
}

$("#zoomout").click(function () {view.zoomout(); load();});
$("#zoomin").click(function () {view.zoomin(); load();});
$('#right').click(function () {view.panright(); load();});
$('#left').click(function () {view.panleft(); load();});
$('.graph-time').click(function () {view.timewindow($(this).attr("time")); load();});

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

$(".feed_selector[name=external_feed]").html(draw_feed_selector(settings.external_feed));
$(".feed_selector[name=internal_feed]").html(draw_feed_selector(settings.internal_feed));
$(".feed_selector[name=power_feed]").html(draw_feed_selector(settings.power_feed));
$(".feed_selector[name=solar_feed]").html(draw_feed_selector(settings.solar_feed));
$("#other_feeds").val(settings.other_feeds.join(","));
$("#solar_scale").val(settings.solarfactor);
$("#solar_offset").val(settings.solaroffset);


$("#other_feeds_ok").click(function(){

  var str = $("#other_feeds").val();
  var arr = str.split(",");
  
  settings.other_feeds = [];
  
  for (z in arr) {
    for (i in feedlist) {
      if (feedlist[i].id == arr[z]) {
        settings.other_feeds.push(arr[z]);
      }
    }
  }
  $("#other_feeds").val(settings.other_feeds.join(","));
  simulate();
});

$("#solar_ok").click(function(){
  settings.solarfactor = parseFloat($("#solar_scale").val());
  settings.solaroffset = parseFloat($("#solar_offset").val());
  simulate();
});

$(".feed_selector").change(function(){
  var name = $(this).attr("name");
  var index = $(this).val();
  settings[name] = feedlist[index].id;
  data[name] = feed.get_data(settings[name],view.start,view.end,view.interval);
  simulate();
});

$("#save").click(function(){
  for (i in segment) 
  {
    segment[i].u = $("#u"+i).val();
    segment[i].k = $("#k"+i).val();
    segment[i].T = $("#t"+i).val();
  }
  
  settings.start = view.start
  settings.end = view.end
  localStorage.setItem("dynamicmodel",JSON.stringify(settings));
  
});

function draw_feed_selector(selected_feed) {
    var out = "", selected = "";
    for (z in feedlist) {
      if (feedlist[z].id==selected_feed) selected = 'selected'; else selected = '';
      if (feedlist[z].datatype==1) out += "<option value='"+z+"' "+selected+">"+feedlist[z].name+"</option>";
    }
    return out;
}

</script>
