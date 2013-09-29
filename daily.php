<?php

  global $path;
  
?>
<br>

<script type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.js"></script>
<h2>Daily average's</h2>
<div id="daily" style="width:960px; height:600px;" ></div>

<h2>Heat loss W/K vs Delta T</h2>
<div id="graph" style="width:960px; height:600px;" ></div>

<script>

var path = "<?php echo $path; ?>";
var apikey = "";

var interval = 3600*24;

var timeWindow = (3600000*24.0*30);
var view = {start:0,end:0};
view.start = +new Date - timeWindow;
view.end = +new Date;

// Load feed data
var lab_back_right = feed.get_timestore_average(16004,view.start,view.end,interval);
var lab_back_left = feed.get_timestore_average(16006,view.start,view.end,interval);
var lab_front_left = feed.get_timestore_average(16008,view.start,view.end,interval);
var lab_front_right = feed.get_timestore_average(16010,view.start,view.end,interval);
var outside_air = feed.get_timestore_average(14972,view.start,view.end,interval);
var lab_power = feed.get_timestore_average(16012,view.start,view.end,interval);

// Sort individual feed data into one data object with matched timestamps
var data = {};
for (z in lab_power) data[lab_power[z][0]] = {'power':lab_power[z][1]};
for (z in lab_back_right) if (data[lab_back_right[z][0]]!=undefined) data[lab_back_right[z][0]]['t1'] = lab_back_right[z][1];
for (z in lab_back_left) if (data[lab_back_left[z][0]]!=undefined) data[lab_back_left[z][0]]['t2'] = lab_back_left[z][1];
for (z in lab_front_left) if (data[lab_front_left[z][0]]!=undefined) data[lab_front_left[z][0]]['t3'] = lab_front_left[z][1];
for (z in lab_front_right) if (data[lab_front_right[z][0]]!=undefined) data[lab_front_right[z][0]]['t4'] = lab_front_right[z][1];
for (z in outside_air) if (data[outside_air[z][0]]!=undefined) data[outside_air[z][0]]['outside'] = outside_air[z][1];

// Finally we do the heat loss calculation and add it to the chart data
var chartdata = [];

for (z in data)
{
  
  var wk1 = data[z].power / (data[z].t1 - data[z].outside);
  var wk2 = data[z].power / (data[z].t2 - data[z].outside);
  var wk3 = data[z].power / (data[z].t3 - data[z].outside);
  var wk4 = data[z].power / (data[z].t4 - data[z].outside);
    
  chartdata.push([data[z].t1-data[z].outside, wk1]);
  chartdata.push([data[z].t2-data[z].outside, wk2]);
  chartdata.push([data[z].t3-data[z].outside, wk3]);
  chartdata.push([data[z].t4-data[z].outside, wk4]);
  
  // var avi = (data[z].t1+data[z].t2+data[z].t3+data[z].t4) / 4.0;
  // var avwk = data[z].power / (avi - data[z].outside);
  // chartdata.push([avi-data[z].outside, avwk]);
}

var options = {
  points: {show:true}
}

$.plot($("#graph"), [chartdata], options);

for (z in lab_power) lab_power[z][1] = lab_power[z][1] * 0.024;

$.plot($("#daily"),
[
  {data:lab_front_left, lines: {show:true}},
  {data:lab_front_right, lines: {show:true}},
  {data:lab_back_left, lines: {show:true}},
  {data:lab_back_right, lines: {show:true}},
  {data:outside_air, lines: {show:true}},

  {data:lab_power, bars: { show: true, align: "center", barWidth: 0.75*interval*1000, fill: true}, yaxis: 2}  
],{
  xaxis: { mode: "time", min: view.start, max: view.end, minTickSize: [interval, "second"] },
  grid: {hoverable: true, clickable: true},
  selection: { mode: "x" }
}

);
    
</script>
