"use strict";

$(document).ready(function() {
    const signup = (e) => {
        e.preventDefault();
        const url = "http://localhost/cre8tive/chidalu/routes/api.php?endpoint=signup";
        
        // Get form data
        const fullname = $('input[name="fullname"]').val();
        const email = $('input[name="email"]').val();
        
        // Validate form data
        if (!fullname || !email) {
            alert("Please fill in all fields");
            return;
        }
        
        const formData = {
            fullname: fullname,
            email: email
        };
        
        console.log("Sending data:", formData);
        
        $.ajax({
            url: url,
            method: "POST",
            data: JSON.stringify(formData),
            contentType: "application/json",
            dataType: "json",
            success: (response) => {
                console.log("Response:", response);
                if (response.statuscode == 200) {
                    alert(response.message || response.status);
                    // Redirect to OTP page
                    window.location.href = "./otp.html";
                } else {
                    alert(response.error || response.status);
                }
            },
            error: (err) => {
                console.log("Error:", err);
                alert("An error occurred. Please try again.");
            }
        });
    };

    $("#signupBtn").on("click", signup);
    
    // Also handle form submission
    $("#signupForm").on("submit", signup);
});