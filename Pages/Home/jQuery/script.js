$(document).ready(function () {
    $(".title-to-review").click(function () {
        let box = $(".subdeck");
        if (box.is(":visible")) {
            box.slideUp(500);
        } else {
            box.css("display", "block").hide().slideDown(500);
        }
        
    });
    $(".title-to-review-second").click(function () {
        $(this).next().children().slideToggle(400);
        let el = $(this);
        el.addClass("clicked");

        setTimeout(function () {
            el.removeClass("clicked");
        }, 300);
    });
    $(".icon").click(function () {
        let box = $(".account-logout");
        if (box.is(":visible")) {
            box.slideUp(500);
        } else {
            box.css("display", "flex").hide().slideDown(500);
        }
    });

    $(".click").click(function () {
        let box = $(".wrapper-delete");
        if (box.is(":visible")) {
            box.fadeOut(150);
        } else {
            box.css("display", "flex").hide().fadeIn(150);
        }
    });
    $(".button-cancel").click(function () {
        let box = $(".wrapper-delete");
        box.fadeOut(150);
    });
    $(".button-update").click(function () {
        $(location).attr('href', 'setting.php');
    });
});