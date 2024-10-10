// form_validation.js

function validateFeedbackForm() {
    var name = document.forms["feedbackForm"]["name"].value;
    var feedback = document.forms["feedbackForm"]["feedback"].value;

    if (name == "" || feedback == "") {
        alert("All fields must be filled out");
        return false;
    }
    return true;
}

// Additional validation functions for other forms
function validateLoginForm() {
    var username = document.forms["login"]["username"].value;
    var password = document.forms["login"]["password"].value;

    if (username == "" || password == "") {
        alert("All fields must be filled out");
        return false;
    }
    return true;
}

function validateRegistrationForm() {
    var fname = document.forms["loginreg"]["f_name"].value;
    var lname = document.forms["loginreg"]["l_name"].value;
    var username = document.forms["loginreg"]["U_name"].value;
    var password = document.forms["loginreg"]["password"].value;
    var email = document.forms["loginreg"]["email"].value;

    if (fname == "" || lname == "" || username == "" || password == "" || email == "") {
        alert("All fields must be filled out");
        return false;
    }
    return true;
}
