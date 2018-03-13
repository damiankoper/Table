$("#contact_if_1, #contact_if_2, #contact_if_3").change(function () {

    $("#old, #new").stop().slideUp(300);
    if ($(this).val() === "old") {
        $("#old").stop().slideDown(300);
    }
    else if ($(this).val() === "new"){
        $("#new").stop().slideDown(300);
    }
});
$("#find").autocomplete();