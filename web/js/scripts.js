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

    membersDiffSelectFirstLog.onchange = function() {
        var url = membersDiffButton.getAttribute("main_url");
        membersDiffButton.setAttribute("onclick", "window.location.href = '" + url + "/" + getSelectValue(membersDiffSelectFirstLog) + "/" + getSelectValue(membersDiffSelectSecondLog) + "';");
    };
    membersDiffSelectSecondLog.onchange = function() {
        var url = membersDiffButton.getAttribute("main_url");
        membersDiffButton.setAttribute("onclick", "window.location.href = '" + url + "/" + getSelectValue(membersDiffSelectFirstLog) + "/" + getSelectValue(membersDiffSelectSecondLog) + "';");
    };
};
