(function ($) {
    //Contacto y Registro
    $(function () {
        //Contacto
        jQuery.validator.addMethod("nombre",
            function (value, element) {
                var result = false;
                var expresion = /^([a-zA-Z\-ñÑáéíóúÁÉÍÓÚ ]*)$/;

                if (expresion.test(value))
                    result = true;

                return result;
            },
            "Por favor, escribe tu nombre sin números"
        );
        $("#register-form").validate({
            rules: {
                nombre: {
                    required: true,
                    nombre: true
                },
                apellidos: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                },
                passwordRepeat: {
                    required: true
                }
            },
            messages: {
                nombre: {
                    required: "Este campo es obligatorio",
                    nombre: "Por favor, escribe tu nombre sin números"
                },
                apellidos: {
                    required: "Este campo es obligatorio",
                    nombre: "Por favor, escribe tus apellidos sin números"
                },
                password: "Este campo es obligatorio",
                passwordRepeat: "Este campo es obligatorio",
                email: {
                    required: "Este campo es obligatorio",
                    email: "Por favor, ingresa una dirección de correo válida"
                },
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

        function resetFormContacto() {
            $('#register-form input').each(function (e) {
                $(this).val('');
            });
        }

        $('#btn-enviar-registro').click(function (e) {
            e.preventDefault();
            var nombre = $('#nombre').val();
            var apellidos = $('#apellidos').val();
            var email = $('#email').val();
            var password = $('#password').val();
            password = (password != "") ? hex_sha1(password) : "";
            var passwordRepeat = $('#passwordRepeat').val();
            passwordRepeat = (passwordRepeat != "") ? hex_sha1(passwordRepeat) : "";
            $('#section-contacto').html("");
            if ($('#register-form').valid()) {
                $.ajax({
                    type: "POST",
                    url: "registro/procesar",
                    data: {
                        nombre: nombre,
                        apellidos: apellidos,
                        email: email,
                        password: password,
                        passwordRepeat: passwordRepeat
                    }
                }).done(function (resp) {
                    if (resp.msg == '1') {
                        resetFormContacto();
                        $('#section-register').html('<h4>Registro Completado Correctamente</h4><br><p>Usted recibio un correo con los datos para completar su registro.</p>');
                    } else if (resp.msg == 0) {
                        $('#section-contacto').html(resp.error);
                    }
                });
            }
        });
    });
})(jQuery);
