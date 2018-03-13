function setEndOfContenteditable(contentEditableElement)
{
    var range, selection;
    if (document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
    {
        range = document.createRange();//Create a range (a range is a like the selection but invisible)
        range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        selection = window.getSelection();//get the selection object (allows you to change selection)
        selection.removeAllRanges();//remove any selections already made
        selection.addRange(range);//make the range you have just created the visible selection
    } else if (document.selection)//IE 8 and lower
    {
        range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
        range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        range.select();//Select the range (make it the visible selection
    }
}

function maxKey(objects) {
    var max = -1;
    for (var i = 0; i < objects.length; i++) {

        if (max === null) {
            max = objects[i].no;
            continue;
        }
        if (parseInt(max) < parseInt(objects[i].no)) {
            max = objects[i].no;
        }
    }
    return parseInt(max);
}

var ajax;
function frameUpdate(it) {
    var cl, com, id, table_id, col_id, ifcl, ifcom, ajax, reversed, cl_col, cl_title, cl_sh_slided;
    id = it.data('id');
    table_id = it.data('table-id');
    col_id = it.data('col-id');
    ifcl = it.data('cl');
    ifcom = it.data('com');
    cl_col = it.data('cl_col');
    cl_title = it.data('cl_title');
    cl_sh_slided = it.find('.cl_scheduled').is(':visible') ? "true" : "";
    var reversed = it.data('reversed');
    cl = [];
    var i = 0;
    $(it).find("a").contents().unwrap();
    var selector = it.children('.cl').find('input:not(.removed):not(.dpicker)').get();
    if (reversed)
        selector.reverse();
    $(selector).each(function () {
        var txt = $(this).parent().next().html();
        if ($(this).prop('name') != '') {
            var JSONcl = {};
            JSONcl.checked = $(this).is(":checked");
            JSONcl.no = $(this).prop('name');
            JSONcl.date = $(this).data("date");
            JSONcl.text = txt;
            cl.push(JSONcl);
        }
    });
    //console.log(cl);
    $(selector).each(function () {
        var txt = $(this).parent().next().html();
        if ($(this).prop('name') == '') {
            var JSONcl = {};
            JSONcl.checked = $(this).is(":checked");
            JSONcl.date = $(this).data("date");
            JSONcl.no = maxKey(cl) + 1;
            JSONcl.text = txt;
            cl.push(JSONcl);
        }
    });
    cl = JSON.stringify(cl);
    console.log(cl);
    com = [];
    i = 0;
    selector = it.children('.com').find('label:not(.removed) > div:first-child').get();
    if (reversed)
        selector.reverse();
    $(selector).each(function () {
        var txt = $(this).html();
        var date = $(this).next().html();
        var JSONcom = {};
        JSONcom.text = txt;
        JSONcom.date = date;
        com.push(JSONcom);
        i++;
    });
    com = JSON.stringify(com);
    it.parent().children('.loading_text').removeClass('zeroopacity');
    it.parent().find('.refresh').addClass('refresh-anim');
    if (ajax != null)
        ajax.abort();
    var object = {"reversed": reversed, "ifcom": ifcom, "ifcl": ifcl, "id": id, "table_id": table_id, "id_column": col_id, "cl": cl, "cl_sh_slided": cl_sh_slided, "cl_col": cl_col, "cl_title": cl_title, "com": com};
    console.log(object);
    ajax = $.post("admin_php_old/ajax_checklist.php", object, function (data) {
        it.parent().replaceWith(linkify(data));
        $(".app-checklist input[type='checkbox'], .cl input[type='checkbox']").each(checkCB);
        $('.subplan label').each(function () {
            $(this).parent().removeClass('slidable');
            if ($(this).innerHeight() < $(this).children('.cl_com_text').innerHeight()) {
                $(this).parent().addClass('slidable');
            }
        });
    }, "html");

}
function frameRefresh(it) {
    var cl, com, id, table_id, col_id, ifcl, ifcom, ajax, reversed, cl_col, cl_title, cl_sh_slided;
    id = it.data('id');
    table_id = it.data('table-id');
    col_id = it.data('col-id');
    ifcl = it.data('cl');
    ifcom = it.data('com');
    cl_col = it.data('cl_col');
    cl_title = it.data('cl_title');
    cl_sh_slided = it.find('.cl_scheduled').is(':visible') ? "true" : "";
    it.parent().find('.refresh').addClass('refresh-anim');
    var reversed = it.data('reversed');
    it.parent().children('.loading_text').removeClass('zeroopacity');
    if (ajax != null)
        ajax.abort();
    //console.log({"reversed":reversed,"ifcom": ifcom, "ifcl": ifcl, "id": id, "table_id": table_id, "id_column": col_id, "cl": cl, "com": com});
    ajax = $.post("admin_php_old/ajax_checklist.php", {"reversed": reversed, "ifcom": ifcom, "ifcl": ifcl, "id": id, "table_id": table_id, "id_column": col_id, "cl": "false", "cl_sh_slided": cl_sh_slided, "cl_col": cl_col, "cl_title": cl_title, "com": "false"}, function (data) {

        it.parent().replaceWith(data);
        $(".app-checklist input[type='checkbox'], .cl input[type='checkbox']").each(checkCB);
        $('.subplan label').each(function () {
            $(this).parent().removeClass('slidable');
            if ($(this).innerHeight() < $(this).children('.cl_com_text').innerHeight()) {
                $(this).parent().addClass('slidable');
            }
        });
    }, "html");

}
function clEdit(it) {
    it.hide();
    it.next().show();
    it.siblings('label').children('div:last-child').css({backgroundColor: 'rgba(200,255,200,1)'});
    it.siblings('label').prop('for', '');
    var input = it.siblings('label').children('div:last-child');
    input.prop("contenteditable", true).focus();
    setEndOfContenteditable(input[0]);
}
function comEdit(it) {
    it.hide();
    it.next().show();
    it.siblings('label').children('div:first-child').css({backgroundColor: 'rgba(200,255,200,1)'});
    it.siblings('label').prop('for', '');
    var input = it.siblings('label').children('div:nth-last-child(2)');
    input.prop("contenteditable", true).focus();
    setEndOfContenteditable(input[0]);
}
function clRemove(it) {
    var context = it.parents('.cl_com_main');
    it.parent().slideUp(100).find('input, label').addClass('removed');
    frameUpdate(context);
}
function clSubmit(it) {
    frameUpdate(it.parents('.cl_com_main'));
}
function clAdd(it) {

    if (it.parents('.cl_com_main').data('reversed') == true)
        it = it.parent().children().filter('div:first-of-type');
    var rand = Math.random();
    it.nextAll('.filler').slideUp(100);
    it.after("<div style='display:none;'>\
                        <label style='background-color:rgba(0,255,0,0.1);' for=''>\
                            <div>\
                                <input id='' type='checkbox'/>\
                            </div>\
                            <div class='cl_com_text'></div>\
                        </label>\
                          <button title='Edytuj' onclick=\"clEdit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/edit.png'/></button>\
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 '><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>\
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>\
                    </div>");
    console.log(it.parent().hasClass('cl_scheduled'));
    if (it.parent().hasClass('cl_scheduled')) {
        var scheduled = true;
        $('.dpicker').remove();
        it.next().after("<input class='dpicker' type='text'>");
        it.next().next().datepick({dateFormat: 'yyyy-mm-dd',
            onSelect: function () {
                $(this).prev().find('input').data("date", $(this).val());
            },
            onClose: function () {
                $(this).remove();
            }
        }).focus().hide();
    }
    it.next().slideDown(100).children('label').children('div:last-child').prop("contenteditable", true).focus().val(it.prev().children('label').children('div:last-child').prop("contenteditable", true).val());
}
function comAdd(it) {
    var d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    d = d.toISOString();
    d = d.match("^[^.]*")[0];
    d = d.replace('T', ' ');
    it.nextAll('.filler').slideUp(100);
    if (it.parents('.cl_com_main').data('reversed') == true)
        it = it.parent().children().filter('div:first-of-type');
    var rand = Math.random();
    it.after("<div style='display:none;'>\
                        <label style='background-color:rgba(0,255,0,0.1);' for=''>\
                        <div  class='cl_com_text'></div>\
                        <div class='com_date'>" + d + "</div>\
</label>\
                         <button title='Edytuj' onclick=\"comEdit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/edit.png'/></button>\
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 '><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>\
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>\
                    </div>");
    var input = it.next().slideDown(100).children('label').children('div:first-child').prop("contenteditable", true).focus();

}
//RELATYWNE TO TYLKO ADD MOŻNA ZMIENIĆ HIHI