$("#openbem").on("click",'#add-element', function(){
    var size = Object.size(data.fabric.elements)+1;
    var name = "Element "+size;
    data.fabric.elements.push({type:'wall', name: name, area: 0, uvalue: 0, wk: 0});
    add_element(size-1);
    update();
});

$("#openbem").on("click",'#add-window', function(){
    var size = Object.size(data.fabric.elements)+1;
    var name = "Window";
    data.fabric.elements.push({
        type:'window', 
        name: name, 
        area: 0, 
        uvalue: 0, 
        wk: 0,
        orientation: 3,
        overshading: 2,
        g: 0.76,
        gL: 0.8,
        ff: 0.7
    });
    add_window(size-1);
    update();
});

$("#openbem").on("click",'.delete-element', function(){
    var row = $(this).attr('row');
    $(this).closest('tr').remove();
    data.fabric.elements.splice(row,1);
    
    $("#elements").html("");
    $("#windows").html("");
    for (z in data.fabric.elements) {
        if (data.fabric.elements[z].type!='window') {
            add_element(z);
        } else {
            add_window(z);
        }
    }
    update();
});

function add_element(z)
{
    $("#elements").append($("#element-template").html());
    $("#elements [key='data.fabric.elements.template.type']").attr('key','data.fabric.elements.'+z+'.type');
    $("#elements [key='data.fabric.elements.template.name']").attr('key','data.fabric.elements.'+z+'.name');
    $("#elements [key='data.fabric.elements.template.area']").attr('key','data.fabric.elements.'+z+'.area');
    $("#elements [key='data.fabric.elements.template.uvalue']").attr('key','data.fabric.elements.'+z+'.uvalue');
    $("#elements [key='data.fabric.elements.template.kvalue']").attr('key','data.fabric.elements.'+z+'.kvalue');
    $("#elements [key='data.fabric.elements.template.wk']").attr('key','data.fabric.elements.'+z+'.wk');
    
    $("#elements [row='template']").attr('row',z);
}

function add_window(z)
{
    $("#windows").append($("#window-template").html());
    $("#windows [key='data.fabric.elements.template.name']").attr('key','data.fabric.elements.'+z+'.name');
    $("#windows [key='data.fabric.elements.template.area']").attr('key','data.fabric.elements.'+z+'.area');
    $("#windows [key='data.fabric.elements.template.uvalue']").attr('key','data.fabric.elements.'+z+'.uvalue');
    
    $("#windows [key='data.fabric.elements.template.orientation']").attr('key','data.fabric.elements.'+z+'.orientation');
    $("#windows [key='data.fabric.elements.template.overshading']").attr('key','data.fabric.elements.'+z+'.overshading');
    
    $("#windows [key='data.fabric.elements.template.g']").attr('key','data.fabric.elements.'+z+'.g');
    $("#windows [key='data.fabric.elements.template.gL']").attr('key','data.fabric.elements.'+z+'.gL');
    $("#windows [key='data.fabric.elements.template.ff']").attr('key','data.fabric.elements.'+z+'.ff');
    $("#windows [key='data.fabric.elements.template.wk']").attr('key','data.fabric.elements.'+z+'.wk');
    $("#windows [key='data.fabric.elements.template.gain']").attr('key','data.fabric.elements.'+z+'.gain');
    
    $("#windows [row='template']").attr('row',z);
}

function elements_initUI()
{
    // Initial addition of floors
    for (z in data.fabric.elements) {
        if (data.fabric.elements[z].type!='window') {
            add_element(z);
        } else {
            add_window(z);
        }
    }
}
