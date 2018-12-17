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
        $("#contact-form").validate({
            rules: {
                nombre: {
                    required: true,
                    nombre: true
                },
                telefono: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                asunto: {
                    required: true
                },
                mensaje: {
                    required: true
                }
            },
            messages: {
                nombre: {
                    required: "Este campo es obligatorio",
                    nombre: "Por favor, escribe tu nombre sin números"
                },
                telefono: "Este campo es obligatorio",
                asunto: "Este campo es obligatorio",
                mensaje: "Este campo es obligatorio",
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
            $('#contact-form input').each(function (e) {
                $(this).val('');
            });
            $('#mensaje').val('');
        }
        $('#btn-enviar-contacto').click(function (e) {
            e.preventDefault();
            var nombre = $('#nombre').val();
            var telefono = $('#telefono').val();
            var asunto = $('#asunto').val();
            var email = $('#email').val();
            var mensaje = $('#mensaje').val();

            if ($('#contact-form').valid()) {
                $('#section-contacto').html('<h4>Por favor, espere...</h4>');

                $.ajax({
                    type: "POST",
                    url: "contacto/procesarContacto",
                    data: {nombre: nombre, telefono: telefono, email: email, asunto: asunto, mensaje: mensaje}
                }).done(function (msg) {
                    if (msg == '1') {
                        resetFormContacto();
                        $('#section-contacto').html('<h4>Mensaje Enviado Correctamente</h4><br><p>Nos pondremos en contacto a la brevedad posible.</p>');
                    } else if (msg == 0) {
                        $('#section-contacto').html('<h4>No se pudo enviar el mensaje</h4><br> Intente mas tarde.');
                    }
                });
            }
        });

    });


})(jQuery);
