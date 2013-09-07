<?php 
/*

All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

*/

global $path; ?>
<script type="text/javascript" src="<?php echo $path; ?>Modules/sap/saptable.js"></script>

<br>
<h1>OpenBEM</h1>
<p>An open source simple building energy model based on SAP 2012</p>

<h3>Internal temperature</h3>

<canvas id="can" width="960px" height="450px"></canvas>

<h4>Average internal temperature: 18C</h4>

<script>
               //  0    1    2    3    4    5    6    7    8    9    10   11   12   13   14   15   16   17   18   19   20   21   22   23
  var internal = [18.0,17.0,16.5,16.3,16.2,16.1,16.1,18.0,21.0,21.0,21.0,18.0,17.0,16.5,16.3,16.2,16.1,16.1,18.0,21.0,21.0,21.0,21.0,21.0];

  var cnvs = document.getElementById("can");
  var ctx = cnvs.getContext("2d");

  var height = 450;

  ctx.clearRect(0,0,960,height);
  ctx.strokeRect(1,1,958,height-2);  
  
  var spacing = 960 / 24;
  for (var h=0; h<24; h++)
  {
    ctx.moveTo(1+h*spacing,1);
    ctx.lineTo(1+h*spacing,height-2);
  }
  ctx.strokeStyle = "#aaa" ; 
  ctx.stroke();

  ctx.lineWidth = 3;
  ctx.strokeStyle    = "rgba(50, 50, 250, 1)";
  ctx.beginPath();
  var pos = 0;
  for (var h=0; h<24; h++)
  {
    var lastpos = pos;
    pos = height - (internal[h]/30*height);
    ctx.strokeRect(1+(h*spacing)-5,pos-5,10,10);
    
    ctx.moveTo(1+((h-1)*spacing),lastpos);
    ctx.lineTo(1+(h*spacing),pos);
  }
  ctx.lineWidth = 10;
  ctx.strokeStyle    = "rgba(100, 100, 100, 0.2)";
  ctx.stroke();

  
</script>

