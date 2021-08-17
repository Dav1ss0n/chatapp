document.addEventListener("DOMContentLoaded", function() {
    userStatusChanger("Onlline");
})
window.onbeforeunload = function() {
    userStatusChanger("Offline");
    return true;
}

function userStatusChanger(status) {
    let xml = new XMLHttpRequest();
    xml.open("POST", "/chat proto/system/functions/statuser.php", true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("status="+status);
}