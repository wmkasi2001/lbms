// Function to validate the registration form
function validateRegistrationForm() {
    var f_name = document.forms["loginreg"]["f_name"].value;
    var l_name = document.forms["loginreg"]["l_name"].value;
    var ID_No = document.forms["loginreg"]["u_name"].value;
    var password = document.forms["loginreg"]["password"].value;
    var email = document.forms["loginreg"]["email"].value;

    if (f_name == "" || l_name == "" || ID_No == "" || password == "" || email == "") {
        alert("All fields must be filled out");
        return false;
    }

    if (!validateEmail(email)) {
        alert("Invalid email format");
        return false;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters long");
        return false;
    }

    return true;
}

// Function to validate the login form
function validateLoginForm() {
    var ID_No = document.forms["login"]["u_name"].value;
    var password = document.forms["login"]["password"].value;

    if (U_name == "" || password == "") {
        alert("All fields must be filled out");
        return false;
    }

    return true;
}

// Helper function to validate email format
function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(email);
}
