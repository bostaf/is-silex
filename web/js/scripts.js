$(document).ready(function() {
    $("time.timeago").timeago();
    $.timeago.settings.cutoff = 1000*60*60*24*365;


    var menu = $("#main_menu");
    var menuButton = $("#menu_button");

    menuButton.click(function() {
        if (menu.not(":visible")) {
            menu.show();
        } else {
            menu.hide();
        }
    });

    var membersDiffButton = $("#get-members-diff-button");
    var membersDiffSelectFirstLog = $("#get-members-diff-select-first-log");
    var membersDiffSelectSecondLog = $("#get-members-diff-select-second-log");

    membersDiffSelectFirstLog.change(function() {
        var url = membersDiffButton.attr("main_url");
        membersDiffButton.attr("onclick", "window.location.href = '" + url + "/" + membersDiffSelectFirstLog.val() + "/" + membersDiffSelectSecondLog.val() + "';");
    });
    membersDiffSelectSecondLog.change(function() {
        var url = membersDiffButton.attr("main_url");
        membersDiffButton.attr("onclick", "window.location.href = '" + url + "/" + membersDiffSelectFirstLog.val() + "/" + membersDiffSelectSecondLog.val() + "';");
    });

    var guestbookButton = $("#guestbook-form-toggler-button");
    var guestbookButtonContainer = $("#guestbook-form-toggler");
    var guestbookContainer = $("#guestbook-form-container");
    var guestbookReset = $("#guestbook-form-button-reset");
    guestbookButton.click(function() {
        if (guestbookContainer.not(":visible")) {
            guestbookContainer.show();
            guestbookButtonContainer.hide();
        }
        return false;
    });
    guestbookReset.click(function() {
        guestbookContainer.hide();
        guestbookButtonContainer.show();
    });
});