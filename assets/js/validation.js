$(document).ready(function() {
    
    // ===== რეგისტრაციის ვალიდაცია =====
    $("#registerForm").on("submit", function(event) {
        let email = $("#email").val().trim();
        let password = $("#password").val();
        let confirmPassword = $("#confirm_password").val();
        let errorBox = $("#js-error");
        
        errorBox.hide().text("");

        if (email === "" || password === "" || confirmPassword === "") {
            event.preventDefault();
            errorBox.text("შეცდომა: ყველა ველი სავალდებულოა!").fadeIn();
            return false;
        }

        let emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        if (!emailPattern.test(email)) {
            event.preventDefault();
            errorBox.text("შეცდომა: გთხოვთ შეიყვანოთ სწორი ელ.ფოსტის ფორმატი!").fadeIn();
            return false;
        }

        if (password.length < 6) {
            event.preventDefault();
            errorBox.text("შეცდომა: პაროლი უნდა შედგებოდეს მინიმუმ 6 სიმბოლოსგან!").fadeIn();
            return false;
        }

        if (password !== confirmPassword) {
            event.preventDefault();
            errorBox.text("შეცდომა: პაროლები არ ემთხვევა ერთმანეთს!").fadeIn();
            return false;
        }
    });


    // ===== ავტორიზაციის (Login) ვალიდაცია =====
    $("#loginForm").on("submit", function(event) {
        let email = $("#login_email").val().trim();
        let password = $("#login_password").val();
        let errorBox = $("#js-login-error");

        errorBox.hide().text("");

        // ცარიელი ველების შემოწმება შესვლისას
        if (email === "" || password === "") {
            event.preventDefault(); // ვაჩერებთ გვერდის გადატვირთვას
            errorBox.text("შეცდომა: გთხოვთ შეავსოთ ორივე ველი!").fadeIn();
            return false;
        }
    });

});