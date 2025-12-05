document.addEventListener("turbo:load", loadCredentialData);

function loadCredentialData() {
    let StripeCheckbox = $("#stripeEnable").is(":checked");
    if (StripeCheckbox) {
        $(".stripe-div").removeClass("d-none");
    } else {
        $(".stripe-div").addClass("d-none");
    }

    // let PaypalCheckbox = $("#paypalEnable").is(":checked");
    // if (PaypalCheckbox) {
    //     $(".paypal-div").removeClass("d-none");
    // } else {
    //     $(".paypal-div").addClass("d-none");
    // }

    let razorpayCheckbox = $("#razorpayEnable").is(":checked");
    if (razorpayCheckbox) {
        $(".razorpay-div").removeClass("d-none");
    } else {
        $(".razorpay-div").addClass("d-none");
    }

    // let paytmCheckbox = $("#paytmEnable").is(":checked");
    // if (paytmCheckbox) {
    //     $(".paytm-div").removeClass("d-none");
    // } else {
    //     $(".paytm-div").addClass("d-none");
    // }

    // let paystackCheckbox = $("#paystackEnable").is(":checked");
    // if (paystackCheckbox) {
    //     $(".paystack-div").removeClass("d-none");
    // } else {
    //     $(".paystack-div").addClass("d-none");
    // }
}

listen("change", "#stripeEnable", function () {
    let StripeCheckbox = $("#stripeEnable").is(":checked");
    if (StripeCheckbox) {
        $(".stripe-div").removeClass("d-none");
    } else {
        $(".stripe-div").addClass("d-none");
    }
});
// listen("change", "#paypalEnable", function () {
//     let PaypalCheckbox = $("#paypalEnable").is(":checked");
//     if (PaypalCheckbox) {
//         $(".paypal-div").removeClass("d-none");
//     } else {
//         $(".paypal-div").addClass("d-none");
//     }
// });
listen("change", "#razorpayEnable", function () {
    let razorpayCheckbox = $("#razorpayEnable").is(":checked");
    if (razorpayCheckbox) {
        $(".razorpay-div").removeClass("d-none");
    } else {
        $(".razorpay-div").addClass("d-none");
    }
});
// listen("change", "#paytmEnable", function () {
//     let paytmCheckbox = $("#paytmEnable").is(":checked");
//     if (paytmCheckbox) {
//         $(".paytm-div").removeClass("d-none");
//     } else {
//         $(".paytm-div").addClass("d-none");
//     }
// });
// listen("change", "#paystackEnable", function () {
//     let payStackCheckbox = $("#paystackEnable").is(":checked");
//     if (payStackCheckbox) {
//         $(".paystack-div").removeClass("d-none");
//     } else {
//         $(".paystack-div").addClass("d-none");
//     }
// });

listenSubmit("#UserCredentialsSettings", function (e) {
    e.preventDefault();
    let StripeCheckbox = $("#stripeEnable").is(":checked");
    // let PaypalCheckbox = $("#paypalEnable").is(":checked");
    let razorpayCheckbox = $("#razorpayEnable").is(":checked");
    // let paytmCheckbox = $("#paytmEnable").is(":checked");
    // let paystackCheckbox = $("#paystackEnable").is(":checked");

    if (StripeCheckbox && $("#stripeKey").val().trim() == "") {
        displayErrorMessage(Lang.get("js.stripe_key") +" "+ Lang.get("js.field_required"));
        return false;
    }
    if (StripeCheckbox && $("#stripeSecret").val().trim() == "") {
        displayErrorMessage(Lang.get("js.stripe_secret") +" "+ Lang.get("js.field_required"));
        return false;
    }
    // if (PaypalCheckbox && $("#paypalKey").val().trim() == "") {
    //     displayErrorMessage(Lang.get("js.paypal_client_id") +" "+ Lang.get("js.field_required"));
    //     return false;
    // }
    // if (PaypalCheckbox && $("#paypalSecret").val().trim() == "") {
    //     displayErrorMessage(Lang.get("js.paypal_secret") +" "+ Lang.get("js.field_required"));
    //     return false;
    // }
    // if (PaypalCheckbox && $("#paypalMode").val().trim() == "") {
    //     displayErrorMessage(Lang.get("js.paypal_mode") +" "+ Lang.get("js.field_required"));
    //     return false;
    // }
    if (razorpayCheckbox && $("#razorpayKey").val().trim() == "") {
        displayErrorMessage(Lang.get("js.razorpay_key") +" "+ Lang.get("js.field_required"));
        return false;
    }
    if (razorpayCheckbox && $("#razorpaySecret").val().trim() == "") {
        displayErrorMessage(Lang.get("js.razorpay_secret") +" "+ Lang.get("js.field_required"));
        return false;
    }
    // if (paytmCheckbox && $("#paytmMerchantId").val().trim() == "") {
    //     displayErrorMessage(Lang.get("messages.new_change.paytm_id"));
    //     return false;
    // }
    // if (paytmCheckbox && $("#paytmMerchantKey").val().trim() == "") {
    //     displayErrorMessage(Lang.get("messages.new_change.paytm_key"));
    //     return false;
    // }
    // if (paystackCheckbox && $("#paystackPublicKey").val().trim() == "") {
    //     displayErrorMessage(Lang.get("messages.new_change.paystack_key"));
    //     return false;
    // }
    // if (paystackCheckbox && $("#paystackSecretKey").val().trim() == "") {
    //     displayErrorMessage(Lang.get("messages.new_change.paystack_secret"));
    //     return false;
    // }

    $("#UserCredentialsSettings")[0].submit();
});
