
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login");
    const registerForm = document.getElementById("register");
    const toRegister = document.getElementById("toRegister");
    const toLogin = document.getElementById("toLogin");
    const toForgot = document.getElementById("toForgot");
    const forgotForm = document.getElementById("forgot");
    const adminLoginForm = document.getElementById("adminLogin")
    
    const toLoginLinks = document.querySelectorAll(".to-login");


    toRegister.addEventListener("click", function () {
        loginForm.classList.add("form-hide");
        registerForm.classList.remove("form-hide");
    });

    toLogin.addEventListener("click", function () {
        registerForm.classList.add("form-hide");
        loginForm.classList.remove("form-hide");
        forgotForm.classList.add("form-hide");
    });

    toForgot.addEventListener("click", function () {
        loginForm.classList.add("form-hide");
        registerForm.classList.add("form-hide");
        forgotForm.classList.remove("form-hide");


    });


    toLoginLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            registerForm.classList.add("form-hide");
            forgotForm.classList.add("form-hide");
            loginForm.classList.remove("form-hide");
        });
    });

});






