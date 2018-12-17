var Compras = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#compra-table-editable');
        var table = $('#compra-table-editable');
        var aoColumns = [
            {
                field: "id",
                title: "#",
                sortable: false, // disable sort for this column
                width: 40,
                textAlign: 'center',
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            },
            {
                field: "usuario",
                title: "Usuario",
                width: 200,
                template: function (row) {
                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-compra__pic">\
									<img src="' + row.imagen + '" class="m--img-rounded m--marginless" alt="photo" style="max-width: 60px !important;border-radius: 0px;">\
								</div>\
								<div class="m-card-user__details">\
									<span class="m-card-user__name">' + row.nombre + '</span>\
								</div>\
							</div>';
                    return output;
                }
            },
            {
                field: "precio",
                title: "Precio",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center'
            },
            {
                field: "fechaCompra",
                title: "Fecha de Compra",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "monto",
                title: "Monto Total",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center'
            },
            {
                field: "estado",
                title: "Estado",
                responsive: {visible: 'lg'},
                width: 60,
                // callback function support for column rendering
                template: function (row) {
                    var status = {
                        1: {'title': 'Activo', 'class': ' m-badge--success'},
                        0: {'title': 'Inactivo', 'class': ' m-badge--danger'}
                    };
                    return '<span class="m-badge ' + status[row.estado].class + ' m-badge--wide">' + status[row.estado].title + '</span>';
                }
            },
            {
                field: "acciones",
                width: 110,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center'
            }
        ];
        oTable = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'compra/listarCompra',
                    }
                },
                pageSize: 10,
                saveState: {
                    cookie: true,
                    webstorage: true
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            // layout definition
            layout: {
                theme: 'default', // datatable theme
                class: '', // custom wrapper class
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                //height: 550, // datatable's body's fixed height
                footer: false // display/hide footer
            },
            // column sorting
            sortable: true,
            pagination: true,
            // columns definition
            columns: aoColumns,
            // toolbar
            toolbar: {
                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        // page size select
                        pageSizeSelect: [10, 25, 30, 50, -1] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },
            //Tanslate
            translate: {
                records: {
                    processing: 'Por favor espere...',
                    noRecords: 'No se existen registros'
                },
                toolbar: {
                    pagination: {
                        items: {
                            info: 'Mostrando {{start}} - {{end}} de {{total}} registros'
                        }
                    }
                }
            }
        });

        //Events
        oTable
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#compra-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#compra-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#compra-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#compra-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#compra-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-compra .m_form_search').on('keyup', function (e) {
            var query = oTable.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();
            var categoria_id = $('#filtro-categoria').val();
            query.categoria_id = categoria_id;
            var marca_id = $('#filtro-marca').val();
            query.marca_id = marca_id;
            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };
    //Filtrar
    var initAccionFiltrar = function () {

        $(document).off('click', "#btn-filtrar");
        $(document).on('click', "#btn-filtrar", function (e) {
            btnClickFiltrar();
        });

        function btnClickFiltrar() {
            var query = oTable.getDataSourceQuery();

            var generalSearch = $('#lista-compra .m_form_search').val();
            query.generalSearch = generalSearch;

            var categoria_id = $('#filtro-categoria').val();
            query.categoria_id = categoria_id;

            var marca_id = $('#filtro-marca').val();
            query.marca_id = marca_id;

            oTable.setDataSourceQuery(query);
            oTable.load();
        }

    };

    //Reset forms
    var resetForms = function () {
        $('#compra-form input').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });
        $('#seo-form input, #seo-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        var fecha_actual = new Date();
        $('#fecha').val(fecha_actual.format("d/m/Y H:i"));

        $('#descripcion').summernote('code', '');

        $('#categoria').val('');
        $('#categoria').trigger('change');

        $('#marca').val('');
        $('#marca').trigger('change');

        $('#estadoactivo').prop('checked', true);
        $('#mostrarPrecioactivo').prop('checked', true);

        //Dropzone
        if ($('#my-dropzone').hasClass('dz-started')) {
            $('#my-dropzone').removeClass('dz-started');
        }
        if ($('#my-dropzone-galeria').hasClass('dz-started')) {
            $('#my-dropzone-galeria').removeClass('dz-started');
        }
        $('.dz-preview').remove();
        realFiles = 0;
        realFilesGaleria = 0;

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

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group').removeClass('has-error');
        event_change = false;
        //Reset relacionados
        relacionados = [];
        //Mostrar el primer tab
        resetWizard();
    };

    //Validacion
    var initForm = function () {
        $("#compra-form").validate({
            rules: {
                nombre: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio"
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
        $("#seo-form").validate({
            rules: {
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

    //Nuevo
    var initAccionNuevo = function () {
        $(document).off('click', "#btn-nuevo-compra");
        $(document).on('click', "#btn-nuevo-compra", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear una nueva compra? Sigue los siguientes pasos:";
            $('#form-compra-title').html(formTitle);
            $('#form-compra').removeClass('m--hide');
            $('#lista-compra').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-wizard-finalizar");
        $(document).on('click', "#btn-wizard-finalizar", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            var compra_id = $('#compra_id').val();

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            var tags = $('#tags').val();

            var descripcion = $('#descripcion').summernote('code');
            var categoria_id = $('#categoria').val();
            var marca_id = $('#marca').val();

            if ($('#compra-form').valid() && $('#seo-form').valid() && descripcion !== ""
                && imagen != "" && tags != "" && categoria_id != "" && marca_id != "") {

                var nombre = $('#nombre').val();
                var titulo = $('#titulo').val();

                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;
                var mostrarPrecio = ($('#mostrarPrecioactivo').prop('checked')) ? 1 : 0;

                var stock = $('#precio').val();
                var precio = $('#precio').val();
                var precioOferta = $('#precioOferta').val();

                var imagenes = "";
                $('#my-dropzone-galeria .dz-preview').each(function (e) {
                    imagenes += $(this).attr('data-value-imagen') + ',';
                });

                var fecha = $('#fecha').val();

                MyApp.block('#form-compra');

                $.ajax({
                    type: "POST",
                    url: "compra/salvarCompra",
                    dataType: "json",
                    data: {
                        'compra_id': compra_id,
                        'categoria': categoria_id,
                        'marca': marca_id,
                        'nombre': nombre,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags,
                        'imagen': imagen,
                        'imagenes': imagenes,
                        'compras': relacionados,
                        'stock': stock,
                        'precio': precio,
                        'precioOferta': precioOferta,
                        'mostrarPrecio': mostrarPrecio,
                        'estado': estado,
                        'fecha': fecha
                    },
                    success: function (response) {
                        mApp.unblock('#form-compra');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-compra');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (imagen == "") {
                    var $element = $('#processing-dropzone');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Es obligatorio seleccionar una imagen")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
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
                if (categoria_id == "") {

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
                if (marca_id == "") {

                    var $element = $('#select-marca .select2');
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
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-compra");
        $(document).on('click', ".cerrar-form-compra", function (e) {
            cerrarForms();
        });
    }
    //Cerrar forms
    var cerrarForms = function () {
        if (!event_change) {
            cerrarFormsConfirmated();
        } else {
            $('#modal-salvar-cambios').modal({
                'show': true
            });
        }
    };
    //Editar
    var initAccionEditar = function () {
        $(document).off('click', "#compra-table-editable a.edit");
        $(document).on('click', "#compra-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var compra_id = $(this).data('id');
            $('#compra_id').val(compra_id);

            $('#form-compra').removeClass('m--hide');
            $('#lista-compra').addClass('m--hide');

            editRow(compra_id);
        });

        function editRow(compra_id) {

            MyApp.block('#compra-form');

            $.ajax({
                type: "POST",
                url: "compra/cargarDatos",
                dataType: "json",
                data: {
                    'compra_id': compra_id
                },
                success: function (response) {
                    mApp.unblock('#compra-form');
                    if (response.success) {
                        //Datos compra    

                        var formTitle = "¿Deseas actualizar la compra \"" + response.compra.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-compra-title').html(formTitle);

                        $('#nombre').val(response.compra.nombre);
                        $('#titulo').val(response.compra.titulo);
                        $('#descripcion').summernote('code', response.compra.descripcion);

                        $('#stock').val(response.compra.stock);
                        $('#precio').val(response.compra.precio);
                        $('#precioOferta').val(response.compra.precioOferta);

                        $('#fecha').val(response.compra.fecha);

                        if (!response.compra.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }
                        if (!response.compra.mostrarPrecio) {
                            $('#mostrarPrecioactivo').prop('checked', false);
                            $('#mostrarPrecioinactivo').prop('checked', true);
                        }

                        var tags = response.compra.tags;
                        if (tags != "" && tags != null) {
                            tags = tags.split(',');
                            for (var i = 0; i < tags.length; i++)
                                $('#tags').addTag(tags[i]);
                        }

                        $('#categoria').val(response.compra.categoria);
                        $('#categoria').trigger('change');

                        $('#marca').val(response.compra.marca);
                        $('#marca').trigger('change');

                        mostrarImagen(response.compra.imagen[0], response.compra.imagen[1], response.compra.imagen[2]);

                        var images = response.compra.imagenes;
                        for (var i = 0; i < images.length; i++) {
                            mostrarImagenGaleria(images[i].imagen[0], images[i].imagen[1], images[i].imagen[2]);
                        }

                        relacionados = response.compra.relacionados;
                        actualizarTableListaRelacionados();

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#compra-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#compra-table-editable a.delete");
        $(document).on('click', "#compra-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-compra");
        $(document).on('click', "#btn-eliminar-compra", function (e) {
            btnClickEliminar();
        });

        $(document).off('click', "#btn-delete");
        $(document).on('click', "#btn-delete", function (e) {
            btnClickModalEliminar();
        });

        $(document).off('click', "#btn-delete-selection");
        $(document).on('click', "#btn-delete-selection", function (e) {
            btnClickModalEliminarSeleccion();
        });

        function btnClickEliminar() {
            var ids = '';
            $('.m-datatable__cell--check .m-checkbox--brand > input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    var value = $(this).attr('value');
                    if (value != undefined) {
                        ids += value + ',';
                    }
                }
            });

            if (ids != '') {
                $('#modal-eliminar-seleccion').modal({
                    'show': true
                });
            } else {
                toastr.error('Seleccione los elementos a eliminar', "Error !!!");
            }
        };

        function btnClickModalEliminar() {
            var compra_id = rowDelete;

            MyApp.block('#compra-table-editable');

            $.ajax({
                type: "POST",
                url: "compra/eliminarCompra",
                dataType: "json",
                data: {
                    'compra_id': compra_id
                },
                success: function (response) {
                    mApp.unblock('#compra-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#compra-table-editable');

                    toastr.error(response.error, "Error !!!");
                }
            });
        };

        function btnClickModalEliminarSeleccion() {
            var ids = '';
            $('.m-datatable__cell--check .m-checkbox--brand > input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    var value = $(this).attr('value');
                    if (value != undefined) {
                        ids += value + ',';
                    }
                }
            });

            MyApp.block('#compra-table-editable');

            $.ajax({
                type: "POST",
                url: "compra/eliminarCompras",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#compra-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#compra-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        };
    };

    //Eventos change
    var event_change = false;
    var initAccionChange = function () {
        $(document).off('change', ".event-change");
        $(document).on('change', ".event-change", function (e) {
            event_change = true;
        });

        $(document).off('click', "#btn-save-changes");
        $(document).on('click', "#btn-save-changes", function (e) {
            cerrarFormsConfirmated();
        });
    };
    var cerrarFormsConfirmated = function () {
        resetForms();
        $('#form-compra').addClass('m--hide');
        $('#lista-compra').removeClass('m--hide');
    };

    //Init dropzone
    var myDropZone = null;
    var realFiles = 0;
    var maxFiles = 1;
    var initDropZone = function () {
        // Prevent Dropzone from auto discovering this element:
        Dropzone.options.myDropzone = false;
        Dropzone.autoDiscover = false;

        myDropZone = new Dropzone("div#my-dropzone", {
            thumbnailWidth: 1131,
            thumbnailHeight: 780,
            maxFilesize: 10, // MB
            paramName: "foto", // The name that will be used to transfer the file
            dictCancelUploadConfirmation: "¿Estas seguro que quieres cancelar la subida del archivo?",
            dictRemoveFileConfirmation: "¿Estas seguro que quieres eliminar el archivo?",
            dictDefaultMessage: "Arrastra tu imagen o haz click aquí",
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            dictCancelUpload: "Cancelar",
            url: "compra/salvarImagen",
            accept: function (file, done) {
                realFiles++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    if (maxFiles < realFiles) {
                        toastr.error('Solo se permite una imagen principal por compra', "Error !!!");
                        //Eliminar
                        eliminarImagen(file);

                    }
                    else {
                        done();
                    }
                }
            },
            success: function (file, response) {

                if (file.width != '591' || file.height != '447') {
                    toastr.error('Las dimensiones de la imagen deben ser 591 x 447', "Error !!!");
                    myDropZone.removeFile(file);
                } else {

                    $(file.previewElement).attr('data-value-imagen', response.name);
                    if (file.previewElement) {
                        return file.previewElement.classList.add("dz-success");
                    }

                    event_change = true;
                }

            },
            removedfile: function (file) {
                var imagen = $(file.previewElement).attr('data-value-imagen');

                MyApp.block('#processing-dropzone');

                $.ajax({
                    type: "POST",
                    url: "compra/eliminarImagen",
                    dataType: "json",
                    data: {
                        'imagen': imagen
                    },
                    success: function (response) {
                        mApp.unblock('#processing-dropzone');
                        if (response.success) {
                            eliminarImagen(file);
                            return;

                        } else {
                            toastr.error(response.error, "Error !!!");
                            return;
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#processing-dropzone');

                        toastr.error(response.error, "Error !!!");
                        return;
                    }
                });
            },
            thumbnail: function (file, dataUrl) {
                var thumbnailElement, _i, _len, _ref, _results;
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    file.previewElement.classList.add("dz-image-preview");
                    _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        thumbnailElement = _ref[_i];
                        thumbnailElement.alt = file.name;
                        _results.push(thumbnailElement.src = dataUrl);
                    }
                    $(file.previewElement).attr('data-value-imagen', file.name);
                    return _results;
                }
            },
        });
        Dropzone.confirm = function (question, accepted, rejected) {
            // Ask the question, and call accepted() or rejected() accordingly.
            // CAREFUL: rejected might not be defined. Do nothing in that case.
            $('#confirm-question').html(question);

            $('#modal-confirm').modal({
                show: true
            });


            $(document).off('click', "#btn-confirm-aceptar");
            $(document).on('click', '#btn-confirm-aceptar', function () {
                accepted();
            });
            $(document).off('click', "#btn-confirm-cancelar");
            $(document).on('click', '#btn-confirm-cancelar', function () {
                //rejected();
            });
        }

        function eliminarImagen(file) {
            var _ref;
            if (file.previewElement) {
                if ((_ref = file.previewElement) != null) {
                    _ref.parentNode.removeChild(file.previewElement);
                }
            }
            realFiles--;
            event_change = true;
            if (realFiles < 1) {
                if ($('#my-dropzone').hasClass('dz-started'))
                    $('#my-dropzone').removeClass('dz-started');
            }

        }
    };
    var mostrarImagen = function (imagen, size, src) {
        if (imagen != "" && imagen != null) {
            var mockFile = {name: imagen, size: size};

            myDropZone.emit("addedfile", mockFile);
            myDropZone.emit("thumbnail", mockFile, src + imagen);
            myDropZone.emit("complete", mockFile);

            realFiles++;
            $('my-dropzone .dz-preview').addClass('dz-success');
        }
    };

    var myDropZoneGaleria = null;
    var realFilesGaleria = 0;
    var initDropZoneGaleria = function () {
        // Prevent Dropzone from auto discovering this element:
        Dropzone.options.myDropzoneGaleria = false;
        Dropzone.autoDiscover = false;

        myDropZoneGaleria = new Dropzone("div#my-dropzone-galeria", {
            thumbnailWidth: 1131,
            thumbnailHeight: 780,
            parallelUploads: 1,
            maxFilesize: 10, // MB
            paramName: "foto", // The name that will be used to transfer the file
            dictCancelUploadConfirmation: "¿Estas seguro que quieres cancelar la subida del archivo?",
            dictRemoveFileConfirmation: "¿Estas seguro que quieres eliminar el archivo?",
            dictDefaultMessage: "Arrastra tu imagen o haz click aquí",
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            dictCancelUpload: "Cancelar",
            url: "compra/salvarImagen",
            accept: function (file, done) {
                realFilesGaleria++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    done();
                }
            },
            success: function (file, response) {

                if (file.width != '591' || file.height != '447') {
                    toastr.error('Las dimensiones de la imagen deben ser 591 x 447', "Error !!!");
                    myDropZone.removeFile(file);
                } else {

                    $(file.previewElement).attr('data-value-imagen', response.name);
                    if (file.previewElement) {
                        return file.previewElement.classList.add("dz-success");
                    }

                    event_change = true;
                }

            },
            removedfile: function (file) {
                var imagen = $(file.previewElement).attr('data-value-imagen');

                MyApp.block('#processing-dropzone-galeria');

                $.ajax({
                    type: "POST",
                    url: "compra/eliminarImagen",
                    dataType: "json",
                    data: {
                        'imagen': imagen
                    },
                    success: function (response) {
                        mApp.unblock('#processing-dropzone-galeria');
                        if (response.success) {
                            eliminarImagen(file);
                            return;

                        } else {
                            toastr.error(response.error, "Error !!!");
                            return;
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#processing-dropzone-galeria');

                        toastr.error(response.error, "Error !!!");
                        return;
                    }
                });
            },
            thumbnail: function (file, dataUrl) {
                var thumbnailElement, _i, _len, _ref, _results;
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    file.previewElement.classList.add("dz-image-preview");
                    _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        thumbnailElement = _ref[_i];
                        thumbnailElement.alt = file.name;
                        _results.push(thumbnailElement.src = dataUrl);
                    }
                    $(file.previewElement).attr('data-value-imagen', file.name);
                    return _results;
                }
            },
        });
        Dropzone.confirm = function (question, accepted, rejected) {
            // Ask the question, and call accepted() or rejected() accordingly.
            // CAREFUL: rejected might not be defined. Do nothing in that case.
            $('#confirm-question').html(question);

            $('#modal-confirm').modal({
                show: true
            });


            $(document).off('click', "#btn-confirm-aceptar");
            $(document).on('click', '#btn-confirm-aceptar', function () {
                accepted();
            });
            $(document).off('click', "#btn-confirm-cancelar");
            $(document).on('click', '#btn-confirm-cancelar', function () {
                //rejected();
            });
        }

        function eliminarImagen(file) {
            var _ref;
            if (file.previewElement) {
                if ((_ref = file.previewElement) != null) {
                    _ref.parentNode.removeChild(file.previewElement);
                }
            }
            realFilesGaleria--;
            event_change = true;
            if (realFilesGaleria < 1) {
                if ($('#my-dropzone-galeria').hasClass('dz-started'))
                    $('#my-dropzone-galeria').removeClass('dz-started');
            }

        }
    };
    var mostrarImagenGaleria = function (imagen, size, src) {
        if (imagen != "" && imagen != null) {
            var mockFile = {name: imagen, size: size};

            myDropZoneGaleria.emit("addedfile", mockFile);
            myDropZoneGaleria.emit("thumbnail", mockFile, src + imagen);
            myDropZoneGaleria.emit("complete", mockFile);

            realFilesGaleria++;
            $('#my-dropzone-galeria .dz-preview').addClass('dz-success');
        }
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-compra');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    var initTagsInput = function () {

        $('.tags').tagsInput({
            width: 'auto',
            defaultText: 'Tag...',
        });

    }

    //Wizard
    var activeTab = 1;
    var totalTabs = 3;
    var initWizard = function () {
        $(document).off('click', "#form-compra .wizard-tab");
        $(document).on('click', "#form-compra .wizard-tab", function (e) {
            e.preventDefault();
            var item = $(this).data('item');
            activeTab = parseInt(item);

            if (activeTab < totalTabs) {
                $('#btn-wizard-finalizar').removeClass('m--hide').addClass('m--hide');
            }
            if (activeTab == 1) {
                $('#btn-wizard-anterior').removeClass('m--hide').addClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide');
            }
            if (activeTab > 1) {
                $('#btn-wizard-anterior').removeClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide');
            }
            if (activeTab == totalTabs) {
                $('#btn-wizard-finalizar').removeClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide').addClass('m--hide');
            }

        });

        //siguiente
        $(document).off('click', "#btn-wizard-siguiente");
        $(document).on('click', "#btn-wizard-siguiente", function (e) {
            if (validWizard()) {
                activeTab++;
                $('#btn-wizard-anterior').removeClass('m--hide');
                if (activeTab == totalTabs) {
                    $('#btn-wizard-finalizar').removeClass('m--hide');
                    $('#btn-wizard-siguiente').addClass('m--hide');
                }

                mostrarTab();
            }
        });
        //anterior
        $(document).off('click', "#btn-wizard-anterior");
        $(document).on('click', "#btn-wizard-anterior", function (e) {
            activeTab--;
            if (activeTab == 1) {
                $('#btn-wizard-anterior').addClass('m--hide');
            }
            if (activeTab < totalTabs) {
                $('#btn-wizard-finalizar').addClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide');
            }
            mostrarTab();
        });

    };
    var mostrarTab = function () {
        setTimeout(function () {
            switch (activeTab) {
                case 1:
                    $('#tab-general').tab('show');
                    break;
                case 2:
                    $('#tab-seo').tab('show');
                    break;
            }
        }, 0);
    }
    var resetWizard = function () {
        activeTab = 1;
        mostrarTab();
        $('#btn-wizard-finalizar').removeClass('m--hide').addClass('m--hide');
        $('#btn-wizard-anterior').removeClass('m--hide').addClass('m--hide');
        $('#btn-wizard-siguiente').removeClass('m--hide');
    }
    var validWizard = function () {
        var result = true;
        if (activeTab == 1) {

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            if (!$('#compra-form').valid() || imagen == "") {
                result = false;

                if (imagen == "") {
                    var $element = $('#processing-dropzone');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Es obligatorio seleccionar una imagen")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
            }

        }

        return result;
    }

    //Init selects
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
        $('#filtro-categoria').select2({
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

        $('#marca').select2();
        $('#filtro-marca').select2();
    };

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initForm();

            initSelects();
            initDropZone();
            initDropZoneGaleria();
            initTagsInput();

            initAccionFiltrar();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();
            initAccionChange();
            initPortlets();
            initWizard();
        }

    };

}();