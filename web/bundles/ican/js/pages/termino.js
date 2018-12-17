var Termino = function () {

    var termino_id;
    
    //Reset forms
    var resetForms = function () {
        $('#pagina-form input, #pagina-form textarea').each(function (e) {
            $element = $(this);

            if ($(this).attr('id') != "termino_id") {
                $element.val('');

                $element.data("title", "").removeClass("has-error").tooltip("dispose");
                $element.closest('.form-group').removeClass('has-error').addClass('success');
            }
        });

        $('#descripcion').summernote('code', '');

        //Dropzone
        if ($('#my-dropzone').hasClass('dz-started')) {
            $('#my-dropzone').removeClass('dz-started');
        }
        $('.dz-preview').remove();
        realFiles = 0;

        //Limpiar tags
        $('#tags_tagsinput span').each(function (e) {
            $(this).remove();
        });

        var $element = $('.tagsinput');
        $element.tooltip("dispose");
        $element.removeClass('has-error');
        $element.closest('.form-group')
            .removeClass('has-error');

        var $element = $('#processing-dropzone');
        $element.tooltip("dispose")
            .removeClass('has-error')
            .closest('.form-group')
            .removeClass('has-error');
    };

    //Validacion
    var initForm = function () {
        $("#pagina-form").validate({
            rules: {
                titulo: {
                    required: true
                }
            },
            messages: {
                titulo: "Este campo es obligatorio"
            },
            showErrors: function (errorMap, errorList) {
                // Clean up any tooltips for valid elements
                $.each(this.validElements(), function (index, element) {
                    var $element = $(element);

                    $element.data("title", "") // Clear the title - there is no error associated anymore
                        .removeClass("has-error")
                        .tooltip("dispose");

                    $element
                        .closest('.form-group')
                        .removeClass('has-error').addClass('success');
                });

                // Create new tooltips for invalid elements
                $.each(errorList, function (index, error) {
                    var $element = $(error.element);

                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", error.message)
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');

                });
            },
        });
    };

    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-termino");
        $(document).on('click', "#btn-salvar-termino", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();

            var descripcion = $('#descripcion').summernote('code');
            var tags = $('#tags').val();

            if ($('#pagina-form').valid() && descripcion !== "" && tags != "") {

                var termino_id = $('#termino_id').val();
                var titulo = $('#titulo').val();

                MyApp.block('#form-pagina');

                $.ajax({
                    type: "POST",
                    url: "terminos/salvarTermino",
                    dataType: "json",
                    data: {
                        'termino_id': termino_id,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags
                    },
                    success: function (response) {
                        mApp.unblock('#form-pagina');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            
                            termino_id = response.termino_id;
                            $("#termino_id").val(termino_id);
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-pagina');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (tags == "") {
                    var $element = $('.tagsinput');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
                if (descripcion == "") {
                    var $element = $('.note-editor');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
            }
        };
    }
    var editRow = function (termino_id) {
        resetForms();
        
        MyApp.block('#pagina-form');

        $.ajax({
            type: "POST",
            url: "terminos/cargarDatos",
            dataType: "json",
            data: {
                'termino_id': termino_id
            },
            success: function (response) {
                mApp.unblock('#pagina-form');
                if (response.success) {
                    //Datos marca

                    $('#titulo').val(response.pagina.titulo);
                    $('#descripcion').summernote('code', response.pagina.descripcion);

                    var tags = response.pagina.tags;
                    if (tags != "" && tags != null) {
                        tags = tags.split(',');
                        for (var i = 0; i < tags.length; i++)
                            $('#tags').addTag(tags[i]);
                    }
                } else {
                    toastr.error(response.error, "Error !!!");
                }
            },
            failure: function (response) {
                mApp.unblock('#pagina-form');

                toastr.error(response.error, "Error !!!");
            }
        });

    }

    var initTagsInput = function () {

        $('.tags').tagsInput({
            width: 'auto',
            defaultText: 'Tag...',
        });

    }
    
    return {
        //main function to initiate the module
        init: function () {
            
            initForm();
            initTagsInput();
            
            initAccionSalvar();
            
            termino_id = $("#termino_id").val();
            if(termino_id != ""){
                editRow(termino_id);
            }
            
        }

    };

}();