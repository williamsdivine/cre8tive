"use strict";

$(document).ready(function() {
    const signup = (e) => {
        e.preventDefault();
        const url = "http://localhost/cre8tive/chidalu/routes/api.php?endpoint=signup";
        const formData = new FormData(signupForm);
        console.log(formData);
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            dataType: "json",
            success: (response) => {
                console.log(response);
                if (response.statuscode == 200) {
                    alert(response.status);
                } else {
                    alert(response.status);
                }
            },
            error: (err) => {
                console.log(err);
            }
        });
    };

    $("#signupBtn").on("click", (e) => {
        signup(e);
    });
});