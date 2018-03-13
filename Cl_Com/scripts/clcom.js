$.fn.getClcomData = function () {
    var data = [];
    var sendData={};
    var _this = this;
    var target="";
    if($(this).data("target")==="aw") target="_AW";
    if ($(this).data("type") === "cl") {
        inputs = $(this).find(":checkbox");
        inputs.each(function () {
            var input = $(this);
            var data_obj = {};
            data_obj["text"] = input.parent().next().find(".wrapper")[0].innerText;
            data_obj["checked"] = input.is(":checked");
            data_obj["no"] = input.data("no");
            data_obj["date"] = input.data("date");
            data.push(data_obj);
        });
        sendData = {
            action: "clcom-update",
            id: $(_this).parents(".table-row").find(".id").text().trim(),
            checklist: JSON.stringify(data),
            target: target
        }
    } else {
        inputs = $(this).find("label .text");
        inputs.each(function () {
            var input = $(this);
            var data_obj = {};
            data_obj["text"] = input.find(".wrapper")[0].innerText;
            data_obj["date"] = input.find(".date").text();
            data.push(data_obj);
        });
        sendData = {
            action: "clcom-update",
            id: $(_this).parents(".table-row").find(".id").text().trim(),
            comments: JSON.stringify(data),
            target: target
        }
    }
    console.log(sendData);
    var loading_button = $(this).find(".clcom-reload img");
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: sendData,
        dataType: "JSON",
        beforeSend: function () {
            loading_button.addClass("loading");
        },
        success: function (response) {
            console.log(response);
            loading_button.removeClass("loading");
            switch (response.type) {
                case "success":
                    break;
                case "error":
                    break;
                default:
                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });

};

