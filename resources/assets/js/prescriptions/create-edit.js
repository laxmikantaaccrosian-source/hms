document.addEventListener("turbo:load", loadPrescriptionCreate);

let uniquePrescriptionId = "";

function loadPrescriptionCreate() {
    if (
        !$("#prescriptionPatientId").length &&
        !$("#editPrescriptionPatientId").length
    ) {
        return;
    }
    const prescriptionAddedAtElement = $("#prescriptionAddedAt");
    const editPrescriptionAddedAtElement = $("#editPrescriptionAddedAt");
    $(
        "#prescriptionPatientId,#editPrescriptionPatientId,#filter_status,#prescriptionDoctorId,#editPrescriptionDoctorId,#prescriptionTime,#prescriptionMedicineCategoryId,#prescriptionMedicineBrandId,.prescriptionMedicineId,.prescriptionMedicineMealId,#editPrescriptionTime,.prescriptionMedicineDurationId,.prescriptionMedicineIntervalId"
    ).select2({
        width: "100%",
    });

    $("#prescriptionMedicineBrandId, #prescriptionMedicineBrandId").select2({
        width: "100%",
        dropdownParent: $("#add_new_medicine"),
    });
    if (prescriptionAddedAtElement.length) {
        $("#prescriptionAddedAt").flatpickr({
            maxDate: new Date(),
            locale: $(".userCurrentLanguage").val(),
        });
    }
    if (editPrescriptionAddedAtElement.length) {
        $("#editPrescriptionAddedAt").flatpickr({
            maxDate: new Date(),
            locale: $(".userCurrentLanguage").val(),
        });
    }
    $("#prescriptionPatientId").first().focus();
}
listenSubmit("#createPrescription, #editPrescription", function () {
    $(".btnPrescriptionSave").attr("disabled", true);
});

listenSubmit("#createMedicineFromPrescription", function (e) {
    e.preventDefault();
    $.ajax({
        url: $("#createMedicineFromPrescriptionPost").val(),
        method: "POST",
        data: $(this).serialize(),
        success: function (result) {
            $(".medicineTable").load(
                location.href + " .medicineTable",
                function () {
                    $(".prescriptionMedicineId").select2();
                }
            );
            displaySuccessMessage(result.message);
            $("#add_new_medicine").modal("hide");
        },
        error: function (result) {
            printErrorMessage("#medicinePrescriptionErrorBox", result);
        },
    });
});

const dropdownToSelecte2 = (selector) => {
    $(selector).select2({
        placeholder: Lang.get("js.select_medicine"),
        width: "100%",
    });
};

listenHiddenBsModal("#add_new_medicine", function () {
    resetModalForm(
        "#createMedicineFromPrescription",
        "#medicinePrescriptionErrorBox"
    );
});

listenClick(".delete-prescription-medicine-item", function () {
    $(this).parents("tr").remove();
    // resetPrescriptionMedicineItemIndex()
});

listenClick(".add-medicine-btn", function () {
    let uniquePrescriptionId = $("#prescriptionUniqueId").val();
    let data = {
        medicines: JSON.parse($(".associatePrescriptionMedicines").val()),
        meals: JSON.parse($(".associatePrescriptionMeals").val()),
        doseDuration: JSON.parse($(".associatePrescriptionDurations").val()),
        doseInterval: JSON.parse($(".associatePrescriptionIntervals").val()),
        uniqueId: uniquePrescriptionId,
    };
    let prescriptionMedicineHtml = prepareTemplateRender(
        "#prescriptionMedicineTemplate",
        data
    );
    $(".prescription-medicine-container").append(prescriptionMedicineHtml);
    dropdownToSelecte2(".prescriptionMedicineId");
    dropdownToSelecte2(".prescriptionMedicineMealId");
    dropdownToSelecte2(".prescriptionMedicineDurationId");
    dropdownToSelecte2(".prescriptionMedicineIntervalId");
    uniquePrescriptionId++;
    $("#prescriptionUniqueId").val(uniquePrescriptionId);

    // resetPrescriptionMedicineItemIndex();
});

const resetPrescriptionMedicineItemIndex = () => {
    let index = 1;
    if (index - 1 == 0) {
        let uniquePrescriptionId = $("#prescriptionUniqueId").val();
        let data = {
            medicines: JSON.parse($(".associatePrescriptionMedicines").val()),
            meals: JSON.parse($(".associatePrescriptionMeals").val()),
            doseDuration: JSON.parse(
                $(".associatePrescriptionDurations").val()
            ),
            doseInterval: JSON.parse(
                $(".associatePrescriptionIntervals").val()
            ),
            uniqueId: uniquePrescriptionId,
        };
        let packageServiceItemHtml = prepareTemplateRender(
            "#prescriptionMedicineTemplate",
            data
        );
        $(".prescription-medicine-container").append(packageServiceItemHtml);
        dropdownToSelecte2(".prescriptionMedicineId");
        dropdownToSelecte2(".prescriptionMedicineMealId");
        dropdownToSelecte2(".prescriptionMedicineDurationId");
        dropdownToSelecte2(".prescriptionMedicineIntervalId");
        uniquePrescriptionId++;
        $("#prescriptionUniqueId").val(uniquePrescriptionId);
    }
};

listenChange(".prescriptionMedicineId", function () {
    let uniqueId = $(this).attr("data-id");
    let medicineId = $(this).val();
    let currentRow = $(this).closest("tr");
    let AvailbleQty = currentRow.find("#AvailbleQty:first");
    let AvailbleQtyClass = currentRow.find(".AvailbleQtyClass");
    let AvlQtyDiv = "#medicineDiv" + uniqueId;

    $.ajax({
        url: route("prescription.medicine.quantity", medicineId),
        type: "GET",
        success: function (data) {
            if (data.data.length !== 0) {
                let availableQuantity = data.data.available_quantity;
                let availbleQtyText = `${Lang.get(
                    "js.available_quantity"
                )}: ${availableQuantity}`;
                let availbleQtyClass =
                    availableQuantity == 0 ? "text-danger" : "text-success";

                $(AvailbleQtyClass).text("");
                console.log($(AvailbleQty)
                .text(availbleQtyText)
                .removeClass()
                .addClass(availbleQtyClass));
                $(AvailbleQty)
                    .text(availbleQtyText)
                    .removeClass()
                    .addClass(availbleQtyClass);
                $(AvlQtyDiv).css({ "margin-top": "22px" });
            }
        },
        error: function () {
            console.log('dsbhjdsbv');
            $(AvailbleQty).text("");
            $(AvlQtyDiv).css({ "margin-top": "0px" });
        },
    });
});
