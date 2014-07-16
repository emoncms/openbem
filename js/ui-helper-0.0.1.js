function load_view(view)
{
    var result = ""; 
    $.ajax({url: path+"Modules/openbem/views/"+view+".html", async: false, cache: false, success: function(data) {result = data;} });
    return result;
}

function varset(key,value)
{
    var p = key.split('.');

    switch (p.length) {
        case 0:
            break;
        case 1:
            window[p[0]] = value;
            break;
        case 2:
            window[p[0]][p[1]] = value;
            break;
        case 3:
            window[p[0]][p[1]][p[2]] = value;
            break;
        case 4:
            window[p[0]][p[1]][p[2]][p[3]] = value;
            break;
        case 5:
            window[p[0]][p[1]][p[2]][p[3]][p[4]] = value;
            break;
        case 6:
            window[p[0]][p[1]][p[2]][p[3]][p[4]][p[5]] = value;
            break;
    }
}

function InitUI()
{
    $(".monthly").each(function(){
    
        var name = $(this).attr('key');
        var dp = $(this).attr('dp');
        var title = $(this).attr('title');
        var units = $(this).attr('units');
        
        var out = "<td>"+title+"</td>";
        for (var m=0; m<12; m++)
        {
            out += "<td key='"+name+"."+m+"' dp="+dp+" units='"+units+"'></td>";
        }

        $(this).html("<tr>"+out+"</tr>");
    });
}

function UpdateUI(data)
{
    $("#openbem").trigger("UpdateUI");
    
    getkeys('data',data);
    
    for (z in keys)
    {
        var value = keys[z];
        var target = $("[key='"+z+"']");
        
        var dp = 1*target.attr('dp');
        var units = target.attr('units');
        if (!isNaN(dp)) value = (1*value).toFixed(dp);
        
        if (units!=undefined) value += ""+units;
        
        if (target.is('span')) target.html(value);
        if (target.is('div')) target.html(value);
        if (target.is('td')) target.html(value);
        if (target.is('th')) target.html(value);
        if (target.is('input[type=text]')) target.val(value);
        if (target.is('input[type=checkbox]')) target.prop('checked', value);
        if (target.is('select')) target.val(value);
    }
}

function getkeys(key,val)
{   
    switch (typeof val) {
        case "object":
            for (subkey in val) getkeys(key+"."+subkey,val[subkey]);
            break;
        case "string":
            keys[key] = val;
            break;
        case "number":
            keys[key] = val;
            break;
        case "boolean":
            keys[key] = val;
            break;
    }
}

function getuikeys()
{
    var uikeys = [];
    $("[key]").each(function(){
        uikeys.push($(this).attr('key'));
    });
    return uikeys;
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
