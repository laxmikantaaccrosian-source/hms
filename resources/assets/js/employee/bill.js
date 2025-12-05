"use strict";
document.addEventListener("turbo:load", loadBillData);

function loadBillData() {
    Lang.setLocale($(".userCurrentLanguage").val());
}

listenChange("#paymentModeType", function () {
    swal({
        title: Lang.get("js.are_you_sure"),
        text: Lang.get("js.complete_this_payment"),
        icon: "warning",
        buttons: {
            confirm: $(".yesVariable").val(),
            cancel: $(".noVariable").val() + ", " + $(".cancelVariable").val(),
        },
    }).then((result) => {
        if (result) {
            let id = $(this).data("id");
            let payment_type = $(this).val();

            $.ajax({
                url: route("manual-billing-payments.store"),
                type: "POST",
                data: { id: id, payment_type: payment_type },
                success: function (data) {
                    if (data.data == null) {
                        displaySuccessMessage(data.message);
                        livewire.emit("refresh");
                    }else{
                        // Stripe payment
                        if (data.data.payment_type == "0") {
                            let sessionId = data.data[0].sessionId;
                            stripe.redirectToCheckout({
                                sessionId: sessionId,
                            })
                            .then(mainResult => manageAjaxErrors(mainResult));
                        }
                        // Razorpay payment
                        if(data.data.payment_type == "2"){
                            let billId = data.data.bill_id;
                            $.ajax({
                                type: 'POST',
                                url: route('razorpay.init'),
                                data: {'bill_id': billId},
                                success: function (result) {
                                    console.log(result);
                                    if (result.success) {
                                        let {id, amount} = result.data
                                        options.amount = amount
                                        options.order_id = id

                                        let rzp = new Razorpay(options)
                                        rzp.open()
                                    }
                                },
                                error: function (error){
                                    displayErrorMessage(error.responseJSON.message);
                                    livewire.emit('refresh');
                                },
                            })
                        }
                    }
                },
                error: function (error){
                    displayErrorMessage(error.responseJSON.message);
                    livewire.emit('refresh');
                }
            });
        } else {
            Livewire.emit("refresh");
        }
    });
});
