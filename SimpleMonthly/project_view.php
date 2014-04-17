<?php
  global $path;
?>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/openbem.js"></script>
<br>
<ul class="breadcrumb">
<li><a href="<?php echo $path; ?>openbem/projects">My Projects</a> <span class="divider">/</span></li>
<li class="active project_name"></li>
</ul>

<div class="row">

    <div class="span3">
        <h3>OpenBEM Project</h3>
        
        <p>Back to <a href="<?php echo $path; ?>openbem/projects">all projects</a></p>
        <br>
        <button id="create-scenario" class="btn btn-info" style="width:87%">Create new scenario</button>
    </div>
     
    <div class="span8">
        <h3 class="project_name"></h3>
        <table class="table">
        <tr>
        <th>Name</th>
        <th>Description</th>
        <th>H</th>
        <th></th>
        </tr>
        <tbody id="scenarios"></tbody>
        </table>
        
        <div id="noscenarios" class="alert alert-warning" style="display:none">No scenarios have been created yet, click create new scenario to create your first model</div>
    </div>
    
    
</div>

<script>

var path = "<?php echo $path; ?>";

var scenarios = [ 
    {id:1, name:'Master', description:"Detached, traditional welsh stone construction", wk:150},
    {id:2, name:'Scenario 1', description:"Internal insulation", wk:100},
    {id:3, name:'Scenario 2', description:"External insulation", wk:80}
];

var project_id = <?php echo $project_id; ?>;

var scenarios = openbem.get_scenarios(project_id);
var project_details = openbem.getprojectdetails(project_id);
$(".project_name").html(project_details.project_name);

console.log(project_details);

draw_scenarios();

$("#create-scenario").click(function(){

    var sid = scenarios.length;
    var name = "Scenario "+sid;
    if (sid==0) name = "Master";
    
    var meta = {name: name, description: "", wk:"---"};
    var id = openbem.add_scenario(project_id,meta);
    
    if (id) {
        var scenario = {scenario_id:id, scenario_meta:meta};
        scenarios.push(scenario);
        draw_scenarios();
    }
});

$("#scenarios").on('click','.clone-scenario', function() {
    var sid = $(this).attr('sid');
    openbem.clone_scenario(project_id,sid);
    scenarios = openbem.get_scenarios(project_id);
    draw_scenarios();
});

function draw_scenarios()
{
    var out = "";
    for (z in scenarios)
    {
      if (z==0) out += '<tr class="info">'; else out += "<tr>";

      // out += "<td>"+scenarios[z].scenario_id+"</td>";

      
      if (scenarios[z].scenario_meta==undefined)
      {
        scenarios[z].scenario_meta = {name:"", description:"",wk:0};
      }
      
      out += "<td>"+scenarios[z].scenario_meta.name+"</td>";      
      out += "<td>"+scenarios[z].scenario_meta.description+"</td>";
      out += "<td>"+scenarios[z].scenario_meta.wk+" W/K</td>";
      
      out += '<td>';
      out += '<a href="'+path+'openbem/monthly?project_id='+project_id+'&scenario_id='+scenarios[z].scenario_id+'"><span class="label label-info">Open <i class="icon-folder-open icon-white"></i></span></a> ';
      out += '<div style="cursor:pointer" class="label label-info clone-scenario" sid='+scenarios[z].scenario_id+'>Clone <i class="icon-file icon-white"></i></div> ';
      
      var master_id = scenarios[0].scenario_id;
      out += '<a href="'+path+'openbem/compare?project_id='+project_id+'&scenarioA='+master_id+'&scenarioB='+scenarios[z].scenario_id+'"><div class="label label-info">Compare <i class="icon-random icon-white"></i></div></a>';
      out += '</td>';
      
      out += "</tr>";
    }

    $("#scenarios").html(out);
    
    if (scenarios.length==0) $("#noscenarios").show(); else $("#noscenarios").hide();
}

</script>
