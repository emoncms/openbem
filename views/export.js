$("#import-data").click(function(){
    data = JSON.parse($("#import-export").val());
    update();
    openbem.save_scenario(scenario_id,data); 
});

$("#input-data").click(function(){
    var inputdata = openbem.extract_inputdata(data);
    $("#import-export").html(JSON.stringify(inputdata, null, 4));
    $('textarea').height($('textarea').prop('scrollHeight'));
});

$("#all-data").click(function(){
    $("#import-export").html(JSON.stringify(data, null, 4));
    $('textarea').height($('textarea').prop('scrollHeight'));
});

function export_initUI()
{
    var inputdata = openbem.extract_inputdata(data);
    $("#import-export").html(JSON.stringify(inputdata, null, 4));
    $('textarea').height($('textarea').prop('scrollHeight'));
}

