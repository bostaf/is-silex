window.onload = function() {
    var menu = document.getElementById("main_menu");
    var menuButton = document.getElementById("menu_button");
    menuButton.onclick = function() {
        if (menu.style.display == "none" || menu.style.display == "") {
            menu.style.display = "block";
        } else {
            menu.style.display = "none";
        }
    }
};
