var Categorias = function () {

    var arbol = null;
    var seleccion = null;
    var rowDelete = null;
    var tipo = "";

    //Init tree
    function initTree() {
        $('#arbol-categoria').jstree(
            {
                "core": {
                    "multiple": false, // no multiselection
                    "themes": {
                        "responsive": false,
                        "variant": "large"
                    },
                    // so that create works
                    "check_callback": true,
                    'data': {
                        'url': function (node) {
                            return 'categoria/listarCategoria';
                        },
                        'data': function (node) {
                            return {'parent': node.id};
                        }
                    },
                    "expand_selected_onload": true
                },
                /*"checkbox": {
                 //"keep_selected_style": false,
                 //"cascade": "up",
                 //"three_state": true
                 },*/
                "plugins": ["wholerow", /*"checkbox"*/, "search"]
            }
        );

        $('#arbol-categoria').on("changed.jstree", function (e, data) {
            seleccion = data.selected;
            mostrarModalDetalle();
        });

        arbol = $.jstree.reference('#arbol-categoria');

        var to = false;
        $('#buscar-input').keyup(function () {
            if (to) {
                clearTimeout(to);
            }
            to = setTimeout(function () {
                var v = $('#buscar-input').val();
                //$('#arbol-categoria').jstree(true).search(v);
                arbol.search(v);
                MyApp.scrollTo($('.jstree-search'), -100);
            }, 250);
        });
    }

    //Validacion
    var initForm = function () {
        //Validacion
        $("#categoria-form").validate({
            rules: {
                nombre: {
                    required: true
                },
                titulo: {
                    required: true
                },
                descripcion: {
                    required: true,
                    maxlength: 160
                },
                tags: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio",
                titulo: "Este campo es obligatorio",
                descripcion: {
                    required: "Este campo es obligatorio",
                    maxlength: "Por favor, no escribas más de {0} caracteres"
                },
                tags: "Este campo es obligatorio",
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
        $("#editar-categoria-form").validate({
            rules: {
                nombre: {
                    required: true
                },
                titulo: {
                    required: true
                },
                descripcion: {
                    required: true,
                    maxlength: 160
                },
                tags: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio",
                titulo: "Este campo es obligatorio",
                descripcion: {
                    required: "Este campo es obligatorio",
                    maxlength: "Por favor, no escribas más de {0} caracteres"
                },
                tags: "Este campo es obligatorio",
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

    //Reset forms
    var resetForms = function () {
        $('#categoria-form input, #categoria-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');
            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        $('#estadoactivo').prop('checked', true);
        $('#estadoinactivo').prop('checked', false);

        $('#categoria').val('');
        $('#categoria').trigger('change');

        var $element = $('.selectpicker');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        $('#categoria-form').addClass('m--hide');
        $('#form-group-categoria').removeClass('m--hide');
        $('#form-toolbar').removeClass('m--hide');
        tipo = "";


        //Limpiar tags
        $('#tags_tagsinput span').each(function (e) {
            $(this).remove();
        });

        var $element = $('.tagsinput');
        $element.tooltip("dispose");
        $element.removeClass('has-error');
        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;
    };
    var resetFormsEditar = function () {
        $('#editar-categoria-form input, #editar-categoria-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');
            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        $('#estadoactivo-editar').prop('checked', true);
        $('#estadoinactivo-editar').prop('checked', false);

        //Limpiar tags
        $('#tags-editar_tagsinput span').each(function (e) {
            $(this).remove();
        });

        var $element = $('.tagsinput');
        $element.tooltip("dispose");
        $element.removeClass('has-error');
        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;
    };
    var eliminarSeleccionArbol = function () {
        arbol.deselect_all();
        seleccion = null;
    }
    var mostrarModalDetalle = function () {
        if (seleccion.length > 0) {
            resetFormsEditar();

            var categoria_id = seleccion[0];
            $('#categoria_id').val(categoria_id);

            $('#modal-editar-categoria').modal({
                'show': true
            });

            editRow(categoria_id);

        }

        function editRow(categoria_id) {

            MyApp.block('#editar-categoria-form');

            $.ajax({
                type: "POST",
                url: "categoria/cargarDatos",
                dataType: "json",
                data: {
                    'categoria_id': categoria_id
                },
                success: function (response) {
                    mApp.unblock('#editar-categoria-form');
                    if (response.success) {

                        //Datos categorias
                        $('#nombre-editar').val(response.categoria.nombre);
                        $('#titulo-editar').val(response.categoria.titulo);
                        $('#descripcion-editar').val(response.categoria.descripcion);

                        if (!response.categoria.estado) {
                            $('#estadoactivo-editar').prop('checked', false);
                            $('#estadoinactivo-editar').prop('checked', true);
                        }

                        var tags = response.categoria.tags;
                        if (tags != "" && tags != null) {
                            tags = tags.split(',');
                            for (var i = 0; i < tags.length; i++)
                                $('#tags-editar').addTag(tags[i]);
                        }

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#editar-categoria-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }

    }

    //Eventos change
    var event_change = false;
    var initAccionChange = function () {
        $(document).off('change', ".event-change");
        $(document).on('change', ".event-change", function (e) {
            event_change = true;
            //nombre = titulo por defecto
            if($(this).attr('id') == 'nombre'){
                $('#titulo').val($(this).val());
            }
        });

        $(document).off('click', "#btn-save-changes");
        $(document).on('click', "#btn-save-changes", function (e) {
            cerrarFormsConfirmated();
        });
    };
    var cerrarFormsConfirmated = function () {
        resetForms();
        eliminarSeleccionArbol();
        $('#categoria-form').addClass('m--hide');
        $('#modal-editar-categoria').modal('hide');
    };

    //Cerrar forms
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-categoria");
        $(document).on('click', ".cerrar-form-categoria", function (e) {
            cerrarForms();
        });
    }
    var cerrarForms = function () {

        if (!event_change) {
            cerrarFormsConfirmated();
        } else {
            $('#modal-salvar-cambios').modal({
                'show': true
            });
        }
    };
    //Funciones para angular

    //Nuevo
    var initAccionNuevo = function () {
        $(document).off('click', "#btn-nuevo-categoria");
        $(document).on('click', "#btn-nuevo-categoria", function (e) {
            btnClickNuevo('categoria');
        });

        $(document).off('click', "#btn-nuevo-subcategoria");
        $(document).on('click', "#btn-nuevo-subcategoria", function (e) {
            btnClickNuevo('subcategoria');
        });

        function btnClickNuevo(tipo_add) {
            resetForms();
            $('#categoria-form').removeClass('m--hide');

            tipo = tipo_add;
            if (tipo === 'categoria') {
                $('#form-group-categoria').addClass('m--hide');
            } else {
                $('#form-group-categoria').removeClass('m--hide');
            }
            $('#form-toolbar').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-categoria");
        $(document).on('click', "#btn-salvar-categoria", function (e) {
            btnClickSalvarForm();
        });
        $(document).off('click', "#btn-actualizar-categoria");
        $(document).on('click', "#btn-actualizar-categoria", function (e) {
            btnClickActualizarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();

            event_change = false;

            var titulo = $('#titulo').val();
            var descripcion = $('#descripcion').val();
            var tags = $('#tags').val();

            if ($('#categoria-form').valid() && tags != "") {

                var categoria_padre_id = $('#categoria').val();
                if (tipo == 'subcategoria') {
                    if (categoria_padre_id == "") {

                        var $element = $('#select-categoria .select2');
                        $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                            .data("title", "Este campo es obligatorio")
                            .addClass("has-error")
                            .tooltip({
                                placement: 'bottom'
                            }); // Create a new tooltip based on the error messsage we just set in the title

                        $element.closest('.form-group')
                            .removeClass('has-success').addClass('has-error');
                        return;
                    }
                }

                var nombre = $('#nombre').val();
                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;

                MyApp.block('#form-categoria');

                $.ajax({
                    type: "POST",
                    url: "categoria/salvarCategoria",
                    dataType: "json",
                    data: {
                        'categoria_id': '',
                        'categoria_padre_id': categoria_padre_id,
                        'nombre': nombre,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags,
                        'estado': estado
                    },
                    success: function (response) {
                        mApp.unblock('#form-categoria');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();

                            arbol.refresh();

                            actualizarCategorias(response.categorias);

                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-categoria');

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
                if (tags == "" || titulo == "" || descripcion == "") {
                    toastr.error("Por favor, rectifica la información del Seo On Page");
                }
            }
        };

        function btnClickActualizarForm() {

            event_change = false;

            var titulo = $('#titulo-editar').val();
            var descripcion = $('#descripcion-editar').val();
            var tags = $('#tags-editar').val();

            if ($('#editar-categoria-form').valid() && tags != "") {

                var categoria_id = $('#categoria_id').val();

                var nombre = $('#nombre-editar').val();
                var estado = ($('#estadoactivo-editar').prop('checked')) ? 1 : 0;

                MyApp.block('#editar-categoria-form');

                $.ajax({
                    type: "POST",
                    url: "categoria/salvarCategoria",
                    dataType: "json",
                    data: {
                        'categoria_id': categoria_id,
                        'categoria_padre_id': '',
                        'nombre': nombre,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags,
                        'estado': estado
                    },
                    success: function (response) {
                        mApp.unblock('#editar-categoria-form');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();

                            arbol.refresh();

                            actualizarCategorias(response.categorias);

                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#editar-categoria-form');

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
                if (tags == "" || titulo == "" || descripcion == "") {
                    toastr.error("Por favor, rectifica la información del Seo On Page");
                }
            }
        };
    }

    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#btn-eliminar-categoria");
        $(document).on('click', "#btn-eliminar-categoria", function (e) {
            e.preventDefault();

            rowDelete = seleccion[0];
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-delete");
        $(document).on('click', "#btn-delete", function (e) {
            btnClickModalEliminar();
        });

        function btnClickModalEliminar() {
            var categoria_id = rowDelete;
            $('#modal-editar-categoria').modal('hide');

            MyApp.block('#div-arbol-categoria');

            $.ajax({
                type: "POST",
                url: "categoria/eliminarCategoria",
                dataType: "json",
                data: {
                    'categoria_id': categoria_id
                },
                success: function (response) {
                    mApp.unblock('#div-arbol-categoria');
                    if (response.success) {

                        toastr.success(response.message, "Exito !!!");
                        arbol.refresh();
                        actualizarCategorias(response.categorias);
                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#div-arbol-categoria');
                    toastr.error(response.error, "Error !!!");
                }
            });
        };
    };

    //Actualizar select categorias
    var actualizarCategorias = function (categorias) {
        //Limipiar select categorias
        $('#categoria option').each(function (e) {
            if ($(this).val() != "")
                $(this).remove();
        });
        $('#categoria').select2();

        //Llenar categorias
        for (var i = 0; i < categorias.length; i++) {
            var id = categorias[i].categoria_id;
            var descripcion = categorias[i].descripcion;
            var class_css = categorias[i].class;

            $('#categoria').append(new Option(descripcion, id, false, false));
            $('#categoria option[value="' + id + '"]').attr("class", class_css);
        }
        initSelects();
    }

    //Init selects y tags input
    var initSelects = function () {
        $('#categoria').select2({
            templateResult: function (data) {
                // We only really care if there is an element to pull classes from
                if (!data.element) {
                    return data.text;
                }

                var $element = $(data.element);

                var $wrapper = $('<span></span>');
                $wrapper.addClass($element[0].className);

                $wrapper.text(data.text);

                return $wrapper;
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

            initTree();
            initForm();

            initSelects();
            initTagsInput();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();
            initAccionEliminar();
            initAccionChange();
        }

    };

}();