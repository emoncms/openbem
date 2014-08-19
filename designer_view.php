<?php 

/*
All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org
*/

    global $session,$path; 

?>

<div id="page-container" style="position:relative;" >
  <canvas id="can" style="position:absolute; top:0px; left:0px; margin:0; padding:0;" oncontextmenu="return false;"></canvas>
  
  <div id="hud" style="position:absolute; top:20px; right:20px; width:200px; height:300px; background-color:rgba(50,50,50,0.8); padding:20px; color:#aaa">
  
  <b>HUD</b>
  
  <p><b>Mouse:</b><br>
    mx: <span id="mx"></span><br>
    my: <span id="my"></span><br>
    mousedown: <span id="mousedown"></span><br>
    pointselected: <span id="pointselected"></span><br>
    activepoint: <span id="activepoint"></span><br>
    command: <br>
    <div id="cmdstr"></div><br>
    <p>left, right, up, down: Create segments in direction</p>
    <p>n: Hold to create a new point on mouse click</p>
  </p>
  
  </div>
</div>

<script type="application/javascript">

    var container = $("#page-container");
    var can = $("#can");

    var width = 0;
    var height = 0;

    init();
    
    var canvas = document.getElementById("can");
    var ctx = canvas.getContext("2d");
    
    var border = 0;
    
    var mousedown = false;
    var keydown = false;
    var pointselected = false;
    var activepoint = 0;
    var lastaction = false;
    
    var linelength = "100";
    $("#cmdstr").html(linelength);
    
    var offsetx = 0;
    var offsety = 0;
    var lmx = 0;
    var lmy = 0;
    var mx = 0;
    var my = 0;
    var minx = 0;
    var miny = 0;
    var maxx = width*1.0;
    var maxy = height*1.0;
    
    var points = [
        {x:100,y:100,highlight:false}
    ];
    
    var wallsegments = [];
    
    draw()

    $(window).resize(function(){
       init();
       draw();
    });
    
    $(window).ready(function(){
        $("#footer").css('background-color','#181818');
        $("#footer").css('color','#999');
    });
    
    // View resize
    function init()
    {
       lastwidth = width;
       lastheight = height;
       
       width = container.width();
       height = $(window).height()-80;
       
       maxx = minx + ((width / lastwidth) * (maxx-minx));
       maxy = miny + ((height / lastheight) * (maxy-miny));
       
       can.attr('width',width);
       can.attr('height',height);
    }
    
    function draw()
    {
        //---------------------------------------------------------------------------------
        // Clear background
        ctx.fillStyle = "#000";
        ctx.fillRect(border,border,width-border*2,height-border*2);
        
        //---------------------------------------------------------------------------------
        // Draw grids
        
        var size = 25;
        
        var xn = Math.round((maxx - minx) / size);
        var yn = Math.round((maxy - miny) / size);
        
        ctx.beginPath();

        var gridy = Math.floor(miny / size) * size;
        for (var y=0; y<yn; y++) {
            gridy += size; 
            pixelposy = ((gridy - miny) / (maxy - miny)) * height;
            ctx.moveTo(border,pixelposy);
            ctx.lineTo(width-border,pixelposy);
        }
        
        var gridx = Math.floor(minx / size) * size;
        for (var x=0; x<xn; x++) {
            gridx += size; 
            pixelposx = ((gridx - minx) / (maxx - minx)) * width;
            ctx.moveTo(pixelposx,border);
            ctx.lineTo(pixelposx,height-border);
        }
        
        ctx.lineWidth = 1;
        
        ctx.strokeStyle = "#222";
        ctx.stroke();
        
        var size = 100;
        
        var xn = Math.round((maxx - minx) / size);
        var yn = Math.round((maxy - miny) / size);
        
        ctx.beginPath();

        var gridy = Math.floor(miny / size) * size;
        for (var y=0; y<yn; y++) {
            gridy += size; 
            pixelposy = ((gridy - miny) / (maxy - miny)) * height;
            ctx.moveTo(border,pixelposy);
            ctx.lineTo(width-border,pixelposy);
        }
        
        var gridx = Math.floor(minx / size) * size;
        for (var x=0; x<xn; x++) {
            gridx += size; 
            pixelposx = ((gridx - minx) / (maxx - minx)) * width;
            ctx.moveTo(pixelposx,border);
            ctx.lineTo(pixelposx,height-border);
        }
        
        ctx.lineWidth = 0.5;
        ctx.strokeStyle = "#888";
        ctx.stroke();
        
        ctx.beginPath();
        ctx.strokeStyle = "#239dfb";
        ctx.fillStyle = "#239dfb";
        
        //---------------------------------------------------------------------------------
        // Draw points
        
        for (var p = 0; p < points.length; p++)
        {
            var px = points[p].x;
            var py = points[p].y;
            
            pixelposx = ((px - minx) / (maxx - minx)) * width;
            pixelposy = ((py - miny) / (maxy - miny)) * height;
            
            ctx.fillRect(pixelposx-4,pixelposy-4,8,8);
            if (points[p].highlight == true) ctx.strokeRect(pixelposx-8,pixelposy-8,16,16);
        }
        
        //---------------------------------------------------------------------------------
        // Draw lines:
        
        for (var p = 0; p < wallsegments.length; p++)
        {
            // get line positions in view and draw line:
            var p1x = points[wallsegments[p][0]].x;
            var p1y = points[wallsegments[p][0]].y;
            pixelpos1x = ((p1x - minx) / (maxx - minx)) * width;
            pixelpos1y = ((p1y - miny) / (maxy - miny)) * height;
            ctx.moveTo(pixelpos1x,pixelpos1y);
            
            var p2x = points[wallsegments[p][1]].x;
            var p2y = points[wallsegments[p][1]].y;
            pixelpos2x = ((p2x - minx) / (maxx - minx)) * width;
            pixelpos2y = ((p2y - miny) / (maxy - miny)) * height;
            ctx.lineTo(pixelpos2x,pixelpos2y);
            
            // Calculation of mid point position on line with vector math
            var v1x = p1x;
            var v1y = p1y;
            
            var v2x = p2x - v1x;
            var v2y = p2y - v1y;
            
            v2mag = Math.sqrt(v2x*v2x + v2y*v2y);
            
            var v2x_n = v2x / v2mag;
            var v2y_n = v2y / v2mag;
            
            var v3x = v2x_n * v2mag * 0.5;
            var v3y = v2y_n * v2mag * 0.5;
            
            tx = v1x + v3x;
            ty = v1y + v3y;
            ctx.textAlign="center";
            
            // Fix to 3 decimal places, this should also update the actual line length
            var text = ""+(v2mag/100);
            var dp = text.split(".");
            if (dp[1]!=undefined && dp[1].length>3) text = (parseFloat(text)).toFixed(3);
            
            // Rotation and drawing of text to line angle
            ctx.save();
            tx = ((tx - minx) / (maxx - minx)) * width;
            ty = ((ty - miny) / (maxy - miny)) * height;
            ctx.translate(tx, ty);
            ctx.rotate(Math.atan(v2y/v2x));
            ctx.textAlign = "center";
            ctx.fillText(text+" m", 0, -8);
            ctx.restore();
        }
        
        ctx.lineWidth = 2.0;
       
        ctx.stroke();
    }
    
    $(this.canvas).mousedown(function(event) {
        mousedown = true;
        $("#mousedown").html('true');
        var changed = point_scan();
        if (changed) draw();
    });
    
    $(this.canvas).mouseup(function(event) { 
        mousedown = false;
        $("#mousedown").html('false');
        pointselected = false;
        $("#pointselected").html('false');
    });
    
    // Add a new point if n key is pressed
    $(this.canvas).click(function() { 
        if (keydown=='n'){
            mx = Math.round(mx/25)*25;
            my = Math.round(my/25)*25;
            px = ((mx / width) * (maxx - minx)) + minx;
            py = ((my / width) * (maxx - minx)) + minx;
            points.push({x:px,y:my,highlight:false});
            draw();
        }
    });
    
    
    $(this.canvas).mousemove(function(event) {
    
        if(event.offsetX==undefined) { // this works for Firefox
            mx = (event.pageX - $(event.target).offset().left);
            my = (event.pageY - $(event.target).offset().top);
        } else {
            mx = event.offsetX;
            my = event.offsetY;
        }
        
        // mx = Math.round(mx/25)*25;
        // my = Math.round(my/25)*25;
        
        $("#mx").html(mx);
        $("#my").html(my);
        
        mmx = mx - lmx;
        mmy = my - lmy;
        lmx = mx;
        lmy = my;
        
        // Move the view if v key is pressed
        if (mousedown && keydown == 'v')
        {  
            minx -= mmx;
            maxx -= mmx;
            
            miny -= mmy;
            maxy -= mmy;
            
            draw();
        }
        
        // Move points if point is selected
        var changed = point_scan();
        
        if (pointselected)
        {
            points[pointselected].x += mmx;
            points[pointselected].y += mmy;
            changed = true;
        }
        
        if (changed) draw();
    });
    
    // To make floor plan layout entry as fast as possible the editor will have a comprehensive set of keyboard shortcuts
    // that can be used to specify exact element directions, lengths etc
    // So far there are keyboard shortcuts for zooming in/out, creating new line segments and setting their lengths
    
    $( window ).keydown(function(event) {
    
        // If last keydown was not false then there is a double key press
        if (keydown!=false) {
            if (event.key=='-' && keydown=='v') view_zoom(2.0); // Zoom out
            if (event.key=='=' && keydown=='v') view_zoom(0.5); // Zoom in
        } else {
        // Single key press:
            keydown = event.key;
            
            // Create a line segment starting from the specified point at angle and length
            if (keydown=='Left') create_line_segment(activepoint,-Math.PI/2,linelength);
            if (keydown=='Right') create_line_segment(activepoint,+Math.PI/2,linelength);
            if (keydown=='Up') create_line_segment(activepoint,Math.PI,linelength);
            if (keydown=='Down') create_line_segment(activepoint,Math.PI*0,linelength);
            
            // Resize line from keyboard entry
            if (isInt(keydown) || keydown=='.' || keydown=="Backspace")
            {
                if (lastaction=='linedir') {
                    linelength = "";
                    lastaction = false;
                }
                
                if (keydown=="Backspace") {
                    if (linelength.length) linelength = linelength.slice(0,-1);
                } else {
                    linelength += keydown;
                }
                
                $("#cmdstr").html(linelength);
                resize_active_line(linelength);
            }
        }
    });
    
    $( window ).keyup(function(event) {
        keydown = false;
    });
    
    
    // Scan points to see if mouse is over point
    function point_scan()
    {
        var changed = false; 
        
        for (i in points)
        {
          var px = points[i].x;
          var py = points[i].y;
          
          var ppx = ((px - minx) / (maxx - minx)) * width;
          var ppy = ((py - miny) / (maxy - miny)) * height;
          
          if  (Math.abs(ppx - mx)<8 && Math.abs(ppy-my)<8) {
            if (!points[i].highlight) {
                changed = true;
                $("body").css('cursor','pointer');
            }
            points[i].highlight = true;
            
            if (mousedown) {
                pointselected = i;
                activepoint = i;
                $("#pointselected").html(pointselected);
                $("#activepoint").html(activepoint);
                
            }
            
          } else {
            if (points[i].highlight) {
                changed = true;
                $("body").css('cursor','default');
            }
            points[i].highlight = false;
          }
        }
        
        return changed;
    }
    
    function view_zoom(zoom)
    {
        var view_width = (maxx-minx) * zoom;
        var view_mid = minx + (view_width / 2);
        minx = view_mid - view_width / 2;
        maxx = view_mid + view_width / 2;

        var view_height = (maxy-miny) * zoom;
        var view_mid = miny + (view_height / 2);
        miny = view_mid - view_height / 2;
        maxy = view_mid + view_height / 2;
        
        draw();
    }
    
    function create_line_segment(point,angle,length)
    {
        var x = points[point].x;
        var y = points[point].y;
        
        x = x + Math.sin(angle) * length;
        y = y + Math.cos(angle) * length;
        
        points.push({x:x,y:y,highlight:false});
        wallsegments.push([point,points.length-1]);
        activepoint = points.length-1;
        $("#activepoint").html(activepoint);
        
        draw();
        
        lastaction = 'linedir';
    }
    
    function resize_active_line(linelength)
    {
        if (parseFloat(linelength)>0)
        {
            var p1 = wallsegments[wallsegments.length-1][0];
            var p2 = wallsegments[wallsegments.length-1][1];
            
            var v1x = points[p1].x;
            var v1y = points[p1].y;
            
            var v2x = points[p2].x - v1x;
            var v2y = points[p2].y - v1y;
            
            v2mag = Math.sqrt(v2x*v2x + v2y*v2y);
            
            var v2x_n = v2x / v2mag;
            var v2y_n = v2y / v2mag;
            
            var v3x = v2x_n * parseFloat(linelength);
            var v3y = v2y_n * parseFloat(linelength);
            
            points[points.length-1].x = v1x + v3x;
            points[points.length-1].y = v1y + v3y;
            
            draw();
        }
    }
    
    function isInt(n){
        return n%1===0;
    }
  
</script>
