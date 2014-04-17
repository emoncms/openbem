<?php
  global $path;
  //openbem/Modules/openbem/SimpleMonthly/interface

?>

<h2>Scenario list</h2>
<table class="table table-striped">
<tr>
  <th>Building id</th>
  <th>Building name</th>
  <th>Thermal performance</th>
  <th>SAP Rating</th>
</tr>
<tbody id="building-list"></tbody>
</table>

<i class="icon-plus" id="add"></i>

<script type="text/javascript" src="<?php echo $path; ?>Modules/openbem/SimpleMonthly/interface/openbem.js"></script>

<script>

    var path = "<?php echo $path; ?>";
    
    var list = openbem.list();
    
    console.log(list);
    
    var out = "";
    for (z in list)
    {
      out += "<tr><td><a href='monthly/"+list[z].building+"'>Scenario "+list[z].building+"</a></td><td>"+list[z].name+"</td></tr>";
    }
    
    $("#building-list").html(out);
    
    $("#add").click(function(){
    
      var list = openbem.list();
      var building = list.length + 1;
    
      openbem.save(building,[]);
    
    });
    
</script>
