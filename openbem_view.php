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

if (!$scenario_id) $scenario_id = 0;
if (!$project_id) $project_id = 0;


?>

<div style="background-color: rgb(238, 238, 235); border-bottom:1px solid #ddd;">
<div class="container">

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/openbem-0.0.1.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/ui-helper-0.0.2.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/ui-openbem-0.0.1.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/model/datasets-0.0.1.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/model/model-0.0.1.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/vectormath.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/arrow.js"></script>
<br>

<ul class="breadcrumb">
    <?php if ($project_id) { ?>
    <li><a href="<?php echo $path; ?>openbem/projects">My Projects</a> <span class="divider">/</span></li>
    <li><a href="<?php echo $path; ?>openbem/project?project_id=<?php echo $project_id; ?>" class="project_name"></a> <span class="divider">/</span></li>
    <?php } else { ?>
    <li><a href="#start">My Projects</a> <span class="divider">/</span></li>
    <?php } ?>
    <li class="active scenario_name">Scenario <?php echo $scenario_id; ?></li>
    <li class="pull-right"><a id="house_graphic" style="margin-right:10px">Show house graphic</a><i class="icon-home icon-white"></i></li>
</ul>

<div id="topgraphic"></div>

</div>
</div>

<div class="container">
<br>
<div id="openbem" class="row">

  <div class="span3">

    <h3>OpenBEM Scenario</h3>
    
    <canvas id="rating" width="269px" height="350px" style="margin-bottom:20px"></canvas>

    <table class="table table-bordered">
        <tr><td><a href="#start">Home</a></td></tr>
        <tr><td><a href="#context">1. Floors</a></td></tr>
        <tr><td><a href="#ventilation">2. Ventilation</a></td></tr>
        <tr><td><a href="#elements">3. Fabric</a></td></tr>
        <tr><td><a href="#system">4. Energy Systems</a></tr>
        <tr><td><a href="#export">Import/Export</a></tr>
        <tr><td><a href="#detail">Detailed view</a></tr>
    </table>
    <table class="table table-bordered">
        <tr><td><a href="#LAC">Lighting, Appliances & Cooking</a></td><td><input type="checkbox" key="data.use_LAC"/></td></tr>
        <tr><td><a href="#waterheating">Water Heating</a></td><td><input type="checkbox" key="data.use_water_heating"/></td></tr>
        <tr><td><a href="#solarhotwater">Solar Hot Water heating</a></td><td><input type="checkbox" key="data.use_SHW"/></td></tr>
        <tr><td><a href="#appliancelist">Appliance List</a></td><td><input type="checkbox" key="data.use_appliancelist"/></td></tr>
        <tr><td><a href="design">2D Layout Editor</a></td><td></td></tr>
    </table>
    
    <table class="table table-bordered">
        <tr><td><a href="https://github.com/emoncms/openbem/blob/v3/docs/guide.md">User guide</a></td></tr>
        <tr><td><a href="https://github.com/emoncms/openbem/blob/v3/docs/ElementLibrary.md">Element Library</a></td></tr>
        <tr><td><a href="https://github.com/openenergymonitor/documentation/tree/master/BuildingBlocks/BuildingEnergyModelling">Building Energy Modelling</a></td></tr>
    </table>
     
  </div>

  <div class="span9">
      <div id="content"></div>
  </div>

</div>

</div>

<script>

    var path = "<?php echo $path;?>";
    load_view("#topgraphic",'topgraphic');
    
    var c=document.getElementById("rating");
    var ctx=c.getContext("2d");
    
    var scenario_id = <?php echo $scenario_id; ?>;
    var scenario = openbem.get_scenario(scenario_id);
    var data = scenario.scenario_data;

    $(".scenario_name").html(scenario.scenario_meta.name);

    var project_id = <?php echo $project_id; ?>; 
    var project_details = openbem.getprojectdetails(project_id);
    $(".project_name").html(project_details.project_name);
    
    var keys = {};
    calc.run();
    calc.run();

    var page = (window.location.hash).substring(1);
    if (!page) page = "start";
    load_view("#content",page);
    InitUI();
    
    UpdateUI(data);
    draw_openbem_graphics();
    draw_rating(ctx);
    
    $(window).on('hashchange', function() {
        page = (window.location.hash).substring(1);
        load_view("#content",page);
        InitUI();
        UpdateUI(data);
    });
    
    function update()
    {
        calc.run();
        UpdateUI(data);
        draw_rating(ctx);
        draw_openbem_graphics();
    }
    
    $("#openbem").on("change",'[key]', function(){
        var key = $(this).attr('key');
        var val = $(this).val();
        var input_type = $(this).attr('type');
        if (input_type=='checkbox') val = $(this)[0].checked;
        
        if (!isNaN(val)) val *= 1;
        varset(key,val);
        
        $("#openbem").trigger("onKeyChange",{key:key,value:val});
        update();
        
        console.log(key+ " "+val);
        
        openbem.save_scenario(scenario_id,data); 
    });
    
    $("#house_graphic").click(function(){
        if ($("#house_graphic").html()=="Show house graphic") {
            $("#topgraphic").show();
            $("#rating").hide();
            $("#house_graphic").html("Hide house graphic");
        } else {
            $("#topgraphic").hide();
            $("#rating").show();
            $("#house_graphic").html("Show house graphic");
        }
    });
    
    $("#topgraphic").show();
    $("#rating").hide();
    $("#house_graphic").html("Hide house graphic");
    
    
</script>
