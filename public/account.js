function deleteUserAccount($accountId) {
    var result = confirm("Are you sure you want to delete this account?");
    if (result) {
        const Http = new XMLHttpRequest();
        Http.onreadystatechange = function () {
            if (Http.readyState === XMLHttpRequest.DONE) {
                location.reload();
            }
        }
        const url = '/account/' + $accountId + '/user';
        Http.open("DELETE", url);
        Http.send();
    }
}
function activateUser(userId){
    var result = confirm("Are you sure you want to activate this account?");
    if (result) {
        const Http = new XMLHttpRequest();
        Http.onreadystatechange = function () {
            if (Http.readyState === XMLHttpRequest.DONE) {
                let message;
                if(Http.response == 0){
                    message = 'Account is already active!';
                }else if(Http.response == 1){
                    message = 'User activated successfully!'
                    location.reload();
                } else {
                    message = Http.response;
                }
                alert(message);
            }
        }
        const url = '/account/status/' + userId + '/active';
        Http.open("PUT", url);
        Http.send();
    }
}
function deactivateUser(userId){
    var result = confirm("Are you sure you want to deactivate this account?");
    if (result) {
        const Http = new XMLHttpRequest();
        Http.onreadystatechange = function () {
            if (Http.readyState === XMLHttpRequest.DONE) {
                let message;
                if(Http.response == 0){
                    message = 'Account is already inactive!';
                }else if(Http.response == 1){
                    message = 'User inactivated successfully!'
                    location.reload();
                } else {
                    message = Http.response;
                }
                alert(message);
            }
        }
        const url = '/account/status/' + userId + '/inactive';
        Http.open("PUT", url);
        Http.send();
    }
}
