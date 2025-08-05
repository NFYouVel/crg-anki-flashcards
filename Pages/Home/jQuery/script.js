$(document).ready(function () {
    $(".title-to-review").click(function () {
        let box = $(".subdeck");
        if (box.is(":visible")) {
            box.slideUp(500);
        } else {
            box.css("display", "block").hide().slideDown(500);
        }
        
    });
    // $(".title-to-review-second").click(function () {
    //     $(this).next().children().slideToggle(400);
    //     let el = $(this);
    //     el.addClass("clicked");

    //     setTimeout(function () {
    //         el.removeClass("clicked");
    //     }, 300);
    // });
    $(".icon").click(function () {
        let box = $(".account-logout");
        if (box.is(":visible")) {
            box.slideUp(500);
        } else {
            box.css("display", "flex").hide().slideDown(500);
        }
    });

    $(".click-delete").click(function () {
        let box = $(".wrapper-delete");
        if (box.is(":visible")) {
            box.fadeOut(150);
        } else {
            box.css("display", "flex").hide().fadeIn(150);
        }
    });
    $(".click-reset").click(function () {
        let box = $(".wrapper-reset");
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
    $(".button-cancel").click(function () {
        let box = $(".wrapper-reset");
        box.fadeOut(150);
    });
    $(".button-update").click(function () {
        $(location).attr('href', 'setting.php');
    });
    $(".contain > ul").hide();

    // Tambahin cursor pointer ke title deck
    $(".title-to-review-second").css("cursor", "pointer");
  
    // Toggle setiap klik title deck
    $(".title-to-review-second").click(function (e) {
      e.stopPropagation(); // biar ga bubbling
  
      const subdeck = $(this).parent().find("ul").first();
  
      // Animate toggle
      if (subdeck.length > 0) {
        subdeck.slideToggle(200);
      }
    });
    $(".title-to-review-second").each(function () {
        // Hitung level berdasarkan berapa kali dia nested dalam <ul>
        let level = $(this).parents("ul").length - 1;

        // Makin dalam, makin besar margin kiri (20px per level misalnya)
        let indent = level * 0;

        // Apply indent
        $(this).css("padding-left", indent + "px");
    });
});