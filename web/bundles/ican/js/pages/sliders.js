var Sliders = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#slider-table-editable');

        var table = $('#slider-table-editable');

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
            },
            {
                field: "fechapublicacion",
                title: "Fecha de Publicación",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "posicion",
                title: "Orden",
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
                        url: 'slider/listarSlider',
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
                mApp.unblock('#slider-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#slider-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#slider-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#slider-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#slider-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-slider .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTable.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();
            // shortcode to datatable.setDataSourceParam('query', query);
            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };
    //Reset forms
    var resetForms = function () {
        $('#slider-form input').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        var fecha_actual = new Date();
        $('#fecha').val(fecha_actual.format("d/m/Y H:i"));

        $('#estadoactivo').prop('checked', true);
        $('#formadeabrirself').prop('checked', true);

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        //Dropzone
        if ($('#my-dropzone').hasClass('dz-started')) {
            $('#my-dropzone').removeClass('dz-started');
        }
        $('.dz-preview').remove();
        realFiles = 0;

        var $element = $('#processing-dropzone');
        $element.tooltip("dispose")
            .removeClass('has-error')
            .closest('.form-group')
            .removeClass('has-error');

        event_change = false;
    };

    //Validacion
    var initForm = function () {
        $("#slider-form").validate({
            rules: {
                nombre: {
                    required: true
                },
                fecha: {
                    required: true
                },
                titulo: {
                    maxlength: 150
                },
                descripcion: {
                    maxlength: 150
                },
                url: {
                    url: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio",
                fecha: "Este campo es obligatorio",
                url: "Por favor, introduzca una url válida",
                titulo: {
                    maxlength: "Por favor, no escribas más de {0} caracteres"
                },
                descripcion: "Por favor, no escribas más de {0} caracteres"
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
        $(document).off('click', "#btn-nuevo-slider");
        $(document).on('click', "#btn-nuevo-slider", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear un nuevo slider? Sigue los siguientes pasos:";
            $('#form-slider-title').html(formTitle);
            $('#form-slider').removeClass('m--hide');
            $('#lista-slider').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-slider");
        $(document).on('click', "#btn-salvar-slider", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            var slider_id = $('#slider_id').val();

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            if ($('#slider-form').valid() && imagen != "") {

                var nombre = $('#nombre').val();
                var titulo = $('#titulo').val();
                var descripcion = $('#descripcion').val();
                var url = $('#url').val();
                var formadeabrir = ($('#formadeabrirself').prop('checked')) ? 'Self' : 'Blank';
                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;
                var fecha = $('#fecha').val();

                MyApp.block('#form-slider');

                $.ajax({
                    type: "POST",
                    url: "slider/salvarSlider",
                    dataType: "json",
                    data: {
                        'slider_id': slider_id,
                        'nombre': nombre,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'url': url,
                        'formadeabrir': formadeabrir,
                        'estado': estado,
                        'fecha': fecha,
                        'imagen': imagen
                    },
                    success: function (response) {
                        mApp.unblock('#form-slider');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-slider');

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
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-slider");
        $(document).on('click', ".cerrar-form-slider", function (e) {
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
        $(document).off('click', "#slider-table-editable a.edit");
        $(document).on('click', "#slider-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var slider_id = $(this).data('id');
            $('#slider_id').val(slider_id);

            $('#form-slider').removeClass('m--hide');
            $('#lista-slider').addClass('m--hide');

            editRow(slider_id);
        });

        function editRow(slider_id) {

            MyApp.block('#slider-form');

            $.ajax({
                type: "POST",
                url: "slider/cargarDatos",
                dataType: "json",
                data: {
                    'slider_id': slider_id
                },
                success: function (response) {
                    mApp.unblock('#slider-form');
                    if (response.success) {
                        //Datos slider    

                        var formTitle = "¿Deseas actualizar el slider \"" + response.slider.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-slider-title').html(formTitle);

                        $('#nombre').val(response.slider.nombre);
                        $('#titulo').val(response.slider.titulo);
                        $('#descripcion').val(response.slider.descripcion)
                        $('#url').val(response.slider.url);
                        $('#fecha').val(response.slider.fecha);

                        if (!response.slider.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }
                        if (response.slider.formadeabrir == "Blank") {
                            $('#formadeabrirself').prop('checked', false);
                            $('#formadeabrirblank').prop('checked', true);
                        }

                        mostrarImagen(response.slider.imagen[0], response.slider.imagen[1], response.slider.imagen[2]);

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#slider-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Cambiar posicion
    var initAccionCambiarPosicion = function () {

        $(document).off('click', "#slider-table-editable a.subir");
        $(document).on('click', "#slider-table-editable a.subir", function (e) {

            var slider_id = $(this).data('id');
            cambiarPosicionSlider(slider_id, 'subir');
        });

        $(document).off('click', "#slider-table-editable a.bajar");
        $(document).on('click', "#slider-table-editable a.bajar", function (e) {

            var slider_id = $(this).data('id');
            cambiarPosicionSlider(slider_id, 'bajar');
        });

        function cambiarPosicionSlider(slider_id, direccion) {

            MyApp.block('#slider-table-editable');

            $.ajax({
                type: "POST",
                url: "slider/cambiarPosicion",
                dataType: "json",
                data: {
                    'slider_id': slider_id,
                    'direccion': direccion
                },
                success: function (response) {
                    mApp.unblock('#slider-table-editable');

                    if (response.success) {
                        oTable.load();
                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#slider-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#slider-table-editable a.delete");
        $(document).on('click', "#slider-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-slider");
        $(document).on('click', "#btn-eliminar-slider", function (e) {
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
            var slider_id = rowDelete;

            MyApp.block('#slider-table-editable');

            $.ajax({
                type: "POST",
                url: "slider/eliminarSlider",
                dataType: "json",
                data: {
                    'slider_id': slider_id
                },
                success: function (response) {
                    mApp.unblock('#slider-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#slider-table-editable');

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

            MyApp.block('#slider-table-editable');

            $.ajax({
                type: "POST",
                url: "slider/eliminarSliders",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#slider-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#slider-table-editable');
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
        $('#form-slider').addClass('m--hide');
        $('#lista-slider').removeClass('m--hide');
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
            url: "slider/salvarImagen",
            accept: function (file, done) {
                realFiles++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    if (maxFiles < realFiles) {
                        toastr.error('Solo se permite una imagen por slider', "Error !!!");
                        //Eliminar
                        eliminarImagen(file);

                    }
                    else {
                        done();
                    }
                }
            },
            success: function (file, response) {

                if (file.width != '1922' || file.height != '564') {
                    toastr.error('Las dimensiones de la imagen deben ser 1922 x 564', "Error !!!");
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
                    url: "slider/eliminarImagen",
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
            $('.dz-preview').addClass('dz-success');
        }
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-slider');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initForm();
            initDropZone();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionCambiarPosicion();
            initAccionEliminar();

            initAccionChange();

            initPortlets();
        }

    };

}();