$(".clcom-container").on("change", ":checkbox", function () {
    $(this).parents(".clcom-container").getClcomData();
    var parent = $(this).parents(".items");
    var items = parent.children(":not(.no-items)").detach();
    var checked = [];
    var unchecked = [];
    items.each(function () {
        if ($(this).find(":checkbox").is(":checked")) {
            checked.push(this);
        }
        else {
            unchecked.push(this);
        }
    });
    checked.sort(function (a, b) {
        if ($(b).find(":checkbox").data("no") > $(a).find(":checkbox").data("no")) {
            return 1;
        }
        return -1;
    });
    unchecked.sort(function (a, b) {
        if ($(b).find(":checkbox").data("no") > $(a).find(":checkbox").data("no")) {
            return 1;
        }
        return -1;
    });
    parent.children().remove();
    parent.append($(unchecked)).append($(checked));
});
$(".clcom-container").on("keypress", ".item .wrapper", function (e) {
   if(e.keyCode == 13 && !e.shiftKey && $(this).parents(".item").hasClass("editing")){
       e.preventDefault();
       e.stopPropagation();
       $(this).parents(".item").find(".clcom-check").click();
   }
});
$(".clcom-container").on("click", ".clcom-edit", function () {
    $(this).parents(".item").find("input").prop("disabled", true);
    $(this).parents(".item").find("label .text .wrapper").prop("contenteditable", true).focus();
    $(this).leftHide().prev().rightShow();
    $(this).parents(".clcom-container").find(".editing .clcom-check").click();
    var item = $(this).parents(".item").addClass("editing");

    var wrapper = $(this).parents(".item").find("label .text .wrapper");
    placeCaretAtEnd(wrapper[0]);

    var text = $(this).parents(".item").find("label .text:not(.com)").addClass("nobg");
    text.data("height", text.outerHeight());
    text.css({ height: (wrapper.outerHeight() + $(this).parents(".item").find("label .text .data").outerHeight()) });
    setTimeout(function () {
        text.addClass("noexpand heightauto");
    }, 100);

});
$(".clcom-container").on("click", ".clcom-check", function () {
    $(this).parents(".item").find("input").prop("disabled", false);
    $(this).parents(".item").find("label .text .wrapper").prop("contenteditable", false);
    var item = $(this).parents(".item");
    item.removeClass("editing");
    var wrapper = $(this).parents(".item").find("label .text:not(.com) .wrapper");
    var text = $(this).parents(".item").find("label .text:not(.com)");
    text.removeClass("noexpand heightauto nobg");
    setTimeout(function () {
        text.css({ height: text.data("height") });
    }, 0);
    $(this).leftHide().next().rightShow();
    $(this).parents(".clcom-container").getClcomData();
});
$(".clcom-container").on("click", ".clcom-remove", function () {
    var item = $(this).parents(".item");
    var parent = item.parent();
    $('.dpicker').remove();
    if ($(this).parents(".items").children().length === 2)
        $(this).parents(".items").children(".no-items").slideDown(100);
    item.css({ transition: "none" }).slideUp(100, function () {
        $(this).remove();
        $(parent).parents(".clcom-container").getClcomData();
    });
});
$(".clcom-container").on("click", ".clcom-add", function () {
    var com = "";
    var comclass = "";
    var cb = "<div class=\"cb\">\
                    <input data-date=\"\" data-no=\""+ (parseInt(no) + 1) + "\" type=\"checkbox\">\
                    <div></div>\
                </div>";
    if ($(this).parents(".clcom-container").hasClass("com-container")) {
        var d = new Date();
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        d = d.toISOString();
        d = d.match("^[^.]*")[0];
        d = d.replace('T', ' ');
        com = "<div class=\"date\">" + d + "</div>";
        comclass = "com";
        cb = "";
    }
    if ($(this).parents(".main-bar").next().children().length === 1)
        $(this).parents(".main-bar").next().children(".no-items").slideUp(100);
    var items = $(this).parents(".main-bar").next();
    var no = 0;
    items.find(":checkbox").each(function () {
        if ($(this).data("no") > no) no = $(this).data("no");
    });
    items.prepend("\
        <div class=\"item\" style=\"display:none;\">\
            <label>\
            "+ cb + "\
                <div class=\"text editable "+ comclass + "\">\
                    <div contenteditable class=\"wrapper\"></div>"+ com + "\
                </div>\
            </label>\
            <div class=\"buttons\">\
                <button class=\"clcom-check settings-button\" type=\"button\" title=\"Opcje\">\
                    <img src=\"Table/img/check.png\" alt=\"Zapisz\">\
                </button>\
                <button class=\"clcom-edit settings-button\" type=\"button\" title=\"Opcje\">\
                    <img src=\"Table/img/edit.png\" alt=\"Edytuj\">\
                </button>\
                <button class=\"clcom-remove settings-button\" type=\"button\" title=\"Opcje\">\
                    <img src=\"Table/img/remove.png\" alt=\"Usuń\">\
                </button>\
            </div>\
        </div>");

    items.find(".editing .clcom-check").click();
    items.find(".item:first-child").slideDown(100, function () {
        $(this).find(".clcom-edit").click();
        if (items.hasClass("clcom-scheduled-slider")) {
            $('.dpicker').remove();
            $(this).after("<input class='dpicker' type='text'>");
            $(this).next().datepick({
                dateFormat: 'yyyy-mm-dd',
                onSelect: function () {
                    $(this).prev().find('input').data("date", $(this).val());
                    $(this).prev().find('.wrapper').focus();
                    $(this).prev().prop("title", "Zaplanowane na: " + $(this).val());
                },
                onClose: function () {
                    $(this).remove();
                }
            }).focus().hide();
        }
    });
});
$(".clcom-scheduled").unbind("click").click(function () {
    $(this).find("img").toggleClass("rotated");
    var container = $(this).parents(".clcom-container");
    container.find(".clcom-scheduled-slider").slideToggle(100);
    $(this).parents(".clcom-container").find(".editing .clcom-check").click();
});