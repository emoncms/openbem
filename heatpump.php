<?php global $path; ?>
<!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.js"></script>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />

<h2>Heatpump efficiency explorer</h2>
<p><b>Note:</b> Building model is based on a small single space building, floor area is 21m2, walls are 600mm thick stone with plaster on hard, 100mm insulation in roof, roof lights, solid floor. See details and picture <a href="http://openenergymonitor.org/emon/node/2783" >here</a>. The radiator model is based on the heat output of the <a href="http://www.dimplex.co.uk/smartrad/technical_specification.htm" >dimplex smartrad at a Delta_T of 25K (1400W)</a>. For the output of this application to be relevant the building model, radiator and heatpump model parameters need to be taylored for the building being explored. At present the results of this application should be taken as a concept, the accuracy of it needs to be tested properly (which is the next step). But it does illustrate some of the core cocepts of a heatpumps operation.</p>

<p><b>Theory: </b><a href="http://openenergymonitor.org/emon/node/2999">Building model</a>, <a href="http://openenergymonitor.org/emon/node/3011">Radiator model</a>, <a href="http://openenergymonitor.org/emon/node/3021">Heatpump model</a></p>

<p>See also: <a href="http://www.emoncms.org/openbem/heatingexplorer">Gas or electric heating period explorer</a></p>

<br>

<p><b>Axes:</b> First axis on the left is flow temperature, second axis is COP and third is indoor and outdoor temperature</p>
<div class="input-prepend input-append">
  <span class="add-on">Set point: </span>
  <input id="setpoint" type="text" style="width:50px" value="18" />
  <span class="add-on">C</span>
</div>

<div class="input-prepend input-append" style="margin-left:10px">
  <span class="add-on">Heater output: </span>
  <input id="heateroutput" type="text" style="width:50px" value="2500" />
  <span class="add-on">W</span>
</div>

<div id="graph_bound" style="width:100%; height:400px; position:relative; ">
  <div id="graph"></div>
</div>

<b style="padding-left:18px">Heating period A </b>
<span id="amount-A" style="border: 0; color: #f6931f; font-weight: bold;" >6:30 - 10:00</span>

<div style="float:right">
<span id="amount-B" style="border: 0; color: #f6931f; font-weight: bold;" >17:30 - 23:00</span>
<b style="padding-right:18px"> Heating period B</b>
</div>
<div style="padding:10px 18px 10px 18px">
  <div id="slider-range-A"></div>
</div>
<div style="padding:10px 18px 20px 18px">
  <div id="slider-range-B"></div>
</div>

<table class="table">
<tr><td>Average heatpump COP: </td><td><span id="average_COP"></span></td><td></td></tr>
<tr><td>Average heatpump electrical input: </td><td><span id="average_hp_input"></span>W</td><td></td>
</tr>
<tr><td>Average heatpump heat output: </td><td><span id="average_power"></span>W</td><td></td></tr>
<tr><td>Average internal temperature: </td><td><span id="average_t3"></span>C</td><td></td></tr>
<tr><td>Average internal temperature when heating is on: </td><td><span id="average_t3_hon"></span>C</td><td></td></tr>
<tr><td>Average internal temperature during occupancy period: </td><td><span id="average_t3_sp"></span>C</td><td></td></tr>
<tr><td>Average outside temperature: </td><td><span id="average_outside"></span>C</td><td></td></tr>
</table>

<div class="input-prepend input-append">
  <span class="add-on">External temperature mid point: </span>
  <input id="externalmid" type="text" style="width:50px" value="10" />
  <span class="add-on">W</span>
</div>

<div class="input-prepend input-append" style="margin-left:10px">
  <span class="add-on">External temperature swing: </span>
  <input id="externalswing" type="text" style="width:50px" value="0" />
  <span class="add-on">W</span>
</div>

