window.onload = function() {

    var menu = document.getElementById("main_menu");
    var menuButton = document.getElementById("menu_button");

    menuButton.onclick = function() {
        if (menu.style.display == "none" || menu.style.display == "") {
            menu.style.display = "block";
        } else {
            menu.style.display = "none";
        }
    };

    var membersDiffButton = document.getElementById("get-members-diff-button");
    var membersDiffSelectFirstLog = document.getElementById("get-members-diff-select-first-log");
    var membersDiffSelectSecondLog = document.getElementById("get-members-diff-select-second-log");

    function getSelectValue(e) {
        return e.options[e.selectedIndex].value;
    }

    if (typeof membersDiffSelectFirstLog == undefined) membersDiffSelectFirstLog.onchange = function() {
        var url = membersDiffButton.getAttribute("main_url");
        membersDiffButton.setAttribute("onclick", "window.location.href = '" + url + "/" + getSelectValue(membersDiffSelectFirstLog) + "/" + getSelectValue(membersDiffSelectSecondLog) + "';");
    };
    if (typeof membersDiffSelectSecondLog == undefined) membersDiffSelectSecondLog.onchange = function() {
        var url = membersDiffButton.getAttribute("main_url");
        membersDiffButton.setAttribute("onclick", "window.location.href = '" + url + "/" + getSelectValue(membersDiffSelectFirstLog) + "/" + getSelectValue(membersDiffSelectSecondLog) + "';");
    };

    var guestbookButton = document.getElementById("guestbook-form-toggler-button");
    var guestbookButtonContainer = document.getElementById("guestbook-form-toggler");
    var guestbookContainer = document.getElementById("guestbook-form-container");
    var guestbookReset = document.getElementById("guestbook-form-button-reset");
    guestbookButton.onclick = function() {
        if (guestbookContainer.style.display == "none" || guestbookContainer.style.display == "") {
            guestbookContainer.style.display = "block";
            guestbookButtonContainer.style.display = "none";
        }
        return false;
    };
    guestbookReset.onclick = function() {
        guestbookContainer.style.display = "none";
        guestbookButtonContainer.style.display = "block";
    }
};
