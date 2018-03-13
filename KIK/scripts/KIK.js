$(".kik-select").autocomplete();
$(".edit-button-kik").unbind('click').click(function () {

    $(this).siblings(".edit-button-check").rightShow();
    $(this).leftHide(100);

    var kik = $(this).parents(".slidable-div");
    kik.find(".kik-editable").each(function () {
        $(this).data("text", $(this).text());
        $(this).prop("contenteditable", true).addClass("editing");
    });
    kik.find(".kik-select").next().show();
    kik.find(".kik-select-info").hide();

});
$(".edit-button-exit-kik").unbind('click').click(function () {

    $(this).parent().children(".edit-button-check").leftHide();
    $(this).siblings(".edit-button-kik").rightShow(100);

    var kik = $(this).parents(".slidable-div");
    kik.find(".kik-editable").each(function () {
        $(this).text($(this).data("text"));
        $(this).prop("contenteditable", false).removeClass("editing");
    });
    kik.find(".kik-select").next().hide();
    kik.find(".kik-select-info").show();
});
$(".edit-button-submit-kik").unbind('click').click(function () {

    var kik = $(this).parents(".slidable-div");
    var kik_main = kik.find(".kik-main-kik");
    var kik_info = kik.find(".kik-main-info");
    var content = {};
    kik_main.find(".kik-editable").each(function () {
        content[$(this).data("org")] = $(this).text();
    });
    kik_info.find(".kik-editable").each(function () {
        content[$(this).data("org")] = $(this).text();
    });
    var sendData = {
        content: JSON.stringify(content),
        connected_with: kik_info.find(".connectedWith .to-send").val(),
        user_id: kik_info.find(".userID .to-send").val(),
        action: "kik-update",
        id: $(this).parents(".table-row").find(".id").text().trim()
    };


    var loading_button = kik_info.find(".reload-button-kik img");
    var self = this;
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
                    $.notify("Zmiany zapisano pomyślnie", "success");
                    $(self).parent().children(".edit-button-check").leftHide();
                    $(self).siblings(".edit-button-kik").rightShow(100);
                    $(self).parents(".table-row").find(".cells .id-użytkownika .wrapper").text(sendData.connected_with);
                    var kik = $(self).parents(".slidable-div");
                    kik.find(".kik-editable").prop("contenteditable", false).removeClass("editing");
                    kik.find(".kik-select-info").each(function () { $(this).text($(this).next().find(".visible").val()); });
                    kik.find(".kik-select").next().hide();
                    kik.find(".kik-select-info").show();

                    break;
                case "error":
                    break;
                default:
                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });
    console.log(sendData);
});

$(".zap-cb").unbind('change').change(function (e) {
    if ($(this).is(":checked")) {
        $(this).parent().nextAll(".zap-input").rightShow();
        $(this).next().text("Zatwierdź");
    }
    else {
      
        var kik = $(this).parents(".slidable-div");
        var kik_main = kik.find(".kik-main-kik");
        var kik_info = kik.find(".kik-main-info");
        var loading_button = kik_info.find(".reload-button-kik img");
        var self = this;
        var sendData = {
            action: "kik-split",
            id: $(this).parents(".table-row").find(".id").text().trim(),
            user_id: $(this).parents(".table-row").find(".id-użytkownika").text().trim(),
            amount: parseFloat($(this).parent().nextAll(".zap-input").val().replace(',','.'))
        };
        console.log(sendData);
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: sendData,
            dataType: "JSON",
            beforeSend: function () {
                loading_button.addClass("loading");
                $(self).prop("disabled", true);
            },
            success: function (response) {
                console.log(response);
                loading_button.removeClass("loading");
                switch (response.type) {
                    case "success":
                        $.notify("Zmiany zapisano pomyślnie", "success");
                        $(self).next().text("TAK");
                        $(self).parent().next().leftHide();
                        break;
                    case "error":
                    default:
                        $(self).prop("disabled", false).prop("checked",true);
                        $.notify("Nieznany błąd", "error");
                        break;
                }
            }
        });
    }
});