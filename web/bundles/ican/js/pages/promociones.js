var Promociones = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#promocion-table-editable');

        var table = $('#promocion-table-editable');

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
                field: "nombre",
                title: "Nombre",
                width: 200,
                template: function (row) {

                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-promocion__pic">\
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
                field: "valor",
                title: "Descuento",
                responsive: {visible: 'lg'},
                textAlign: 'center',
                template: function (row) {
                    return row.valor + '%';
                }
            },
            {
                field: "fechainicio",
                title: "Inicio",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "fechafin",
                title: "Fin",
                responsive: {visible: 'lg'},
                width: 120,
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
                        url: 'promocion/listarPromocion',
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
                mApp.unblock('#promocion-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#promocion-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#promocion-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#promocion-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#promocion-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-promocion .m_form_search').on('keyup', function (e) {

            var query = oTable.getDataSourceQuery();

            query.generalSearch = $(this).val().toLowerCase();

            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };

    //Reset forms
    var resetForms = function () {
        $('#promocion-form input').each(function (e) {
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
        var fecha_aux = new Date();
        fecha_aux.setHours(120 + fecha_aux.getHours()); // Por defecto 5 dias para la promocion.

        $('#fechainicio').val(fecha_actual.format("d/m/Y H:i"));
        $('#fechafin').val(fecha_aux.format("d/m/Y H:i"));

        $('#porciento').val('');
        $('#porciento').trigger('change');

        $('#estadoactivo').prop('checked', true);

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

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;

        //Reset productos
        productos = [];
        actualizarTableListaProductos();

        //Mostrar el primer tab
        resetWizard();
    };

    //Validacion
    var initForm = function () {
        $("#promocion-form").validate({
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
        $(document).off('click', "#btn-nuevo-promocion");
        $(document).on('click', "#btn-nuevo-promocion", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear un nuevo banner de promoción? Sigue los siguientes pasos:";
            $('#form-promocion-title').html(formTitle);
            $('#form-promocion').removeClass('m--hide');
            $('#lista-promocion').addClass('m--hide');
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

            var promocion_id = $('#promocion_id').val();

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            var tags = $('#tags').val();

            var porciento_id = $('#porciento').val();

            if ($('#promocion-form').valid() && $('#seo-form').valid()
                && imagen != "" && tags != "" && porciento_id != "" && productos.length > 0) {

                var nombre = $('#nombre').val();
                var titulo = $('#titulo').val();
                var descripcion = $('#descripcion').val();

                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;

                var fechainicio = $('#fechainicio').val();
                var fechafin = $('#fechafin').val();

                MyApp.block('#form-promocion');

                $.ajax({
                    type: "POST",
                    url: "promocion/salvarPromocion",
                    dataType: "json",
                    data: {
                        'promocion_id': promocion_id,
                        'porciento': porciento_id,
                        'nombre': nombre,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags,
                        'imagen': imagen,
                        'productos': productos,
                        'estado': estado,
                        'fechainicio': fechainicio,
                        'fechafin': fechafin
                    },
                    success: function (response) {
                        mApp.unblock('#form-promocion');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-promocion');

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
                if (porciento_id == "") {

                    var $element = $('#select-porciento .select2');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
                if (productos.length == 0) {
                    toastr.error("Debe agregar los productos de la promoción", "Error !!!");
                }
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-promocion");
        $(document).on('click', ".cerrar-form-promocion", function (e) {
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
        $(document).off('click', "#promocion-table-editable a.edit");
        $(document).on('click', "#promocion-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var promocion_id = $(this).data('id');
            $('#promocion_id').val(promocion_id);

            $('#form-promocion').removeClass('m--hide');
            $('#lista-promocion').addClass('m--hide');

            editRow(promocion_id);
        });

        function editRow(promocion_id) {

            MyApp.block('#promocion-form');

            $.ajax({
                type: "POST",
                url: "promocion/cargarDatos",
                dataType: "json",
                data: {
                    'promocion_id': promocion_id
                },
                success: function (response) {
                    mApp.unblock('#promocion-form');
                    if (response.success) {
                        //Datos promocion    

                        var formTitle = "¿Deseas actualizar la promocion \"" + response.promocion.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-promocion-title').html(formTitle);

                        $('#nombre').val(response.promocion.nombre);
                        $('#titulo').val(response.promocion.titulo);
                        $('#descripcion').val(response.promocion.descripcion);

                        $('#fechainicio').val(response.promocion.fechainicio);
                        $('#fechafin').val(response.promocion.fechafin);

                        if (!response.promocion.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }

                        var tags = response.promocion.tags;
                        if (tags != "" && tags != null) {
                            tags = tags.split(',');
                            for (var i = 0; i < tags.length; i++)
                                $('#tags').addTag(tags[i]);
                        }

                        $('#porciento').val(response.promocion.porciento);
                        $('#porciento').trigger('change');

                        mostrarImagen(response.promocion.imagen[0], response.promocion.imagen[1], response.promocion.imagen[2]);

                        productos = response.promocion.productos;
                        actualizarTableListaProductos();

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#promocion-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#promocion-table-editable a.delete");
        $(document).on('click', "#promocion-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-promocion");
        $(document).on('click', "#btn-eliminar-promocion", function (e) {
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
            var promocion_id = rowDelete;

            MyApp.block('#promocion-table-editable');

            $.ajax({
                type: "POST",
                url: "promocion/eliminarPromocion",
                dataType: "json",
                data: {
                    'promocion_id': promocion_id
                },
                success: function (response) {
                    mApp.unblock('#promocion-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#promocion-table-editable');

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

            MyApp.block('#promocion-table-editable');

            $.ajax({
                type: "POST",
                url: "promocion/eliminarPromociones",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#promocion-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#promocion-table-editable');
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
            //nombre = titulo por defecto
            if ($(this).attr('id') == 'nombre') {
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
        $('#form-promocion').addClass('m--hide');
        $('#lista-promocion').removeClass('m--hide');
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
            url: "promocion/salvarImagen",
            accept: function (file, done) {
                realFiles++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    if (maxFiles < realFiles) {
                        toastr.error('Solo se permite una imagen principal por promocion', "Error !!!");
                        //Eliminar
                        eliminarImagen(file);

                    }
                    else {
                        done();
                    }
                }
            },
            success: function (file, response) {

                if (file.width != '350' || file.height != '150') {
                    toastr.error('Las dimensiones de la imagen deben ser 350 x 150', "Error !!!");
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
                    url: "promocion/eliminarImagen",
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

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-promocion');
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
        $(document).off('click', "#form-promocion .wizard-tab");
        $(document).on('click', "#form-promocion .wizard-tab", function (e) {
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

                //bug visual de la tabla que muestra las cols corridas
                actualizarTableListaProductos();
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
                case 3:
                    $('#tab-productos').tab('show');
                    //bug visual de la tabla que muestra las cols corridas
                    actualizarTableListaProductos();
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

            var porciento_id = $('#porciento').val();

            if (!$('#promocion-form').valid() || imagen == "" || porciento_id == "") {
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
                if (porciento_id == "") {
                    var $element = $('#select-porciento .select2');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
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

        $('#filtro-marca').select2();
        $('#porciento').select2();
    };


    //Productos
    var oTableProductos;
    var productos = [];

    var initTableProductos = function () {
        MyApp.block('#productos-table-editable');

        var table = $('#productos-table-editable');

        var aoColumns = [
            {
                field: "nombre",
                title: "Nombre",
                width: 200,
                template: function (row) {

                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-promocion__pic">\
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
                field: "categoria",
                title: "Categoría",
                responsive: {visible: 'lg'},
            },
            {
                field: "marca",
                title: "Marca",
                responsive: {visible: 'lg'},
            },
            {
                field: "precio",
                title: "Precio",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center'
            },
            {
                field: "fechapublicacion",
                title: "Fecha de Publicación",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "views",
                title: "Visitas",
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
                width: 80,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center'
            }
        ];
        oTableProductos = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'producto/listarParaPromocion',
                    }
                },
                pageSize: 10,
                saveState: {
                    cookie: false,
                    webstorage: false
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            // layout definition
            layout: {
                theme: 'default', // datatable theme
                class: '', // custom wrapper class
                scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                height: 310, // datatable's body's fixed height
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
        oTableProductos
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#productos-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#productos-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#productos-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#productos-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#productos-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableProductos.getDataSourceQuery();
        $('#lista-relacionado .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableProductos.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            var categoria_id = $('#filtro-categoria').val();
            query.categoria_id = categoria_id;

            var marca_id = $('#filtro-marca').val();
            query.marca_id = marca_id;

            var productos_id = devolverProductosId();
            query.productos_id = productos_id;

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableProductos.setDataSourceQuery(query);
            oTableProductos.load();
        }).val(query.generalSearch);

    };
    var btnClickFiltrarProductos = function () {

        var generalSearch = $('#lista-relacionado .m_form_search').val();

        var query = oTableProductos.getDataSourceQuery();

        query.generalSearch = generalSearch;

        var categoria_id = $('#filtro-categoria').val();
        query.categoria_id = categoria_id;

        var marca_id = $('#filtro-marca').val();
        query.marca_id = marca_id;

        var productos_id = devolverProductosId();
        query.productos_id = productos_id;

        oTableProductos.setDataSourceQuery(query);
        oTableProductos.load();

    };
    var devolverProductosId = function () {
        var lista = [];
        for (var i = 0; i < productos.length; i++) {
            lista.push(productos[i].producto_id);
        }

        return lista;
    }

    var ExisteProducto = function (id) {
        var result = false;
        for (var i = 0; i < productos.length; i++) {
            if (productos[i].producto_id == id) {
                result = true;
                break;
            }
        }
        return result;
    };
    var initAccionesProductos = function () {

        $(document).off('click', "#btn-agregar-productos");
        $(document).on('click', "#btn-agregar-productos", function (e) {
            $('#modal-productos').modal({
                'show': true
            });
            btnClickFiltrarProductos();
        });
        $(document).off('click', "#btn-filtrar-productos");
        $(document).on('click', "#btn-filtrar-productos", function (e) {
            btnClickFiltrarProductos();
        });

        $(document).off('click', "#productos-table-editable a.add");
        $(document).on('click', "#productos-table-editable a.add", function (e) {
            e.preventDefault();

            var producto_id = $(this).data('id');

            var nombre = $(this).data('nombre');
            var categoria = $(this).data('categoria');
            var marca = $(this).data('marca');
            var estado = $(this).data('estado');
            var imagen = $(this).data('imagen');
            var precio = $(this).data('precio');
            var fecha = $(this).data('fecha');
            var views = $(this).data('views');

            var posicion = productos.length;

            var existeRelacionado = ExisteProducto(producto_id);
            if (!existeRelacionado) {

                productos.push({
                    producto_id: producto_id,
                    nombre: nombre,
                    categoria: categoria,
                    marca: marca,
                    estado: estado,
                    imagen: imagen,
                    precio: precio,
                    fecha: fecha,
                    views: views,
                    posicion: posicion
                });
                //actualizar lista
                actualizarTableListaProductos();

                btnClickFiltrarProductos();

                event_change = true;

            } else {
                toastr.error("Ya se ha agregado el producto seleccionado", "Error !!!");
            }
        });

        $(document).off('click', "#lista-productos-table-editable a.delete");
        $(document).on('click', "#lista-productos-table-editable a.delete", function (e) {

            e.preventDefault();
            var posicion = $(this).data('posicion');

            if (productos[posicion]) {

                var promocion_id = $('#promocion_id').val();

                if (promocion_id !== '') {

                    MyApp.block('#lista-productos-table-editable');

                    $.ajax({
                        type: "POST",
                        url: "promocion/eliminarProducto",
                        dataType: "json",
                        data: {
                            'producto_id': productos[posicion].producto_id
                        },
                        success: function (response) {
                            mApp.unblock('#lista-productos-table-editable');
                            if (response.success) {

                                toastr.success(response.message, "Exito !!!");
                                deleteProducto(posicion);
                            } else {
                                toastr.error(response.error, "Error !!!");
                            }
                        },
                        failure: function (response) {
                            mApp.unblock('#lista-productos-table-editable');
                            toastr.error(response.error, "Error !!!");
                        }
                    });
                } else {
                    deleteProducto(posicion);
                }
            }
        });

        function deleteProducto(posicion) {
            //Eliminar
            productos.splice(posicion, 1);
            //actualizar posiciones
            for (var i = 0; i < productos.length; i++) {
                productos[i].posicion = i;
            }
            //actualizar lista
            actualizarTableListaProductos();
        }
    };

    var oTableListaProductos;
    var initTableListaProductos = function () {
        MyApp.block('#lista-productos-table-editable');

        var table = $('#lista-productos-table-editable');

        var aoColumns = [
            {
                field: "nombre",
                title: "Nombre",
                width: 200,
                template: function (row) {

                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-promocion__pic">\
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
                field: "categoria",
                title: "Categoría",
                responsive: {visible: 'lg'},
            },
            {
                field: "marca",
                title: "Marca",
                responsive: {visible: 'lg'},
            },
            {
                field: "precio",
                title: "Precio",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center',
                template: function (row) {
                    return MyApp.formatearNumero(row.precio, 0, ',', '.')
                }
            },
            {
                field: "fecha",
                title: "Fecha de Publicación",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "views",
                title: "Visitas",
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
                field: "posicion",
                width: 80,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center',
                template: function (row) {
                    return '<a href="javascript:;" data-posicion="' + row.posicion + '" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar relacionado"><i class="la la-trash"></i></a>';
                }
            }
        ];
        oTableListaProductos = table.mDatatable({
            // datasource definition
            data: {
                type: 'local',
                source: productos,
                pageSize: 10,
                saveState: {
                    cookie: true,
                    webstorage: true
                }
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
        oTableListaProductos
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#lista-productos-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#lista-productos-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#lista-productos-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#lista-productos-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#lista-productos-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableListaProductos.getDataSourceQuery();
        $('#lista-productos .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableListaProductos.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableListaProductos.setDataSourceQuery(query);

            oTableListaProductos.search(query.generalSearch);

        }).val(query.generalSearch);

    };
    var actualizarTableListaProductos = function () {
        oTableListaProductos.destroy();
        initTableListaProductos();
    }

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initForm();

            initSelects();
            initDropZone();

            initTagsInput();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();

            initAccionChange();

            initPortlets();

            initWizard();

            //Productos
            initTableProductos();
            initTableListaProductos();
            initAccionesProductos();
        }

    };

}();