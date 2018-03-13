jQuery.fn.autocomplete = function () {
    this.each(function () {
        var name = $(this).attr('name');
        var id = $(this).prop('id');
        var placeholder = $(this).data('placeholder');
        var options = [];
        $(this).children("option").each(function () {
            options.push({ value: $(this).val(), text: $(this).text() })
        });
        $(this).css({ display: "none" });
        $(this).after('<div class="autocomplete" id="' + id + '_autocomplete"></div>');
        var div_id = "#" + id + "_autocomplete";
        div_id = $(this).next();
        $(div_id).append('<input class="to-send" type="hidden" name="' + name + '" required>');
        $(div_id).append('<input class="visible" type="text" placeholder=\"' + placeholder + '\">');
        $(div_id).append('<div class="autocomplete-list"></div>');
        console.log(options);
        if($(this).find(":selected").length===1){
            $(div_id).find(".visible").val($(this).find(".selected").text());
            $(div_id).find(".to-send").val($(this).find(".selected").val());
        }

        $(div_id).children(".visible").on("keyup", function (e) {
            switch (e.keyCode) {
                case 13:case 40: case 38: case 39: case 37: case 27: break;
                default:
                    var value = $(this).val().replace(/[.?*+^$[\]\\(){}|-]/g, "\\$&");;
                    $(div_id).children(".autocomplete-list").children().remove();
                    if (value === "") {
                        for (i = 0; i < options.length; i++) {
                            $(div_id).children(".autocomplete-list").append("<div class=\"option\" data-value=\"" + options[i].value + "\" data-text=\"" + options[i].text + "\">" + options[i].text + "</div>");
                        }
                        $(div_id).children(".autocomplete-list").slideDown(100);
                        return;
                    }
                    var results = [];
                    for (i = 0; i < options.length; i++) {
                        var exp = new RegExp(value, "gi");
                        var indexes = [];
                        do {
                            var result = exp.exec(options[i].text);
                            if (result) indexes.push(result.index);
                        } while (result);
                        if (indexes.length !== 0) results.push({ object: options[i], index: indexes });
                    }
                    if (results.length !== 0) {
                        for (i = 0; i < results.length; i++) {
                            for (j = results[i].index.length - 1; j >= 0; j--) {
                                var position_end = results[i].index[j] + value.length;
                                var position_start = results[i].index[j];
                                var text = [results[i].object.text.slice(0, position_end), "</b>", results[i].object.text.slice(position_end)].join('');
                                text = [text.slice(0, position_start), "<b>", text.slice(position_start)].join('');
                            }
                            $(div_id).children(".autocomplete-list").append("<div class=\"option\" data-value=\"" + results[i].object.value + "\" data-text=\"" + results[i].object.text + "\">" + text + "</div>");
                        }
                        $(div_id).children(".autocomplete-list").slideDown(100);
                    }
                    break;
            }
        });

        $(div_id).children(".visible").on("focus", function (e) {
            $(div_id).children(".visible").data("selected", false);
            $(div_id).find(".visible").val("");
            $(div_id).find(".to-send").val("");
            $(div_id).children(".autocomplete-list").children().remove();
            for (i = 0; i < options.length; i++) {
                $(div_id).children(".autocomplete-list").append("<div class=\"option\" data-value=\"" + options[i].value + "\" data-text=\"" + options[i].text + "\">" + options[i].text + "</div>");
            }
            $(div_id).children(".autocomplete-list").slideDown(100);
        });

        $(div_id).children(".visible").on("keydown", function (e) {
            switch (e.keyCode) {
                case 40:
                    e.preventDefault();
                    var selected = $(div_id).find(".autocomplete-list div.autocomplete-list-hover");
                    console.log($(div_id).find(".autocomplete-list div"));
                    if ($(div_id).find(".autocomplete-list div").length === 1) {
                        $(div_id).find(".autocomplete-list div").eq(0).addClass("autocomplete-list-hover").scrollintoview({ duration: 100 });
                    }
                    else if (selected.length === 0 || selected.is(":last-child")) {
                        $(div_id).find(".autocomplete-list div").eq(0).addClass("autocomplete-list-hover").scrollintoview({ duration: 100 });
                        $(div_id).find(".autocomplete-list div").last().removeClass("autocomplete-list-hover");
                    }
                    else {
                        selected.removeClass("autocomplete-list-hover");
                        selected.next().addClass("autocomplete-list-hover").scrollintoview({ duration: 100 });
                    }

                    break;
                case 38:
                    e.preventDefault();
                    var selected = $(div_id).find(".autocomplete-list div.autocomplete-list-hover");
                    if ($(div_id).find(".autocomplete-list div").length === 1) {
                        $(div_id).find(".autocomplete-list div").eq(0).addClass("autocomplete-list-hover").scrollintoview({ duration: 100 });
                    }
                    else if (selected.length === 0 || selected.is(":first-child")) {
                        $(div_id).find(".autocomplete-list div").last().addClass("autocomplete-list-hover").scrollintoview({ duration: 100 });;
                        $(div_id).find(".autocomplete-list div").eq(0).removeClass("autocomplete-list-hover");
                    }
                    else {
                        selected.removeClass("autocomplete-list-hover");
                        selected.prev().addClass("autocomplete-list-hover").scrollintoview({ duration: 100 });

                    }

                    break;
                case 27:
                    e.preventDefault();
                    $(this).blur();
                    break;
                case 13:
                    e.preventDefault();
                    $(div_id).find(".autocomplete-list div.autocomplete-list-hover").click();
                    break;

            }
        });

        $(div_id).children(".visible").on("focusout", function (e) {
            if ($(div_id).children(".visible").data("selected") === false) {
                $(div_id).find(".visible").val("");
                $(div_id).find(".to-send").val("");
            }
            setTimeout(function () {
                $(div_id).children(".autocomplete-list").slideUp(100);
            }, 100);
        });
        $(div_id).find(".autocomplete-list").on("click", ".option", function (e) {
            console.log(this);
            $(this).parents(".autocomplete").find(".visible").blur();
            $(this).parents(".autocomplete").find(".visible").val($(this).data("text"));
            $(this).parents(".autocomplete").find(".to-send").val($(this).data("value"));
            $(this).parents(".autocomplete").children(".autocomplete-list").slideUp(100);
        });
    });
}