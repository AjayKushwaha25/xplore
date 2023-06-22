// reedeem Now
function reedeem() {
    var user = document.getElementById("username").value;
    var phone = document.getElementById("phone").value;

    if (user == "") {
        document.getElementById("uname").innerHTML = "Please enter your name";
        //    return false;
    }
    if (phone == "") {
        document.getElementById("pnumber").innerHTML =
            "Please enter your phone number";
        //    return false;
    }

    var x = document.getElementById("toast");
    x.className = "show";
    setTimeout(function () {
        x.className = x.className.replace("show", "");
    }, 10000);
}

// Sign up
function login() {
    var user = document.getElementById("username").value;
    var phone = document.getElementById("phone").value;
    var whatsapp = document.getElementById("whatsapp").value;
    var upi = document.getElementById("upi").value;

    if (user == "") {
        document.getElementById("uname").innerHTML = "Please enter your name";
        //    return false;
    }
    if (phone == "") {
        document.getElementById("pnumber").innerHTML =
            "Please enter your phone number";
        //    return false;
    }

    if (whatsapp == "") {
        document.getElementById("whatsno").innerHTML =
            "Please enter your whatsapp number";
        // return false;
    }

    if (upi == "") {
        document.getElementById("upiid").innerHTML = "Please enter your UPI ID";
        // return false;
    }
}
