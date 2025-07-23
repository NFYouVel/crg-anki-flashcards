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
    $(".icon").click(function () {
        let box = $(".account-logout");
        if (box.is(":visible")) {
            box.slideUp(500);
        } else {
            box.css("display", "flex").hide().slideDown(500);
        }
    });
});
$(document).ready(function () {
    $(document).on("click", ".expand", function () {
        var $arrow = $(this);
        var $parentDeck = $arrow.closest(".deck");
        var $childDeck = $parentDeck.children(".deck");

        $childDeck.toggleClass("expanded");
        $arrow.toggleClass("rotated");
    });
});