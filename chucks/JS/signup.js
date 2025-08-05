"use strict";

$(document).ready(function() {
    const signup = (e) => {
        e.preventDefault()
        const url = "http://localhost/cre8tive/chidalu/routes/api.php?endpoint=signup"
        const formData = new FormData(signupForm);
        console.log(FormData) 
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            dataType: "json",
            success: (response) => {
                const json = JSON.parse(response);
                console.log(response);
                if (json.statuscode == 200) {
                    alert(json.status);
                } else {
                    alert(json.status);
                }
            },
            error: (err) => {
                console.log(err);
            }
        })
    }

     $("#signupBtn").on("click", (e) =>{
        
        // signup(e);
    })
});