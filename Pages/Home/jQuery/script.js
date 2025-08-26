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
    // $(".plus").on("click", function (e) {
    //     // e.stopPropagation(); // Biar nggak kena bubbling ke parent

    //     const $this = $(this);
    //     const $ul = $this.closest(".contain").children("ul");

    //     // Toggle tampilannya
    //     $ul.slideToggle(200);

    //     // Ganti tanda plus/minus
    //     if ($this.text().trim() === "+") {
    //       $this.text("−");
    //     } else {
    //       $this.text("+");
    //     }
    //   });

    $(".title-to-review-second").each(function () {
        // Hitung level berdasarkan berapa kali dia nested dalam <ul>
        let level = $(this).parents("ul").length - 1;

        // Makin dalam, makin besar margin kiri (20px per level misalnya)
        let indent = level * 0;

        // Apply indent
        $(this).css("padding-left", indent + "px");
    });
    $('.line').width($('.title-to-review').width());

    $("#click-show").on("click", function () {
        $(this).css("z-index", "0");
        $('span.pinyin').css('display', 'flex');
        $('span.word-class').css('display', 'flex');
        $('.vocab-card').children('table').css('display', 'flex');
        $('.sentence').css('display', 'flex')
        $('.vocab-card').css('border-bottom', '1px solid rgb(174, 174, 174)')
    });

    $(document).on("click", ".text-report", function () {
        $(".wrapper-report").css("display", "flex");
    });
    $(".button-cancel").click(function () {
        let box = $(".wrapper-report");
        box.fadeOut(150);
    });

    // New Fitur
    let openDecks = JSON.parse(localStorage.getItem("openDecks")) || [];

    // Buka ulang deck yang ada di list
    openDecks.forEach(function (id) {
        let $contain = $(`.contain[data-id='${id}']`);
        $contain.children("ul").show();       // buka subdeck
        $contain.find(".plus").text("−");     // ubah tanda jadi minus
    });

    $(".plus").on("click", function () {
        let $this = $(this);
        let $contain = $this.closest(".contain");
        let $ul = $contain.children("ul");
        let deckId = $contain.data("id");

        // Toggle show/hide
        if ($ul.is(":visible")) {
            $ul.slideUp(200);
            $this.text("+");
            openDecks = openDecks.filter(x => x !== deckId);
        } else {
            $ul.slideDown(200);
            $this.text("−");
            if (!openDecks.includes(deckId)) {
                openDecks.push(deckId);
            }
        }

        // Update localStorage
        localStorage.setItem("openDecks", JSON.stringify(openDecks));
    });
});