<script id="source" language="javascript" type="text/javascript">

  var $graph_bound = $('#graph_bound');
  var $graph = $('#graph').width($graph_bound.width()).height($('#graph_bound').height());

  var start = 0, end = 0;
  var graph_data = [];
  var outside_data = [];  
  var COP_data = [];   
  var flowtemp_data = [];
  
  $(window).resize(function(){
    $graph.width($graph_bound.width());
    if (embed) $graph.height($(window).height());
    plot();
  });
  
  var occupancy_start_A = 7, occupancy_end_A = 9;
  var occupancy_start_B = 18, occupancy_end_B = 23;
  
  var heating_on_A = 6.5, heating_off_A = 10;
  var heating_on_B = 17.5, heating_off_B = 23;
  
  var setpoint = 18;
  var heateroutput = 2500;
  var hs = 0.4;
  
  var externalmid = 10;
  var externalswing = 0;
  
  var RatedPower = 1400;
  var RatedDeltaT = 25;
  
  var u1 = 130, k1 = 11000000, t1 = 10.0;
  var u2 = 340, k2 = 2500000, t2 = 10.0;
  var u3 = 712, k3 = 600000, t3 = 10.0;
  
  var wk = 1 / ((1/u1)+(1/u2)+(1/u3));
  
  var e1 = t1 * k1;
  var e2 = t2 * k3;
  var e3 = t3 * k2;
  
  var sum_power = 0,
      sum_t3 = 0,
      sum_t3_hon = 0,
      sum_t3_sp = 0,
      sum_outside = 0,
      sum_COP = 0,
      sum_hp_power = 0,
      count_hon = 0,
      count_sp = 0,
      count_hp = 0;
   
  sim();
  plot();
  
  function sim()
  {  
    graph_data = [];
    outside_data = [];
    COP_data = []; 
    flowtemp_data = [];
    
    var start_t1 = t1;
    
    var timestep = 30;
    var itterations = 3600*24 / timestep;

    sum_power = 0;
    sum_t3 = 0;
    sum_t3_hon = 0;
    sum_t3_sp = 0;
    sum_outside = 0;
    sum_COP = 0;
    sum_hp_power = 0;
    count_hon = 0;
    count_sp = 0;
    count_hp = 0;

    for (var i=0; i<itterations; i++)
    {
      var time = i * timestep * 1000; 

      var PracticalCOP = undefined;
      var flow_temperature = undefined;     
      
      var outside = externalmid - Math.cos(2*Math.PI*(i/itterations)) * externalswing;
      sum_outside += outside;
      
      var hour = time / 3600000;
      
      var heatinput = 0;
      var Powerinput = 0;
      
      // Heating schedule
      if ((hour>heating_on_A && hour<heating_off_A) || (hour>heating_on_B && hour<heating_off_B))
      {
        if (t3>setpoint+(hs/2)) heating = false;
        if (t3<setpoint-(hs/2)) heating = true;
        
        if (heating) {
          heatinput = heateroutput;
          
          // Radiator model
          var Delta_T = Math.pow(heatinput/RatedPower,1/1.3)*RatedDeltaT;
          var MWT = t3 + Delta_T;
          flow_temperature = MWT + heatinput / (2 * 4186.0 * 0.1);
          
          // Really basic steady-state heatpump model
          condencing = flow_temperature + 3;
          refrigerant = outside - 3;
          IdealCOP = (condencing + 273) / ((condencing+273) - (refrigerant + 273));
          PracticalCOP = 0.5 *  IdealCOP;
          Powerinput =  heatinput /  PracticalCOP;
          
          sum_COP += PracticalCOP;
          count_hp++;
        }
        sum_t3_hon += t3;
        count_hon ++;
      }
      
      // Temperature at occupancy times
      if ((hour>occupancy_start_A && hour<occupancy_end_A) || (hour>occupancy_start_B && hour<occupancy_end_B))
      {
        sum_t3_sp += t3;
        count_sp ++;
      }
      
      h3 = heatinput - u3*(t3 - t2);
      h2 = u3*(t3 - t2) - u2*(t2 - t1);      
      h1 = u2*(t2 - t1) - u1*(t1 - outside);

      e3 += h3 * timestep;
      e2 += h2 * timestep;
      e1 += h1 * timestep;
      
      t3 = e3 / k3;
      t2 = e2 / k2;
      t1 = e1 / k1;
                        
      graph_data.push([time,t3]);
      outside_data.push([time,outside]);
      COP_data.push([time,PracticalCOP]);
      flowtemp_data.push([time,flow_temperature]);
      end = time;
      
      sum_hp_power += Powerinput;
      sum_power += heatinput;
      sum_t3 += t3;
    }
    if (Math.abs(start_t1-t1)>hs*1.0) sim();
    
    var average_power = sum_power / itterations;
    $("#average_power").html(average_power.toFixed(0));
    
    var average_outside = sum_outside / itterations;
    $("#average_outside").html(average_outside.toFixed(2));
        
    $("#average_power").html(average_power.toFixed(0));
    var diff_continuous = (1 - (average_power / (wk * (setpoint-average_outside)))) * 100; 
    if (diff_continuous<0) moreless = "% More"; else moreless = "% Less";
    $("#diff_continuous").html(Math.abs(diff_continuous).toFixed(0)+moreless);
        
    var average_t3 = sum_t3 / itterations;
    $("#average_t3").html(average_t3.toFixed(2));
    
    var average_t3_hon = sum_t3_hon / count_hon;
    $("#average_t3_hon").html(average_t3_hon.toFixed(2));

    var average_t3_sp = sum_t3_sp / count_sp;
    $("#average_t3_sp").html(average_t3_sp.toFixed(2));
    
    var average_hp_power = sum_hp_power / itterations;
    $("#average_hp_input").html(average_hp_power.toFixed(0));

    var average_COP = sum_COP / count_hp;
    $("#average_COP").html(average_COP.toFixed(2));
  }

  function plot()
  {
    var plot = $.plot($graph, [
      {data: graph_data, lines: { show: true, fill: false }},
      {data: outside_data, lines: { show: true, fill: false }},
      {data: COP_data, yaxis: 2, lines: { show: true, fill: false }},
      {data: flowtemp_data, yaxis: 3, lines: { show: true, fill: false }}
    ], {
      grid: { show: true },
      xaxis: { mode: 'time', min: start, max: end },
      yaxes: [{},{min:1.5}],
      selection: { mode: "xy" }
    });
    /*
    var canvas = plot.getCanvas();
    var ctx = canvas.getContext('2d');
   
   ctx.fillStyle = "rgba(255,0,0,0.5)"; 
   ctx.rect(19,10,150,363);
   ctx.fill();
    */
    
  }
  
  $("#setpoint").keyup(function()
  {
    setpoint = parseFloat($(this).val());
    if (isNaN(setpoint)) setpoint = 0;
    sim();
    plot();
  });
  
  $("#heateroutput").keyup(function()
  {
    heateroutput = parseFloat($(this).val());
    if (isNaN(heateroutput)) heateroutput = 0;
    sim();
    plot();
  });
  
  $("#externalmid").keyup(function()
  {
    externalmid = parseFloat($(this).val());
    if (isNaN(externalmid)) externalmid = 0;
    sim();
    plot();
  });
  
  $("#externalswing").keyup(function()
  {
    externalswing = parseFloat($(this).val());
    if (isNaN(externalswing)) externalswing = 0;
    sim();
    plot();
  });
    
  $(function() {
    $( "#slider-range-A" ).slider({
      range: true,
      min: 0,
      max: 24,
      step: 0.5,
      values: [ heating_on_A, heating_off_A ],
      slide: function( event, ui ) {
        heating_on_A = ui.values[ 0 ];
        heating_off_A = ui.values[ 1 ];
        
        sim();
        plot();
     
        var minon = (ui.values[0] - Math.floor(ui.values[0])) * 60;
        if (minon<10) minon = "0"+minon;
        var minoff = (ui.values[1] - Math.floor(ui.values[1])) * 60;
        if (minoff<10) minoff = "0"+minoff;
        $( "#amount-A" ).html(Math.floor(ui.values[0])+":"+minon+" - "+Math.floor(ui.values[1])+":"+minoff );
      }
    });

    $( "#slider-range-B" ).slider({
      range: true,
      min: 0,
      max: 24,
      step: 0.5,
      values: [ heating_on_B, heating_off_B ],
      slide: function( event, ui ) {
        heating_on_B = ui.values[ 0 ];
        heating_off_B = ui.values[ 1 ];
        
        sim();
        plot();
  
        var minon = (ui.values[0] - Math.floor(ui.values[0])) * 60;
        if (minon<10) minon = "0"+minon;
        var minoff = (ui.values[1] - Math.floor(ui.values[1])) * 60;
        if (minoff<10) minoff = "0"+minoff;
        $( "#amount-B" ).html(Math.floor(ui.values[0])+":"+minon+" - "+Math.floor(ui.values[1])+":"+minoff );
      }
    });
    
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ) );
  });
  
  </script>
