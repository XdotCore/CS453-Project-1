// handles setting up the request reciever
window.onRecievedFromRequest = function (request, action) {
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                action();
            } else if (request.status == 404) {
                alert("Server was not found: data returned is :\n"+request.responseText);
            }
        }
    }
}