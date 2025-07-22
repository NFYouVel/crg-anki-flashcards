$(document).ready(function () {
    $(".title-to-review").click(function () {
        let box = $(".contain-ci");
        if (box.is(":visible")) {
            box.slideUp(500);
        } else {
            box.css("display", "flex").hide().slideDown(500);
        }
        $(this).find(".arrow").toggleClass("rotate");
    });
    $(".click").on("click", function () {
        $(".wrapper-add").css("display", "flex").hide().fadeIn()
    });
    $(".button-cancel").on("click", function () {
        $(".wrapper-add").fadeOut();
    });

});