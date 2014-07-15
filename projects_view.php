<?php
  global $path;

?>
<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/js/openbem-0.0.1.js"></script>
<br>
<ul class="breadcrumb">
<li class="active">My Projects</li>
</ul>

<div class="row">

    <div class="span3">
        <h3>OpenBEM</h3>
        <p style="color:#aaa">Open source building energy modelling</p>
        <br>
        <button id="create-new-step1" class="btn btn-info" style="width:87%">Create new project</button>
        
        <div id="new-project-input" style="display:none">
        <p><b>Add new project:</b></p>
        <p>
            <span class="muted">Project name</span>
            <br><input id="project-name-input" type="text" style="width:82%" />
        </p>
        <p>
            <span class="muted">Project description</span>
            <br><input id="project-description-input" type="text" style="width:82%" />
        </p>
        <button id="create-new-step2" class="btn btn-info" style="width:87%">Create</button>
        </div>
        
    </div>
     
    <div class="span8">
        <h3>My Projects</h3>

        <table class="table">
        <tr>
          <th>Project name</th>
          <th>Project description</th>
          <th>Last modified</th>
          <th>Shared with</th>
          <th></th>
        </tr>
        
        <tbody id="projects"></tbody>

        </table>
        
        <div id="noprojects" class="alert alert-warning" style="display:none">No projects have been created yet, click create new project to get started</div>

    </div>
</div>

<script>

var path = "<?php echo $path; ?>";
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

var projects = [
    {id:1, name:"Ogoronwy", description:"Detached, traditional welsh stone construction", mdate:"Apr 2",sharedwith:[{userid:1, username:"charlie"}]},
    {id:2, name:"Gorwell", description:"1950s cavity wall insulation", mdate:"Apr 2",sharedwith:[{userid:1, username:"charlie"}]}
];

var projects = openbem.getprojects();

draw_projects();

$("#create-new-step1").click(function(){
    $("#create-new-step1").hide();
    $("#new-project-input").show();
});

$("#create-new-step2").click(function(){
    
    var name = $("#project-name-input").val();
    var description = $("#project-description-input").val();
    
    if (name=="") {
        alert("Please enter a project name");
    } else {

        var projectid = openbem.addproject(name,description)*1;
        if (projectid) {
            console.log(projectid);
            
        var project = {
            project_id:projectid, 
            project_name: name, 
            project_description: description, 
            project_mdate:(new Date()).getTime() * 0.001,
            sharedwith:[]
        };
        
        projects.push(project);
        draw_projects();
        }

        $("#create-new-step1").show();
        $("#new-project-input").hide();
        $("#noprojects").hide();
        
        $("#project-name-input").val("");
        $("#project-description-input").val("");
    } 
});

$("#projects").on('click','.delete-project', function() {
    var projectid = $(this).attr('projectid');
    var z = $(this).attr('z');
    if (openbem.deleteproject(projectid))
    {
        projects.splice(z,1);
        draw_projects();
    }
});

function draw_projects()
{
    var out = "";
    for (z in projects)
    {
      out += "<tr>";
      out += "<td>"+projects[z].project_name+"</td>";
      out += "<td>"+projects[z].project_description+"</td>";
      
      var t = new Date();
      var d = new Date(projects[z].project_mdate*1000);
      
      if (t.getYear()==d.getYear()) {
        out += "<td>"+d.getDate()+" "+months[d.getMonth()]+"</td>";
      } else {
        out += "<td>"+d.getDate()+"/"+(d.getMonth()+1)+"/"+d.getFullYear()+"</td>";
      }
      
      out += "<td>";
      for (i in projects[z].sharedwith)
      {
        out += '<span class="label">'+projects[z].sharedwith[i].username+'</span>';
      }
      out += "</td>";
      out += '<td><a href="'+path+'openbem/project?project_id='+projects[z].project_id+'"><span class="label label-info">Open <i class="icon-folder-open icon-white"></i></span></a></td>';
      out += '<td><i class="icon-trash delete-project" projectid='+projects[z].project_id+' z='+z+' style="cursor:pointer"></i></td>';
      out += "</tr>";
    }

    $("#projects").html(out);

    if (projects.length==0) $("#noprojects").show();
}

</script>
