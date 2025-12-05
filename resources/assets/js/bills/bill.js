"use strict";

listenClick(".bill-delete-btn", function (event) {
    let id = $(event.currentTarget).data("id");
    deleteItem($("#indexBillUrl").val() + "/" + id, "", $("#Bill").val());
});

listenChange("#manualPayment", function () {
    let id = $(this).data("id");
    let payment_status = $(this).val();

    $.ajax({
        url: route("manual-billing-payments.update", id),
        type: "PATCH",
        data: { payment_status: payment_status },
        success: function (data) {
            displaySuccessMessage(data.message);
            livewire.emit('refresh');
        },
    });
});
