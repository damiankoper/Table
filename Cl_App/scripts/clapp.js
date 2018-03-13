function clapp_handler() {
    var items = $(this).parents(".items");
    var cb = items.find(":checkbox");
    var progress = [];
    cb.each(function () {
        progress.push({
            id: $(this).prop("name"),
            checked: $(this).is(":checked"),
            text: $(this).parents("label").find("textarea").val()
        });
    });

    var sendData = {
        action: "clapp-update",
        progress: JSON.stringify(progress),
        id: $(this).parents(".table-row").find(".id").text().trim()
    };
    var loading_button = $(this).parents(".clapp-container").find(".clcom-reload img");
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
}
$(".clapp-container :checkbox").on("change", clapp_handler);
$(".clapp-container textarea").on("focusout", clapp_handler);