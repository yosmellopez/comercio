var WebpayConfig = function () {

    var webpay_id;

    //Reset forms
    var resetForms = function () {
        $('#webpay-form input, #webpay-form textarea').each(function (e) {
            $element = $(this);

            if ($(this).attr('id') != "webpay_id") {
                $element.val('');

                $element.data("title", "").removeClass("has-error").tooltip("dispose");
                $element.closest('.form-group').removeClass('has-error').addClass('success');
            }
        });
    };

    //Validacion
    var initForm = function () {
        $("#webpay-form").validate({
            rules: {
                comercioCode: {
                    required: true
                },
                privateKey: {
                    required: true
                },
                privateCert: {
                    required: true
                },
                publicCert: {
                    required: true
                }
            },
            messages: {
                comercioCode: "Este campo es obligatorio",
                privateKey: {
                    required: "Este campo es obligatorio",
                },
                privateCert: {
                    required: "Este campo es obligatorio",
                },
                publicCert: {
                    required: "Este campo es obligatorio",
                }
            },
            showErrors: function (errorMap, errorList) {
                // Clean up any tooltips for valid elements
                $.each(this.validElements(), function (index, element) {
                    var $element = $(element);

                    $element.data("title", "") // Clear the title - there is no error associated anymore
                        .removeClass("has-error")
                        .tooltip("destroy");

                    $element
                        .closest('.form-group')
                        .removeClass('has-error').addClass('success');
                });

                // Create new tooltips for invalid elements
                $.each(errorList, function (index, error) {
                    var $element = $(error.element);

                    $element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", error.message)
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title 

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');

                });
            }
        });
    };

    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-webpay");
        $(document).on('click', "#btn-salvar-webpay", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();

            if ($('#webpay-form').valid()) {

                var webpay_id = $('#webpay_id').val();

                var comercioCode = $('#comercioCode').val();
                var privateKey = $('#privateKey').val();
                var privateCert = $('#privateCert').val();
                var publicCert = $('#publicCert').val();

                MyApp.block('#form-webpay');

                $.ajax({
                    type: "POST",
                    url: "webpayconfig/salvar",
                    dataType: "json",
                    data: {
                        'webpay_id': webpay_id,
                        'comercioCode': comercioCode,
                        'privateKey': privateKey,
                        'privateCert': privateCert,
                        'publicCert': publicCert
                    },
                    success: function (response) {
                        mApp.unblock('#form-webpay');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");

                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-webpay');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {

            }
        };
    }


    return {
        //main function to initiate the module
        init: function () {

            initForm();

            initAccionSalvar();

        }

    };

}();