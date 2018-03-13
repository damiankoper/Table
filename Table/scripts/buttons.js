function buttonToggle(self, status) {
    $.ajax({
        async: true,
        type: "POST",
        url: window.location.href,
        data: {
            action: "edit-status",
            id: $(self).parents(".cells").find(".id").text().trim(),
            status: status
        },
        dataType: "json",
        beforeSend: function () {
            $(self).parents(".settings-button-bar").siblings(".settings-button").find("img").addClass("loading");
            $(self).prop("disabled");
        },
        success: function (response) {
            console.log(response);
            $(self).prop("disabled", false);
            $(self).parents(".settings-button-bar").siblings(".settings-button").find("img").removeClass("loading");
            switch (response.type) {
                case "success":
                    $.notify("Status zmieniony pomyślnie", "success");
                    self.add(self.siblings(".status-button:not(.status-button-edit)")).leftHide();
                    self.siblings(".status-button-edit").rightShow();
                    break;
                case "error":
                    $.notify("Błąd po stronie serwera przy edycji", "error");
                    break;
                default:
                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });
}
$(".status-button").unbind('click').click(function () {
    $(this).siblings(".status-button").rightShow();
    $(this).leftHide();
});
$(".status-button-1").unbind('click').click(function () {
    buttonToggle($(this),"1");
});
$(".status-button-0").unbind('click').click(function () {
    buttonToggle($(this),"0");
});
$(".status-button--1").unbind('click').click(function () {
    buttonToggle($(this),"-1");
});